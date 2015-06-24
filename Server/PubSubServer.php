<?php

namespace Gos\Bundle\NotificationBundle\Server;

use Evenement\EventEmitter;
use Gos\Bundle\NotificationBundle\Exception\NotificationServerException;
use Gos\Bundle\NotificationBundle\Model\Message\Message;
use Gos\Bundle\NotificationBundle\Model\Message\PatternMessage;
use Gos\Bundle\NotificationBundle\Router\Dumper\RedisDumper;
use Gos\Bundle\WebSocketBundle\Server\Type\ServerInterface;
use Gos\Component\PnctlEventLoopEmitter\PnctlEmitter;
use Predis\Async\Client;
use Predis\Async\PubSub\PubSubContext;
use Predis\ResponseError;
use Psr\Log\LoggerInterface;
use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
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

    /** @var array */
    protected $pubSubConfig;

    /** @var RedisDumper */
    protected $redisDumper;

    /** @var  PubSubContext */
    protected $pubSub;

    /** @var ServerNotificationProcessorInterface  */
    protected $processor;

    /** @var  bool */
    protected $debug;

    /**
     * @param EventDispatcherInterface             $eventDispatcher
     * @param array                                $pubSubConfig
     * @param RedisDumper                          $redisDumper
     * @param ServerNotificationProcessorInterface $processor
     * @param bool                                 $debug
     * @param LoggerInterface                      $logger
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        Array $pubSubConfig,
        RedisDumper $redisDumper,
        ServerNotificationProcessorInterface $processor,
        $debug,
        LoggerInterface $logger = null
    ) {
        $this->logger = $logger;
        $this->eventDispatcher = $eventDispatcher;
        $this->pubSubConfig = $pubSubConfig;
        $this->redisDumper = $redisDumper;
        $this->processor = $processor;
        $this->debug = $debug;
        $this->logger = null === $logger ? new NullLogger() : $logger;
    }

    /**
     * @return array
     */
    protected function getSubscriptions()
    {
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

        return $subscription;
    }

    /**
     * {@inheritdoc}
     */
    public function launch($profile)
    {
        $this->logger->info('Starting redis pubsub');

        $this->loop = Factory::create();

        if (extension_loaded('pcntl')) {
            $this->handlePnctlEvent();
        }

        $this->client = new Client('tcp://' . $this->getAddress(), $this->loop);

        $dispatcher = new EventEmitter();
        $dispatcher->on('notification', $this->processor);

        if (true === $profile) {
            $this->loop->addPeriodicTimer(5, function () {
                $this->logger->info('Memory usage : ' . round((memory_get_usage() / (1024 * 1024)), 4) . 'Mo');
            });
        }

        $subscriptions = $this->getSubscriptions();

        $this->client->connect(function () use ($dispatcher, $subscriptions) {

            $this->pubSub = $this->client->pubSub($subscriptions, function ($event) use ($dispatcher) {
                if ($event instanceof ResponseError) {
                    throw new \Exception($event);
                }

                if (!in_array($event->kind, array(PubSubContext::MESSAGE, PubSubContext::PMESSAGE))) {
                    throw new NotificationServerException(sprintf(
                        'Unsupported message type %s given, supported [%]',
                        $event->kind,
                        [PubSubContext::MESSAGE, PubSubContext::PMESSAGE, PubSubContext::PSUBSCRIBE, PubSubContext::SUBSCRIBE, PubSubContext::UNSUBSCRIBE]
                    ));
                }

                if (in_array($event->kind, [PubSubContext::MESSAGE, PubSubContext::PMESSAGE])) {
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

                    $dispatcher->emit('notification', [$message]);
                }
            });
        });

        $this->logger->info(sprintf(
            'Launching %s on %s',
            $this->getName(),
            $this->getAddress()
        ));

        $this->loop->run();
    }

    protected function handlePnctlEvent()
    {
        $pnctlEmitter = new PnctlEmitter($this->loop);

        $pnctlEmitter->on(SIGTERM, function () {
            $this->logger->notice('Stopping server ...');

            if (null !== $this->pubSub) {
                $this->pubSub->quit();
            }

            $this->client->getConnection()->disconnect();
            $this->loop->stop();
            $this->logger->notice('Server stopped !');
        });

        $pnctlEmitter->on(SIGINT, function () {
            $this->logger->notice('Press CTLR+C again to stop the server');

            if (SIGINT === pcntl_sigtimedwait([SIGINT], $siginfo, 5)) {
                $this->logger->notice('Stopping server ...');

                if (null !== $this->pubSub) {
                    $this->pubSub->quit();
                }

                $this->client->getConnection()->disconnect();
                $this->loop->stop();

                $this->logger->notice('Server stopped !');
            } else {
                $this->logger->notice('CTLR+C not pressed, continue to run normally');
            }
        });
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
