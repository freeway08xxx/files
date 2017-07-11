<?php

namespace Photocreate\EventBundle\Controller;

use GuzzleHttp\Client;
use Photocreate\EventBundle\Exception\InvalidArgumentException;
use Photocreate\ResourceBundle\Criteria\SearchStoreSubcategoryCriteriaBuilder;
use Photocreate\ResourceBundle\Entity\PasswordUnlockHistory;
use Photocreate\EventBundle\Form\Type\PasswordUnlockHistoryWithSerialcodeType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class EventSubcategoryController
 *
 * @package Photocreate\EventBundle\Controller
 */
class EventSubcategoryController extends Controller
{
    /**
     * @param $subcategoryId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction($subcategoryId, Request $request)
    {
        // ストアで設定されている種目か確認
        $storeId = $this->getParameter('photocreate_event.store_id');
        $criteriaBuilder = new SearchStoreSubcategoryCriteriaBuilder();
        $criteria = $criteriaBuilder
            ->setStoreId($storeId)
            ->setSubcategoryId($subcategoryId)
            ->build();
        $subCategories = $this->get('photocreate_resource.usecase.search_store_subcategory')->run($criteria);
        if ($subCategories->isEmpty()) {
            throw new InvalidArgumentException('Invalid subcategory_id: "'.$subcategoryId.'" on store_id:"'.$storeId.'".');
        }

        $apiRootUri = $this->getParameter('photocreate_member.api.root_uri');
        $params = [
            'store_id' => $this->getParameter('photocreate_event.store_id'),
        ];
        $apiUri = $apiRootUri.'/event-subcategory/subcategories/'.$subcategoryId.'.json?'.http_build_query($params);
        $client = new Client();
        $response = $client->get($apiUri);
        $items = $response->json();

        // TODO: subcategory の取得も API に実装する
        $subcategory = $this->getDoctrine()->getRepository('Resource:Subcategory')->findOneBy(['id' => $subcategoryId]);
        // Formに種目を選択させる
        $request->query->set('event_search', [
            'sub_category_id' => $subcategoryId,
        ]);

        return $this->render('PhotocreateEventBundle:event_subcategory:index.html.twig', [
            'subcategory' => $subcategory,
            'events'      => $items,
            'store_id'    => $this->getParameter('photocreate_event.store_id'),
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction()
    {
        $apiRootUri = $this->getParameter('photocreate_member.api.root_uri');
        $params = [
            'store_id' => $this->getParameter('photocreate_event.store_id'),
        ];
        $apiUri = $apiRootUri.'/store-subcategories.json?'.http_build_query($params);
        $client = new Client();
        $response = $client->get($apiUri);
        $items = $response->json();

        return $this->render('PhotocreateEventBundle:event_subcategory:list.html.twig', [
            'store_subcategories' => $items,
        ]);
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function babyAction(Request $request)
    {
        $form = $this->createForm(PasswordUnlockHistoryWithSerialcodeType::class, new PasswordUnlockHistory(), [
            'method'   => 'POST',
            'attr'     => ['novalidate' => 'nonvalidate'],
            'doctrine' => $this->getDoctrine(),
            'member'   => $this->getUser(),
            'store_id' => $this->getParameter('photocreate_event.store_id'),
            'album_password_unlocker' => $this->get('photocreate_event.service.album_password_unlocker'),
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // password_unlock_histories に解除履歴を登録
            $passwordUnlockHistory = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($passwordUnlockHistory);
            $em->flush();

            // リダイレクトに必要なパラメーターの取得
            $page       = $passwordUnlockHistory->getEvent();
            $serialcode = $passwordUnlockHistory->getSerialcode();

            return $this->redirect($this->generateUrl(
                'photocreate_event.photo_list',
                [
                    'eventId'  => $page->getEventId(),
                    'pageId'   => $page->getId(),
                    'tag_code' => 'serialcode',
                    'tag'      => $serialcode->getSerialcode(),
                ]
            ));
        }

        return $this->render('PhotocreateEventBundle:event_subcategory:baby.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * TODO: フォームの処理が babyAction と重複しているのでまとめる
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function studioAction(Request $request)
    {
        $form = $this->createForm(PasswordUnlockHistoryWithSerialcodeType::class, new PasswordUnlockHistory(), [
            'method'   => 'POST',
            'attr'     => ['novalidate' => 'nonvalidate'],
            'doctrine' => $this->getDoctrine(),
            'member'   => $this->getUser(),
            'store_id' => $this->getParameter('photocreate_event.store_id'),
            'album_password_unlocker' => $this->get('photocreate_event.service.album_password_unlocker'),
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // password_unlock_histories に解除履歴を登録
            $passwordUnlockHistory = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($passwordUnlockHistory);
            $em->flush();

            // リダイレクトに必要なパラメーターの取得
            $page       = $passwordUnlockHistory->getEvent();
            $serialcode = $passwordUnlockHistory->getSerialcode();

            return $this->redirect($this->generateUrl(
                'photocreate_event.photo_list',
                [
                    'eventId'  => $page->getEventId(),
                    'pageId'   => $page->getId(),
                    'tag_code' => 'serialcode',
                    'tag'      => $serialcode->getSerialcode(),
                ]
            ));
        }

        return $this->render('PhotocreateEventBundle:event_subcategory:studio.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
