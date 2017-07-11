<?php

namespace Photocreate\EventBundle\Controller;

use GuzzleHttp\Client;
use Photocreate\ResourceBundle\Entity\Value\EventSearchDateTime;
use Photocreate\ResourceBundle\Entity\Value\EventSearchKeywords;
use Photocreate\ResourceBundle\Entity\Value\EventSearchParams;
use Photocreate\ResourceBundle\Entity\Value\EventSearchSubCategoryId;
use Photocreate\ResourceBundle\Entity\View\Event;
use Photocreate\EventBundle\Entity\Keywords;
use Photocreate\EventBundle\Form\Type\EventSearchByKeywordsType;
use Photocreate\ResourceBundle\Form\Type\EventSearchType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Photocreate\EventSearchBundle\Entity\Date;
use Photocreate\EventSearchBundle\Form\EventSerialCodeType;
use Photocreate\EventSearchBundle\Entity\SerialCode;
use Photocreate\EventSearchBundle\Form\EventDateType;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class EventSearchController
 *
 * @package Photocreate\EventBundle\Controller
 */
class EventSearchController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function resultAction(Request $request)
    {
        $event_search = $request->get('event_search');
        $isKeywordSearch = false;
        if ($event_search) {
            $keywords      = $event_search['state'] ?? null;
            $date          = $event_search['date'] ?? null;
            $subCategoryId = $event_search['sub_category_id'] ?? null;
        } else {
            $keywords = $request->get('keywords');
            $date = null;
            $subCategoryId = null;
            $isKeywordSearch = true;
        }

        $storeId = $this->getParameter('photocreate_event.store_id');

        // 検索キーワードがイベントIDだった場合は写真区分一覧にリダイレクト
        if (preg_match('/^e?\d+$/i', mb_convert_kana($keywords, 'a'), $matches)) {
            $event = $this->getDoctrine()->getRepository('Resource:View\Event')->findOneBy([
                'eventId' => str_replace(['E', 'e'], '', $matches[0]),
                'storeId' => $storeId,
            ]);

            if ($event instanceof Event) {
                return $this->redirect($this->generateUrl('photocreate_event.event_album', [
                    'eventId' => $event->getEventId(),
                    'pageId'  => $event->getPageId(),
                ]));
            }
        }

        // API でイベント検索
        $items = [];
        if ($keywords || $date || $subCategoryId) {
            $apiRootUri = $this->getParameter('photocreate_member.api.root_uri');
            // TODO: 検索は page_tags -> pages としており page_tags の id 順で取得した pages.id から取得できる pages の開催日順となっている、これでいいか検討が必要
            $params = [
                'keywords'       => $keywords,
                'date'           => $date,
                'subcategory_id' => $subCategoryId,
                'sort_by'        => 'id',
                'sort_direction' => 'desc',
                'limit'          => 1000,
                'store_id'       => $storeId,
            ];
            $apiUri = $apiRootUri.'/event-search.json?'.http_build_query($params);
            $client = new Client();
            $response = $client->get($apiUri);
            $items = $response->json();

            // キーワード検索結果が１件だけの場合は写真区分一覧にリダイレクト
            if (!empty($keyword) && count($items) === 1) {
                $event = array_shift($items);

                if ($event['store_id'] == $storeId) {
                    return $this->redirect($this->generateUrl('photocreate_event.event_album', [
                        'eventId' => $event['event_id'],
                        'pageId'  => $event['page_id']
                    ]));
                }
            }
        }

        $subcategory = null;
        if ($subCategoryId) {
            $subcategory = $this->getDoctrine()->getRepository('Resource:Subcategory')->findOneBy(['id' => $subCategoryId]);
        }

        return $this->render('PhotocreateEventBundle:event_search:result.html.twig', [
            'events'   => $items,
            'store_id' => $storeId,
            'is_keyword_search' => $isKeywordSearch,
            'keywords' => $keywords,
            'date' => $date,
            'subcategory' => $subcategory,
        ]);
    }

    /**
     * @return Response
     */
    public function formAction(Request $request)
    {
        $form = $this->createForm(EventSearchType::class);
        $form->submit($request);

        return $this->render('PhotocreateEventBundle:event_search:_form_refine.html.twig',[
           'form' => $form->createView(),
        ]);
    }
}
