<?php

namespace Photocreate\EventBundle\Controller\UI;

use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class EventSubcategoryController
 *
 * @package Photocreate\EventBundle\Controller
 */
class EventSubcategoryController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function panelAction()
    {
        $apiRootUri = $this->getParameter('photocreate_member.api.root_uri');
        $params = [
            'store_id' => $this->getParameter('photocreate_event.store_id'),
        ];
        $apiUri = $apiRootUri.'/store-subcategories.json?'.http_build_query($params);
        $client = new Client();
        $response = $client->get($apiUri);
        $items = $response->json();

        return $this->render('PhotocreateEventBundle:ui/event_subcategory:panel.html.twig', [
            'store_subcategories' => $items,
        ]);
    }
}
