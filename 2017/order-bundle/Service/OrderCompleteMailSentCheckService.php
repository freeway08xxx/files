<?php

namespace Photocreate\OrderBundle\Service;

use Photocreate\ResourceBundle\Entity\Order;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class OrderCompleteMailSentCheckService
 * @package Photocreate\OrderBundle\Service
 */
class OrderCompleteMailSentCheckService
{
    /** @var Session $session */
    private $session;

    /** @var string $sessionName */
    private $sessionName = 'order-conplete-mail-sent';

    /**
     * OrderCompleteMailSentCheckService constructor.
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
        $this->clearPast();
    }

    protected function clearPast()
    {
        $values    = $this->get();
        $newValues = [];
        foreach ($values as $num => $createTs) {
            if ($createTs < new \DateTime('-1 hour')) {
                continue;
            }
            $newValues[$num] = $createTs;
        }
        $this->session->set($this->sessionName, $newValues);
    }

    /**
     * @return array
     */
    public function get(): array
    {
        $values = $this->session->get($this->sessionName) ?? [];
        if (!is_array($values)) {
            return [];
        }

        return $values;
    }

    /**
     * @param Order $order
     * @return bool
     */
    public function isSent(Order $order): bool
    {
        // 古すぎる注文は送信済みとみなす
        if ($order->getCreateTs() < new \DateTime('-1 hour')) {
            return true;
        }

        if (array_key_exists($order->getNum(), $this->get())) {
            return true;
        }

        return false;
    }

    /**
     * @param Order $order
     */
    public function add(Order $order)
    {
        $values = $this->get();
        $values[$order->getNum()] = $order->getCreateTs();

        $this->session->set($this->sessionName, $values);
    }
}
