<?php

namespace Photocreate\EventBundle\Controller;

use Photocreate\ResourceBundle\Entity\EmailQueue;
use Photocreate\EventBundle\Annotations\AccessControlByPageOpenStatus;
use Photocreate\EventBundle\Annotations\LockByPasswordUnlockHistory;
use Photocreate\EventBundle\Form\Type\PhotoDeleteRequestType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class PhotoDeleteRequestController
 *
 * @AccessControlByPageOpenStatus()
 * @LockByPasswordUnlockHistory()
 *
 * @package Photocreate\EventBundle\Controller
 */
class PhotoDeleteRequestController extends Controller
{
    /**
     * @param Request $request
     * @param         $pageId
     * @param         $eventId
     * @param         $photoId
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function formAction(Request $request, $pageId, $eventId, $photoId)
    {
        $form = $this->createForm(PhotoDeleteRequestType::class, $emailQueue = new EmailQueue(), [
            'method' => 'POST',
            'attr'   => ['novalidate' => 'nonvalidate'],
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $member = $this->getUser();

            /** @var EmailQueue $emailQueue */
            $emailQueue = $form->getData();
            $emailQueue->setEventId($eventId);
            $emailQueue->setPageId($pageId);
            $emailQueue->setPhotoId($photoId);
            $emailQueue->setMemberId($member->getId());
            $emailQueue->setTypeCode(EmailQueue::TYPE_CODE_DELETE_MY_PHOTO); // 固定値
            $emailQueue->setStoreId($this->getParameter('photocreate_event.store_id'));

            $em = $this->getDoctrine()->getManager();
            $em->persist($emailQueue);
            $em->flush($emailQueue);

            return $this->redirect($this->generateUrl('photocreate_event.photo_delete_request.complete', [
                'eventId' => $eventId,
                'pageId'  => $pageId,
                'photoId' => $photoId,
            ]));
        }

        // 写真情報の取得
        $photo = $this->getDoctrine()->getRepository('Resource:Photo')->findOneBy(['id' => $photoId]);

        return $this->render('PhotocreateEventBundle:photo_delete_request:form.html.twig', [
            'form'  => $form->createView(),
            'photo' => $photo,
        ]);

    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function completeAction()
    {
        return $this->render('PhotocreateEventBundle:photo_delete_request:complete.html.twig');
    }
}
