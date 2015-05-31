<?php

namespace Gos\Bundle\NotificationBundle\Server;

use Gos\Bundle\NotificationBundle\Event\NotificationEvents;
use Gos\Bundle\NotificationBundle\Event\NotificationPublishedEvent;
use Gos\Bundle\NotificationBundle\Model\Message\Message;
use Gos\Bundle\NotificationBundle\Model\Message\PatternMessage;
use Gos\Bundle\NotificationBundle\Processor\ProcessorInterface;
use Gos\Bundle\NotificationBundle\Pusher\ProcessorDelegate;
use Gos\Bundle\NotificationBundle\Pusher\PusherInterface;
use Gos\Bundle\NotificationBundle\Pusher\PusherLoopAwareInterface;
use Gos\Bundle\NotificationBundle\Pusher\PusherRegistry;
use Gos\Bundle\NotificationBundle\Router\Dumper\RedisDumper;
use Gos\Bundle\NotificationBundle\Serializer\NotificationContextSerializerInterface;
use Gos\Bundle\NotificationBundle\Serializer\NotificationSerializerInterface;
use Gos\Bundle\PubSubRouterBundle\Request\PubSubRequest;
use Gos\Bundle\PubSubRouterBundle\Router\RouterInterface;
use Gos\Bundle\WebSocketBundle\Event\Events;
use Gos\Bundle\WebSocketBundle\Event\ServerEvent;
use Gos\Bundle\WebSocketBundle\Server\Type\ServerInterface;
use Predis\Async\Client;
use Predis\Async\PubSub\PubSubContext;
use Predis\ResponseError;
use Psr\Log\LoggerInterface;
use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Log\NullLogger;

/**
 * @author Johann Saunier <johann_27@hotmail.fr>
 */
class PubSubServer implements ServerInterface
{
    /** @var  LoopInterface */
    protected $loop;

    /** @var  Client */
    protected $client;

    /** @var LoggerInterface */
    protected $logger;

    /** @var  EventDispatcherInterface */
    protected $eventDispatcher;

    /** @var NotificationSerializerInterface */
    protected $notificationSerializer;

    /** @var NotificationContextSerializerInterface */
    protected $contextSerializer;

    /** @var PusherRegistry */
    protected $pusherRegistry;

    /** @var array */
    protected $pubSubConfig;

    /** @var RouterInterface */
    protected $router;

    /** @var RedisDumper */
    protected $redisDumper;

    /** @var  ContainerInterface */
    protected $container;

    /**
     * @param EventDispatcherInterface               $eventDispatcher
     * @param NotificationSerializerInterface        $notificationSerializer
     * @param NotificationContextSerializerInterface $contextSerializer
     * @param PusherRegistry                         $pusherRegistry
     * @param array                                  $pubSubConfig
     * @param RouterInterface                        $router
     * @param LoggerInterface                        $logger
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        NotificationSerializerInterface $notificationSerializer,
        NotificationContextSerializerInterface $contextSerializer,
        PusherRegistry $pusherRegistry,
        Array $pubSubConfig,
        RouterInterface $router,
        RedisDumper $redisDumper,
        ContainerInterface $container,
        LoggerInterface $logger = null
    ) {
        $this->logger = $logger;
        $this->eventDispatcher = $eventDispatcher;
        $this->notificationSerializer = $notificationSerializer;
        $this->contextSerializer = $contextSerializer;
        $this->pusherRegistry = $pusherRegistry;
        $this->pubSubConfig = $pubSubConfig;
        $this->redisDumper = $redisDumper;
        $this->router = $router;
        $this->container = $container;
        $this->logger = null === $logger ? new NullLogger() : $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function launch()
    {
        if (null !== $this->logger) {
            $this->logger->info('Starting redis pubsub');
        }

        $this->loop = Factory::create();
        $this->client = new Client('tcp://' . $this->getAddress(), $this->loop);

        $this->client->connect(function () {
            $subscription = $this->redisDumper->dump();

            if (!empty($subscription['subscribe'])) {
                $this->logger->info(sprintf(
                    'Listening topics %s',
                    implode(', ', $subscription['subscribe'])
                ));
            }

            if (!empty($subscription['psubscribe'])) {
                $this->logger->info(sprintf(
                    'Listening pattern %s',
                    implode(', ', $subscription['psubscribe'])
                ));
            }

            /* @var PubSubContext $pubSubContext */
            $this->client->pubSub($subscription, function ($event) {

                if ($event instanceof ResponseError) {
                    if (null !== $this->logger) {
                        $this->logger->error($event->getMessage());
                    }

                    return;
                }

                if (!in_array($event->kind, array(PubSubContext::MESSAGE, PubSubContext::PMESSAGE))) {
                    return;
                }

                if ($event->kind === PubSubContext::MESSAGE) {
                    $message = new Message(
                        $event->kind,
                        $event->channel,
                        $event->payload
                    );
                }

                if ($event->kind === PubSubContext::PMESSAGE) {
                    $message = new PatternMessage(
                        $event->kind,
                        $event->pattern,
                        $event->channel,
                        $event->payload
                    );
                }

                $decodedMessage = json_decode($message->getPayload(), true);

                $notification = $this->notificationSerializer->deserialize(json_encode($decodedMessage['notification']));

                if (isset($decodedMessage['context'])) {
                    $context = $this->contextSerializer->deserialize(json_encode($decodedMessage['context']));
                } else {
                    $context = null;
                }

                if (null !== $this->logger) {
                    $this->logger->info('process notification');
                }

                $matched = $this->router->match($message->getChannel());
                $request = new PubSubRequest($matched[0], $matched[1], $matched[2]);

                $this->logger->info(sprintf(
                    'Route %s matched with [%s]',
                    $request->getRouteName(),
                    implode(', ', array_map(function ($v, $k) { return sprintf("%s='%s'", $k, $v); }, $request->getAttributes()->all(), array_keys($request->getAttributes()->all())))
                ));

                $route = $request->getRoute();
                $pushers = $this->pusherRegistry->getPushers($route->getCallback());

                $this->eventDispatcher->dispatch(NotificationEvents::NOTIFICATION_PUBLISHED, new NotificationPublishedEvent($message, $notification, $context, $request));

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

                    $pusher->push($message, $notification, $request, $context);
                }

                $this->logger->info('notification processed');
            });

            /* Server Event Loop to add other services in the same loop. */
            $event = new ServerEvent($this->loop, $this->getAddress(), $this->getName());
            $this->eventDispatcher->dispatch(Events::SERVER_LAUNCHED, $event);

            $this->logger->info(sprintf(
                'Launching %s on %s',
                $this->getName(),
                $this->getAddress()
            ));
        });

        $this->loop->run();
    }

    /**
     * {@inheritdoc}
     */
    public function getAddress()
    {
        return $this->pubSubConfig['host'] . ':' . $this->pubSubConfig['port'];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'PubSub';
    }
}
