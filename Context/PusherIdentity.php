<?php

namespace Gos\Bundle\NotificationBundle\Context;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Johann Saunier <johann_27@hotmail.fr>
 */
class PusherIdentity
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $identifier;

    /**
     * @param string $type
     * @param string $identifier
     */
    public function __construct($type, $identifier)
    {
        $this->type = $type;
        $this->identifier = $identifier;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getIdentity();
    }

    /**
     * @param UserInterface $user
     *
     * @return PusherIdentity|$this
     */
    public static function fromAccount(UserInterface $user)
    {
        return new PusherIdentity('user', $user->getUsername());
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @return string
     */
    public function getIdentity()
    {
        return $this->type . '#' . $this->identifier;
    }
}
