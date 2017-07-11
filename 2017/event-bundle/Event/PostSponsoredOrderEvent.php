<?php

namespace Photocreate\EventBundle\Event;

use Photocreate\ResourceBundle\Entity\SponsoredOrder;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class PostSponsoredOrderEvent
 *
 * @package Photocreate\EventBundle\Event
 */
class PostSponsoredOrderEvent extends Event
{
    const NAME = 'photocreate_event.post_sponsored_order';

    /** @var SponsoredOrder */
    protected $sponsoredOrder;

    /**
     * OrderCompleteEvent constructor.
     *
     * @param SponsoredOrder $sponsoredOrder
     */
    public function __construct($sponsoredOrder)
    {
        $this->sponsoredOrder = $sponsoredOrder;
    }

    /**
     * @return SponsoredOrder
     */
    public function getSponsoredOrder()
    {
        return $this->sponsoredOrder;
    }
}