<?php

namespace Photocreate\EventBundle\Controller;

use Photocreate\EventBundle\Annotations\AccessControlByPageOpenStatus;
use Photocreate\EventBundle\Annotations\LockByPasswordUnlockHistory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class PhotoListController
 *
 * @AccessControlByPageOpenStatus()
 * @LockByPasswordUnlockHistory()
 *
 * @package Photocreate\EventBundle\Controller
 */
class PhotoListController extends Controller
{
    public function indexAction(Request $request, $eventId, $pageId)
    {
        $params  = $this->buildParams($request,$eventId, $pageId);

        // シリアルコード検索イベントにおける１枚無料画像ダウンロードのダウンロードURLを取得
        $downloadPhotoUrl = '';
        if ($this->get('photocreate_event.service.free_photo_download')->isFreePhotoDownloadEvent((int)$eventId)) {
            $serialcode = $request->get('tag');
            $downloadPhotoUrl = sprintf($this->getParameter('photocreate_event.free_photo_download_url_format'), $eventId, $serialcode);
        }

        return $this->render('PhotocreateEventBundle:photo_list:index.html.twig', [
            'params'             => $params,
            'download_photo_url' => $downloadPhotoUrl,
        ]);
    }

    public function photosetAction(Request $request, $eventId, $pageId,$code)
    {
        $params  = $this->buildParams($request,$eventId, $pageId,$code);
        return $this->render('PhotocreateEventBundle::photoset/index.html.twig', [
            'params'   => $params
        ]);
    }

    public function zoomAction()
    {
        return $this->render('PhotocreateEventBundle::zoom/index.html.twig');
    }

    private function buildParams(Request $request,$eventId, $pageId,$code = null)
    {

        $service    = $this->get('photocreate_event.service.api_uri_builder');
        $apiHost    = $service->getHost();
        $apiVersion = $service->getVersion();
        $apiQuery   = $request->getQueryString();
        $apiUrl     = sprintf('/photo-list/events/%s/pages/%s/photos.json?%s',$eventId, $pageId, $apiQuery, $request);

        $params  = [
            'event_id'    => $eventId,
            'page_id'     => $pageId,
            'store_id'    => $this->getParameter('photocreate_event.store_id'),
            'code'        => $code,
            'domain'      => $apiHost,
            'version'     => $apiVersion,
            'path'        => $apiUrl
        ];
        return json_encode($params);
    }

}
