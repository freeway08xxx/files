<?php

namespace Photocreate\OrderBundle\Subscriber;

use Photocreate\MailerBundle\Entity\OrderNum;
use Photocreate\MailerBundle\Usecase\SendOrderCompleteMailUsecase;
use Photocreate\MailerBundle\Usecase\SendOrderNoticeMailUsecase;
use Photocreate\OrderBundle\Event\PostOrderEvent;
use Photocreate\OrderBundle\Exception\FailedEventException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class OrderEventSubscriber
 *
 * @package Photocreate\OrderBundle\EventSubscriber
 */
class OrderEventSubscriber implements EventSubscriberInterface
{
    /** @var SendOrderCompleteMailUsecase  */
    protected $sendOrderCompleteMailUsecase;

    /** @var SendOrderCompleteMailUsecase  */
    protected $sendOrderCompleteMailUsecaseToSupport;

    /** @var SendOrderNoticeMailUsecase */
    protected $sendOrderNoticeMailUsecase;

    /**
     * OrderEventSubscriber constructor.
     *
     * @param SendOrderCompleteMailUsecase $sendOrderCompleteMailUsecase
     * @param SendOrderCompleteMailUsecase $sendOrderCompleteMailUsecaseToSupport
     * @param SendOrderNoticeMailUsecase $sendOrderNoticeMailUsecase
     */
    public function __construct(
        SendOrderCompleteMailUsecase $sendOrderCompleteMailUsecase,
        SendOrderCompleteMailUsecase $sendOrderCompleteMailUsecaseToSupport,
        SendOrderNoticeMailUsecase $sendOrderNoticeMailUsecase
    )
    {
        $this->sendOrderCompleteMailUsecase          = $sendOrderCompleteMailUsecase;
        $this->sendOrderCompleteMailUsecaseToSupport = $sendOrderCompleteMailUsecaseToSupport;
        $this->sendOrderNoticeMailUsecase            = $sendOrderNoticeMailUsecase;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            PostOrderEvent::NAME => 'onPostOrder',
        ];
    }

    /**
     * @param PostOrderEvent $event
     */
    public function onPostOrder(PostOrderEvent $event)
    {
        try {
            // 注文完了メールの送信
            $orderNum = new OrderNum($event->getOrderNum());

            // 注文者に送信
            $this->sendOrderCompleteMailUsecase->run($orderNum);

            // サポートに送信（セールスフォースに登録するため必要）
            $this->sendOrderCompleteMailUsecaseToSupport->run($orderNum);

            // ご意見ご要望メールを送信
            $this->sendOrderNoticeMailUsecase->run($orderNum);

        } catch (\Exception $e) {
            throw new FailedEventException(sprintf(
                'Failed subscribed event %s. param "order_num" is "%s", exception "%s" occurred, message is "%s".', self::class, $orderNum, get_class($e), $e->getMessage()
            ));
        }

        $event->stopPropagation();
    }
}