<?php

namespace Photocreate\EventBundle\Controller\UI;

use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class EventHeaderController
 *
 * @package Photocreate\EventBundle\Controller\UI
 */
class EventHeaderController extends Controller
{
    /**
     * @param $eventId
     * @param $pageId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function headerAction($eventId, $pageId)
    {
        $event = $this->getDoctrine()->getRepository('Resource:View\Event')->findOneBy(['eventId' => $eventId, 'pageId' => $pageId]);

        return $this->render('PhotocreateEventBundle::_block.event_header.html.twig', [
            'event' => $event,
        ]);
    }
}
