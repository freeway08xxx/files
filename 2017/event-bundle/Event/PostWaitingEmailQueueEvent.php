<?php

namespace Photocreate\EventBundle\Event;

use Photocreate\ResourceBundle\Entity\EmailQueue;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class PostWaitingEmailQueueEvent
 *
 * @package Photocreate\EventBundle\Event
 */
class PostWaitingEmailQueueEvent extends Event
{
    const NAME = 'photocreate_event.event.post_waiting_email_queue';

    /** @var EmailQueue  */
    protected $emailQueue;

    /**
     * PostWaitingEmailQueueEvent constructor.
     *
     * @param EmailQueue $emailQueue
     */
    public function __construct(EmailQueue $emailQueue)
    {
        $this->emailQueue = $emailQueue;
    }

    /**
     * @return EmailQueue
     */
    public function getEmailQueue()
    {
        return $this->emailQueue;
    }
}
