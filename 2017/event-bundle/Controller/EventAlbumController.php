<?php

namespace Photocreate\EventBundle\Controller;

use Photocreate\EventBundle\Annotations\AccessControlByLogin;
use Photocreate\EventBundle\Annotations\AccessControlByPageOpenStatus;
use Photocreate\EventBundle\Annotations\AccessControlByPagePhotoSearchType;
use Photocreate\EventBundle\Annotations\LockByPasswordUnlockHistory;
use Photocreate\EventBundle\Entity\PhotoNumbersQuery;
use Photocreate\EventBundle\Form\Type\PhotoSearchByPhotoNumbersType;
use Photocreate\ResourceBundle\Entity\View\Page;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class EventAlbumController
 *
 * @AccessControlByPagePhotoSearchType()
 * @AccessControlByPageOpenStatus()
 * @AccessControlByLogin()
 * @LockByPasswordUnlockHistory()
 *
 * @package Photocreate\EventBundle\Controller
 */
class EventAlbumController extends Controller
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
        $albumName = $request->get('album_name');

        // 写真番号検索フォーム
        $form = $this->createForm(PhotoSearchByPhotoNumbersType::class, new PhotoNumbersQuery(), [
            'method'   => 'POST',
            'attr'     => ['novalidate' => 'nonvalidate'],
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $photoNumbersQuery = $form->getData();
            $query = $photoNumbersQuery->getQuery();
            $url = $this->generateUrl(
                'photocreate_event.photo_list',
                [
                    'eventId'  => $eventId,
                    'pageId'   => $pageId,
                ]
            ).'?'.$query;

            return $this->redirect($url);
        }

        $eventAlbums = $this->fetchEventAlbum($eventId, $pageId, $albumName);

        return $this->render('PhotocreateEventBundle:event_album:index.html.twig', [
            'event_album'            => $eventAlbums,
            'request_album_name'     => $albumName,
            'show_album_search_form' => $this->showAlbumSearchForm($eventAlbums),
            'form'                   => $form->createView(),
        ]);
    }

    /**
     * @param $eventId
     * @param $pageId
     *
     * @return mixed
     */
    protected function fetchEventAlbum($eventId, $pageId, $albumName)
    {
        $member = $this->getUser();
        $oauthAccessToken = $member->getOAuthAccessToken();
        $oauthAccessToken = $oauthAccessToken->getToken();

        $apiRootUri = $this->get('photocreate_order.service.api_uri_builder')->getRoot();
        $apiUri = sprintf('%s/event-album/events/%s/pages/%s/albums.json', $apiRootUri, $eventId, $pageId);
        if (strlen($albumName) > 0) {
            $apiUri .= '?album_name=' . $albumName;
        }
        $client = new Client();
        $response = $client->get($apiUri, ['headers' => ['Authorization' => 'Bearer '.$oauthAccessToken]]);

        return  $response->json();
    }

    /**
     * 「イベント統一パス」または「パスワードなし」のイベントで、Directory Level 2 の数が一定数以上だったら区分絞込フォームを表示（=true）する
     *
     * @param array $eventAlbums
     * @return bool
     */
    protected function showAlbumSearchForm(array $eventAlbums): bool
    {
        if (!isset($eventAlbums['albums']) && !is_array($eventAlbums['albums'])) {
            return false;
        }

        // 「イベント統一パス」または「パスワードなし」のイベントでなければfalse
        $passwordType = $eventAlbums['event']['password_type'] ?? '';
        if (!in_array($passwordType, [Page::PASSWORD_TYPE_COMMON, Page::PASSWORD_TYPE_NONE])) {
            return false;
        }

        $directoryLevel2Count = 0;
        foreach ($eventAlbums['albums'] as $album) {

            if (!isset($album['directory_level'])) {
                continue;
            }

            if ($album['directory_level'] !== 2) {
                continue;
            }

            $directoryLevel2Count++;

            // 一定数以上存在したらtrueを返す
            if($directoryLevel2Count >= 50){
                return true;
            }
        }

        return false;
    }
}
