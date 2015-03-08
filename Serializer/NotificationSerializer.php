<?php

namespace Gos\Bundle\NotificationBundle\Serializer;

use Gos\Bundle\NotificationBundle\Model\NotificationInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;

class NotificationSerializer implements NotificationSerializerInterface
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
    protected $notificationClass;

    /**
     * @param string $notificationClass
     */
    public function __construct($notificationClass)
    {
        $this->notificationClass = $notificationClass;

        $dateCallback = function ($datetime) {
            return $datetime instanceof \DateTime
                ? $datetime->format(\DateTime::W3C)
                : null;
        };

        $normalizer = new GetSetMethodNormalizer();

        $normalizer->setCallbacks(array(
            'createdAt' => $dateCallback,
            'viewedAt' => $dateCallback,
        ));

        $this->normalizers = array($normalizer);
        $this->encoders = array(new JsonEncoder());

        $this->serializer = new Serializer($this->normalizers, $this->encoders);
    }

    /**
     * {@inheritdoc}
     */
    public function serialize(NotificationInterface $notification)
    {
        return $this->serializer->serialize($notification, 'json');
    }

    /**
     * {@inheritdoc}
     */
    public function deserialize($message)
    {
        return $this->serializer->deserialize($message, $this->notificationClass, 'json');
    }
}
