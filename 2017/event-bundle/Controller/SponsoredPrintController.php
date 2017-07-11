<?php

namespace Photocreate\EventBundle\Controller;

use Photocreate\EventBundle\Annotations\AccessControlBySponsoredPrint;
use Photocreate\ResourceBundle\Entity\Photo;
use Photocreate\ResourceBundle\Entity\Security\Member;
use Photocreate\ResourceBundle\Entity\SponsoredOrder;
use Photocreate\ResourceBundle\Entity\View\Page;
use Photocreate\ResourceBundle\Form\Type\SponsoredOrderType;
use Photocreate\EventBundle\Annotations\AccessControlByPageOpenStatus;
use Photocreate\EventBundle\Annotations\AccessControlByLogin;
use Photocreate\EventBundle\Annotations\LockByPasswordUnlockHistory;
use Photocreate\EventBundle\Event\PostSponsoredOrderEvent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class SponsoredPrintController
 *
 * @AccessControlByPageOpenStatus()
 * @AccessControlByLogin()
 * @AccessControlBySponsoredPrint()
 * @LockByPasswordUnlockHistory()
 *
 * @package Photocreate\EventBundle\Controller
 */
class SponsoredPrintController extends Controller
{

    const ENTITY_SESSION_KEY = 'photocreate_event.sponsored_print.sponsored_order';

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request, $eventId, $pageId, $photoId, $campaignCode)
    {
        /** @var Photo $photo */
        $photo = $this->getDoctrine()->getRepository('PhotocreateResourceBundle:Photo')->find($photoId);

        return $this->render('PhotocreateEventBundle:sponsored_print:index.html.twig',[
            'eventId' => $eventId,
            'pageId' => $pageId,
            'photoId' => $photoId,
            'photo' => $photo,
            'campaignCode' => $campaignCode,
        ]);
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function formAction(Request $request, $eventId, $pageId, $photoId, $campaignCode)
    {
        $form = $this->createForm(SponsoredOrderType::class, $entity = new SponsoredOrder(), [
            'method'        => 'POST',
            'attr'          => ['novalidate' => 'nonvalidate'],
            'campaign_code' => $campaignCode,
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // フォームから送信されたデータの扱いをシンプルにするため
            // セッションにセットする箇所はフォームのバリデーション後に絞る
            $this->get('session')->set(self::ENTITY_SESSION_KEY, $entity);

            return $this->redirect($this->generateUrl('photocreate_event.sponsored_print.confirm',[
                'eventId' => $eventId,
                'pageId' => $pageId,
                'photoId' => $photoId,
                'campaignCode' => $campaignCode,
            ]));
        }

        return $this->render('PhotocreateEventBundle:sponsored_print:form.html.twig', [
            'form' => $form->createView(),
            'eventId' => $eventId,
            'pageId' => $pageId,
            'photoId' => $photoId,
            'campaignCode' => $campaignCode,
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function confirmAction(Request $request, $eventId, $pageId, $photoId, $campaignCode)
    {
        if (!$entity = $this->get('session')->get(self::ENTITY_SESSION_KEY)) {
            // セッションにデータが存在しない場合はフォームにリダイレクト
            return $this->redirect($this->generateUrl('photocreate_event.sponsored_print.form',[
                'eventId' => $eventId,
                'pageId' => $pageId,
                'photoId' => $photoId,
                'campaignCode' => $campaignCode,
            ]));
        }

        $form = $this->createForm(SponsoredOrderType::class, $entity, [
            'method'        => 'POST',
            'attr'          => ['novalidate' => 'nonvalidate'],
            'campaign_code' => $campaignCode,
        ]);

        return $this->render('PhotocreateEventBundle:sponsored_print:confirm.html.twig', [
            'form' => $form->createView(),
            'entity' => $entity,
            'eventId' => $eventId,
            'pageId' => $pageId,
            'photoId' => $photoId,
            'campaignCode' => $campaignCode,
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function completeAction(Request $request, $eventId, $pageId, $photoId, $campaignCode)
    {
        /** @var SponsoredOrder $entity */
        if (!$entity = $this->get('session')->get(self::ENTITY_SESSION_KEY)) {
            // セッションにデータが存在しない場合はフォームにリダイレクト
            return $this->redirect($this->generateUrl('photocreate_event.sponsored_print.form', [
                'eventId'      => $eventId,
                'pageId'       => $pageId,
                'photoId'      => $photoId,
                'campaignCode' => $campaignCode,
            ]));
        }

        /** @var Member $member */
        $member = $this->getUser();

        /** @var Page $page */
        $page = $this->getDoctrine()->getRepository('Resource:View\Page')->findOneBy([
            'pageId' => $pageId,
            'eventId' => $eventId,
        ]);

        /** @var Photo $photo */
        $photo = $this->getDoctrine()->getRepository('Resource:Photo')->find($photoId);

        // データ登録
        $entity
            ->setMemberId($member->getId())
            ->setCampaignCode($campaignCode)
            ->setPage($page)
            ->setPhoto($photo);
        $entity = $this->get('photocreate_resource.usecase.register_sponsored_order')->run($entity);

        // メール送信
        $event = new PostSponsoredOrderEvent($entity);
        $this->get('event_dispatcher')->dispatch(PostSponsoredOrderEvent::NAME, $event);

        // セッションのクリア
        $this->get('session')->remove(self::ENTITY_SESSION_KEY);

        return $this->render('PhotocreateEventBundle:sponsored_print:complete.html.twig',[
            'sponsoredOrder' => $entity,
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function orderedAction(Request $request, $eventId, $pageId, $photoId, $campaignCode)
    {
        return $this->render('PhotocreateEventBundle:sponsored_print:ordered.html.twig');
    }
}
