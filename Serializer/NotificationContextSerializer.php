<?php

namespace Gos\Bundle\NotificationBundle\Serializer;

use Gos\Bundle\NotificationBundle\Context\NotificationContextInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;

class NotificationContextSerializer implements NotificationContextSerializerInterface
{
    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * @var NormalizerInterface[]
     */
    protected $normalizers;

    /**
     * @var EncoderInterface[]
     */
    protected $encoders;

    /**
     * @var string
     */
    protected $notificationContextClass;

    /**
     * @param string $notificationContextClass
     */
    public function __construct($notificationContextClass)
    {
        $this->notificationContextClass = $notificationContextClass;

        $normalizer = new GetSetMethodNormalizer();

        $this->normalizers = array($normalizer);
        $this->encoders = array(new JsonEncoder());

        $this->serializer = new Serializer($this->normalizers, $this->encoders);
    }

    /**
     * {@inheritdoc}
     */
    public function serialize(NotificationContextInterface $context)
    {
        return $this->serializer->serialize($context, 'json');
    }

    /**
     * {@inheritdoc}
     */
    public function deserialize($message)
    {
        return $this->serializer->deserialize($message, $this->notificationContextClass, 'json');
    }
}
