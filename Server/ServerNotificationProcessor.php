<?php

namespace Gos\Bundle\NotificationBundle\Server;

use Gos\Bundle\NotificationBundle\Context\NotificationContextInterface;
use Gos\Bundle\NotificationBundle\Context\NullContext;
use Gos\Bundle\NotificationBundle\Event\NotificationEvents;
use Gos\Bundle\NotificationBundle\Event\NotificationPublishedEvent;
use Gos\Bundle\NotificationBundle\Model\Message\MessageInterface;
use Gos\Bundle\NotificationBundle\Model\NotificationInterface;
use Gos\Bundle\NotificationBundle\Processor\ProcessorInterface;
use Gos\Bundle\NotificationBundle\Pusher\ProcessorDelegate;
use Gos\Bundle\NotificationBundle\Pusher\PusherInterface;
use Gos\Bundle\NotificationBundle\Pusher\PusherLoopAwareInterface;
use Gos\Bundle\NotificationBundle\Pusher\PusherRegistry;
use Gos\Bundle\NotificationBundle\Serializer\NotificationContextSerializerInterface;
use Gos\Bundle\NotificationBundle\Serializer\NotificationSerializerInterface;
use Gos\Bundle\PubSubRouterBundle\Matcher\MatcherInterface;
use Gos\Bundle\PubSubRouterBundle\Request\PubSubRequest;
use Gos\Bundle\PubSubRouterBundle\Router\RouteInterface;
use Gos\Component\Yolo\Yolo;
use Gos\Component\Yolo\YoloInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use React\EventLoop\LoopInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Dump\Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ServerNotificationProcessor implements ServerNotificationProcessorInterface
{
    /** @var LoggerInterface */
    protected $logger;

    /** @var  NotificationSerializerInterface */
    protected $notificationSerializer;

    /** @var  NotificationContextSerializerInterface */
    protected $contextSerializer;

    /** @var  MatcherInterface */
    protected $matcher;

    /** @var  PusherRegistry */
    protected $pusherRegistry;

    /** @var  EventDispatcherInterface */
    protected $eventDispatcher;

    /** @var  ContainerInterface */
    protected $container;

    /** @var  LoopInterface */
    protected $loop;

    /**
     * @param NotificationSerializerInterface        $notificationSerializer
     * @param NotificationContextSerializerInterface $contextSerializer
     * @param MatcherInterface                       $matcher
     * @param PusherRegistry                         $pusherRegistry
     * @param LoggerInterface                        $logger
     */
    public function __construct(
        NotificationSerializerInterface $notificationSerializer,
        NotificationContextSerializerInterface $contextSerializer,
        MatcherInterface $matcher,
        PusherRegistry $pusherRegistry,
        EventDispatcherInterface $eventDispatcher,
        ContainerInterface $container,
        LoggerInterface $logger = null
    ) {
        $this->notificationSerializer = $notificationSerializer;
        $this->contextSerializer = $contextSerializer;
        $this->matcher = $matcher;
        $this->pusherRegistry = $pusherRegistry;
        $this->container = $container;
        $this->eventDispatcher = $eventDispatcher;
        $this->logger = null === $logger ? new NullLogger() : $logger;
    }

    /**
     * @param LoopInterface $loop
     */
    public function setLoop(LoopInterface $loop)
    {
        $this->loop = $loop;
    }

    /**
     * @param MessageInterface $message
     *
     * @return array
     */
    public function getNotification(MessageInterface $message)
    {
        $decodedMessage = json_decode($message->getPayload(), true);

        $notification = $this->notificationSerializer->deserialize(json_encode($decodedMessage['notification']));

        if (isset($decodedMessage['context'])) {
            $context = $this->contextSerializer->deserialize(json_encode($decodedMessage['context']));
        } else {
            $context = new NullContext();
        }

        $this->logger->info('processing notification');

        return [$notification, $context];
    }

    /**
     * @param MessageInterface $message
     *
     * @return PubSubRequest
     */
    public function getRequest(MessageInterface $message)
    {
        $matched = $this->matcher->match($message->getChannel());
        $request = new PubSubRequest($matched[0], $matched[1], $matched[2]);

        $this->logger->info(sprintf(
            'Route %s matched with [%s]',
            $request->getRouteName(),
            implode(', ', array_map(function ($v, $k) { return sprintf("%s='%s'", $k, $v); }, $request->getAttributes()->all(), array_keys($request->getAttributes()->all())))
        ));

        return $request;
    }

    /**
     * @param PusherInterface[]            $pushers
     * @param RouteInterface               $route
     * @param PubSubRequest                $request
     * @param MessageInterface             $message
     * @param NotificationInterface        $notification
     * @param NotificationContextInterface $context
     *
     * @throws \Exception
     */
    public function push(
        $pushers,
        RouteInterface $route,
        PubSubRequest $request,
        MessageInterface $message,
        NotificationInterface $notification,
        NotificationContextInterface $context
    ) {
        /** @var PusherInterface $pusher */
        foreach ($pushers as $pusher) {
            if ($pusher instanceof ProcessorDelegate && count($route->getArgs()) >= 1) {
                $args = $route->getArgs();

                foreach ($args as $attributeName => $processorService) {
                    if (!in_array($attributeName, $availableAttributes = array_keys($request->getAttributes()->all()))) {
                        throw new \Exception(sprintf(
                            'Undefined attribute %s, available are [%s]',
                            $attributeName,
                            $availableAttributes
                        ));
                    }

                    if ('@' !== $processorService{0}) {
                        throw new \Exception(sprintf(
                            'Your processor service must start with "@"'
                        ));
                    }

                    $processor = $this->container->get(ltrim($processorService, '@'));

                    if (!$processor instanceof ProcessorInterface) {
                        throw new \Exception('Processor class must implement ProcessorInterface !');
                    }

                    call_user_func([$pusher, 'addProcessor'], $attributeName, $processor);
                }
            }

            if ($pusher instanceof PusherLoopAwareInterface) {
                $pusher->setLoop($this->loop);
            }

            if ($pusher instanceof YoloInterface) {
                $yoloPush = new Yolo([$pusher, 'push'], [$message, $notification, $request, $context], null, 10);
                $yoloPush->setLogger($this->logger);
                $yoloPush->tryUntil($pusher);
            } else {
                $pusher->push($message, $notification, $request, $context);
            }
        }
    }

    /**
     * @param MessageInterface $message
     *
     * @throws \Exception
     */
    public function __invoke(MessageInterface $message)
    {
        /*
         * @var NotificationInterface
         * @var NotificationContextInterface
         */
        list($notification, $context) = $this->getNotification($message);
        $request = $this->getRequest($message);
        $route = $request->getRoute();
        $pushers = $this->pusherRegistry->getPushers($route->getCallback());

        $this->eventDispatcher->dispatch(
            NotificationEvents::NOTIFICATION_PUBLISHED,
            new NotificationPublishedEvent($message, $notification, $context, $request)
        );

        $this->push($pushers, $route, $request, $message, $notification, $context);

        $this->logger->info(sprintf(
            'Notification %s processed',
            $notification->getUuid()
        ));
    }
}
