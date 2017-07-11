<?php

namespace Photocreate\OrderBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class PostOrderEvent
 *
 * @package Photocreate\OrderBundle\Event
 */
class PostOrderEvent extends Event
{
    const NAME = 'photocreate_order.post_order';

    /** @var mixed */
    protected $orderNum;

    /**
     * OrderCompleteEvent constructor.
     *
     * @param $orderNum
     */
    public function __construct($orderNum)
    {
        $this->orderNum = $orderNum;
    }

    /**
     * @return mixed
     */
    public function getOrderNum()
    {
        return $this->orderNum;
    }
}
