<?php

namespace Photocreate\EventBundle\Controller;

use GuzzleHttp\Client;
use Photocreate\EventBundle\Annotations\AccessControlByLogin;
use Photocreate\EventBundle\Annotations\AccessControlByPageOpenStatus;
use Photocreate\EventBundle\Event\PostWaitingEmailQueueEvent;
use Photocreate\EventBundle\Form\Type\WaitingEmailQueueType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class EventWaitingController
 *
 * @AccessControlByPageOpenStatus()
 * @AccessControlByLogin()
 *
 * @package Photocreate\EventBundle\Controller
 */
class EventWaitingController extends Controller
{
    /**
     * @param Request $request
     * @param         $eventId
     * @param         $pageId
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request, $eventId, $pageId)
    {
        // ユーザー情報を取得
        $member = $this->getUser();

        // 公開通知メール登録済みかどうか判断
        $isRegistered = $this->get('photocreate_resource.service.waiting_email_queue')->isRegistered($pageId, $member);

        // 公開通知メール登録用の EmailQueue を作成
        $emailQueue = $this->get('photocreate_resource.service.waiting_email_queue')->build($pageId, $member);

        // 公開通知メール登録フォームの処理
        $form = $this->createForm(WaitingEmailQueueType::class, $emailQueue, [
            'method'                    => 'POST',
            'attr'                      => ['novalidate' => 'nonvalidate'],
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // EmailQueue の登録
            $this->get('photocreate_resource.usecase.register_email_queue')->run($form->getData());

            // 公開通知メール登録完了後のイベント
            $event = new PostWaitingEmailQueueEvent($form->getData());
            $this->get('event_dispatcher')->dispatch(PostWaitingEmailQueueEvent::NAME, $event);

            return $this->redirect($this->generateUrl('photocreate_event.event_waiting.complete', [
                'eventId' => $eventId,
                'pageId'  => $pageId,
            ]));
        }

        return $this->render('PhotocreateEventBundle:event_waiting:index.html.twig', [
            'event_album'   => $this->fetchEventAlbum($eventId, $pageId),
            'form'          => $form->createView(),
            'is_registered' => $isRegistered,
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function completeAction()
    {
        return $this->render('PhotocreateEventBundle:event_waiting:complete.html.twig');
    }

    /**
     * @param $eventId
     * @param $pageId
     *
     * @return mixed
     */
    protected function fetchEventAlbum($eventId, $pageId)
    {
        $member = $this->getUser();
        $oauthAccessToken = $member->getOAuthAccessToken();
        $oauthAccessToken = $oauthAccessToken->getToken();

        $apiRootUri = $this->get('photocreate_order.service.api_uri_builder')->getRoot();
        $apiUri = sprintf('%s/event-album/events/%s/pages/%s/albums.json', $apiRootUri, $eventId, $pageId);
        $client = new Client();
        $response = $client->get($apiUri, ['headers' => ['Authorization' => 'Bearer '.$oauthAccessToken]]);

        return  $response->json();
    }
}
