<?php

namespace Gos\Bundle\NotificationBundle\Pusher;

use Gos\Bundle\NotificationBundle\Context\NotificationContextInterface;
use Gos\Bundle\NotificationBundle\Event\NotificationEvents;
use Gos\Bundle\NotificationBundle\Event\NotificationPushedEvent;
use Gos\Bundle\NotificationBundle\Model\Message\MessageInterface;
use Gos\Bundle\NotificationBundle\Model\NotificationInterface;
use Gos\Bundle\NotificationBundle\Processor\ProcessorInterface;
use Gos\Bundle\PubSubRouterBundle\Request\PubSubRequest;
use Gos\Bundle\PubSubRouterBundle\Router\RouteInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

abstract class AbstractPusher implements PusherInterface, ProcessorDelegate
{
    use ProcessorTrait;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function push(MessageInterface $message, NotificationInterface $notification, PubSubRequest $request, NotificationContextInterface $context = null)
    {
        $route = $request->getRoute();
        $matrix = [];

        foreach ($request->getAttributes()->all() as $name => $value) {
            if (!isset($this->processors[$name])) {
                throw new \Exception(sprintf('Missing processor for %s on route "%s"', $name, $request->getRoute()));
            }

            /** @var ProcessorInterface $processor */
            $processor = $this->processors[$name];

            //attribute is wildcarded
            if (in_array($request->getAttributes()->get($name, false), ['*', 'all']) &&
                isset($route->getRequirements()[$name]['wildcard']) &&
                true === $route->getRequirements()[$name]['wildcard']
            ) {
                $matrix[$name] = $processor->process(true, $this->getAlias(), $notification, $request);
            } else { //he is not
                $matrix[$name] = $processor->process(false, $this->getAlias(), $notification, $request);
            }
        }

        $matrix = $this->fixMapIndex($route, $matrix);

        $this->doPush($message, $notification, $request, $matrix, $context);

        $this->eventDispatcher->dispatch(NotificationEvents::NOTIFICATION_PUSHED, new NotificationPushedEvent($message, $notification, $request, $context, $this));
    }

    /**
     * @param MessageInterface             $message
     * @param NotificationInterface        $notification
     * @param PubSubRequest                $request
     * @param array                        $matrix
     * @param NotificationContextInterface $context
     */
    protected function doPush(
        MessageInterface $message,
        NotificationInterface $notification,
        PubSubRequest $request,
        array $matrix,
        NotificationContextInterface $context = null
    ) {
        //override it !
    }

    /**
     * @param RouteInterface $route
     * @param array          $matrix
     *
     * @return array
     */
    protected function fixMapIndex(RouteInterface $route, Array &$matrix)
    {
        $pattern = implode('|', array_keys($route->getRequirements()));
        $matches = [];

        preg_match_all('#' . $pattern . '#', $route->getPattern(), $matches);

        uksort($matrix, function ($key) use ($matches) {
            foreach ($matches[0] as $order => $attributeName) {
                if ($attributeName === $key) {
                    return $order;
                }
            }
        });

        return $matrix;
    }

    /**
     * @param array  $data
     * @param array  $all
     * @param string $groupName
     * @param array  $group
     * @param null   $value
     * @param int    $i
     *
     * @see http://fr.wikipedia.org/wiki/Matrice_de_permutation
     *
     * @return array
     */
    protected function generateMatrixPermutations(
        array $data,
        array &$all = array(),
        $groupName = '',
        array $group = array(),
        $value = null,
        $i = 0
    ) {
        $keys = array_keys($data);

        if (isset($value) === true) {
            $group[$groupName] = $value;
        }

        if ($i >= count($data)) {
            array_push($all, $group);
        } else {
            $currentKey = $keys[$i];
            $currentElement = $data[$currentKey];

            if (is_array($currentElement)) {
                foreach ($currentElement as $groupName => $val) {
                    $this->generateMatrixPermutations($data, $all, $currentKey, $group, $val, $i + 1);
                }
            } else {
                $this->generateMatrixPermutations($data, $all, $currentKey, $group, $currentElement, $i + 1);
            }
        }

        return $all;
    }
}
