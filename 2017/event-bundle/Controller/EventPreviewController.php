<?php

namespace Photocreate\EventBundle\Controller;

use Photocreate\EventBundle\Annotations\AccessControlByLogin;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class EventPreviewController
 *
 * @AccessControlByLogin()
 *
 * @package Photocreate\EventBundle\Controller
 */
class EventPreviewController extends Controller
{
    /**
     * @param $eventId
     * @param $pageId
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function registerAction($eventId, $pageId)
    {
        // 写真番号検索フォーム
        $this->get('photocreate_event.service.preview_mode_session_storage')->register($pageId);

        return $this->redirect($this->generateUrl('photocreate_event.event_album', [
            'eventId' => $eventId,
            'pageId' => $pageId,
        ]));
    }
}
