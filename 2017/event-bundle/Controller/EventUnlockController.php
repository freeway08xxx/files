<?php

namespace Photocreate\EventBundle\Controller;

use Photocreate\ResourceBundle\Entity\PasswordUnlockHistory;
use Photocreate\EventBundle\Annotations\AccessControlByLogin;
use Photocreate\EventBundle\Annotations\AccessControlByPageOpenStatus;
use Photocreate\EventBundle\Annotations\AccessControlByPagePhotoSearchType;
use Photocreate\EventBundle\Form\Type\PasswordUnlockHistoryType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class EventUnlockController
 *
 * @AccessControlByPagePhotoSearchType()
 * @AccessControlByPageOpenStatus()
 * @AccessControlByLogin()
 *
 * @package Photocreate\EventBundle\Controller
 */
class EventUnlockController extends Controller
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
        // パスワードなしイベントの場合は強制解除して写真区分一覧に遷移
        // TODO: 同じセッションのアクセスでも password_unlock_history がどんどん増えていくため修正必要
        $page = $this->getDoctrine()->getRepository('Resource:Page')->findOneBy(['id' => $pageId]);
        if ($this->get('photocreate_event.service.album_password_unlocker')->unlockIfNonePasswordTypePage($page, $this->getUser())) {
           return $this->redirect($this->generateUrl('photocreate_event.event_album', [
               'eventId' => $eventId,
               'pageId' => $pageId,
           ]));
        }

        // 閲覧パスワード解除フォームの処理
        $masterPasswords = $this->getParameter('photocreate_event.master_passwords');
        $form = $this->createForm(PasswordUnlockHistoryType::class, $passwordUnlockHistory = new PasswordUnlockHistory(), [
            'method'                        => 'POST',
            'attr'                          => ['novalidate' => 'nonvalidate'],
            'search_album_password_usecase' => $this->get('photocreate_resource.usecase.search_album_password'),
            'album_password_unlocker'       => $this->get('photocreate_event.service.album_password_unlocker'),
            'page_id'                       => $pageId,
            'member_id'                     => $this->getUser()->getId(),
            'master_passwords'              => $masterPasswords,
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // password_unlock_histories に解除履歴を登録
            $passwordUnlockHistory = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($passwordUnlockHistory);
            $em->flush();

            return $this->redirect($this->generateUrl(
                'photocreate_event.event_album',
                [
                    'eventId' => $eventId,
                    'pageId'  => $pageId,
                ]
            ));
        }

        return $this->render('PhotocreateEventBundle:event_unlock:index.html.twig', [
            'event_album' => $this->fetchEventAlbum($eventId, $pageId),
            'form'        => $form->createView(),
        ]);
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
