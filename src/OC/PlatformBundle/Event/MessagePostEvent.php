<?php

namespace OC\PlatformBundle\Event;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\EventDispatcher\Event;

class MessagePostEvent extends Event{
    
    protected $message;
    protected $user;
    
    public function __construct($message, UserInterface $user) {
        $this->message = $message;
        $this->user = $user;
    }
    
    public function getMessage() {
        return $this->message;
    }

    public function setMessage($message) {
        $this->message = $message;
    }
    
    function getUser() {
        return $this->user;
    }
    
}
