<?php

namespace Photocreate\EventBundle\EventListener;

use Photocreate\MailerBundle\Usecase\SendSponsoredOrderCompleteMailUsecase;
use Photocreate\EventBundle\Event\PostSponsoredOrderEvent;
use Photocreate\EventBundle\Exception\FailedEventException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class SponsoredOrderEventSubscriber
 *
 * @package Photocreate\OrderBundle\EventSubscriber
 */
class SponsoredOrderEventSubscriber implements EventSubscriberInterface
{
    /** @var SendSponsoredOrderCompleteMailUsecase  */
    protected $sendSponsoredOrderCompleteMailUsecase;

    /**
     * SponsoredOrderEventSubscriber constructor.
     *
     * @param SendSponsoredOrderCompleteMailUsecase $sendSponsoredOrderCompleteMailUsecase
     */
    public function __construct(
        SendSponsoredOrderCompleteMailUsecase $sendSponsoredOrderCompleteMailUsecase
    )
    {
        $this->sendSponsoredOrderCompleteMailUsecase = $sendSponsoredOrderCompleteMailUsecase;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            PostSponsoredOrderEvent::NAME => 'onPostSponsoredOrder',
        ];
    }

    /**
     * @param PostSponsoredOrderEvent $event
     */
    public function onPostSponsoredOrder(PostSponsoredOrderEvent $event)
    {
        try {
            $this->sendSponsoredOrderCompleteMailUsecase->run($event->getSponsoredOrder());
        } catch (\Exception $e) {
            throw new FailedEventException(sprintf(
                'Failed subscribed event %s. exception "%s" occurred, message is "%s".', self::class, get_class($e), $e->getMessage()
            ));
        }

        $event->stopPropagation();
    }
}