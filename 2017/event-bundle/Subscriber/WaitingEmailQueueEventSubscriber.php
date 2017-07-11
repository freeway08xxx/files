<?php

namespace Photocreate\EventBundle\Subscriber;

use Photocreate\EventBundle\Event\PostWaitingEmailQueueEvent;
use Photocreate\MailerBundle\Usecase\SendRegisterWaitingEmailQueueMailUsecase;
use Photocreate\OrderBundle\Exception\FailedEventException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class WaitingEmailQueueEventSubscriber
 *
 * @package Photocreate\OrderBundle\Subscriber
 */
class WaitingEmailQueueEventSubscriber implements EventSubscriberInterface
{
    /** @var   */
    protected $sendRegisterWaitingEmailQueueMailUsecase;

    /**
     * WaitingEmailQueueEventSubscriber constructor.
     *
     * @param SendRegisterWaitingEmailQueueMailUsecase $sendRegisterWaitingEmailQueueMailUsecase
     */
    public function __construct(
        SendRegisterWaitingEmailQueueMailUsecase $sendRegisterWaitingEmailQueueMailUsecase
    )
    {
        $this->sendRegisterWaitingEmailQueueMailUsecase = $sendRegisterWaitingEmailQueueMailUsecase;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            PostWaitingEmailQueueEvent::NAME => 'onPostWaitingEmailQueue',
        ];
    }

    /**
     * @param PostWaitingEmailQueueEvent $event
     */
    public function onPostWaitingEmailQueue(PostWaitingEmailQueueEvent $event)
    {
        try {
            // 公開通知メール登録完了メールの送信
            $emailQueue = $event->getEmailQueue();
            $this->sendRegisterWaitingEmailQueueMailUsecase->run($emailQueue);
        } catch (\Exception $e) {
            throw new FailedEventException(sprintf(
                'Failed subscribed event %s, exception "%s" occurred, message is "%s".', self::class, get_class($e), $e->getMessage()
            ));
        }

        $event->stopPropagation();
    }
}
