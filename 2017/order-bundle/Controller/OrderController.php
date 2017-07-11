<?php

namespace Photocreate\OrderBundle\Controller;

use Photocreate\OrderBundle\Event\PostOrderEvent;
use Photocreate\OrderBundle\Service\OrderCompleteMailSentCheckService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class OrderController
 *
 * @package Photocreate\OrderBundle\Controller
 */
class OrderController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function cartAction()
    {
        return $this->render('PhotocreateOrderBundle:order:cart.html.twig', [
            'params' => $this->buildParams(),
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function trimmingAction()
    {
        return $this->render('PhotocreateOrderBundle:order:trimming.html.twig',[
            'params' => $this->buildParams(),
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function formAction()
    {
        // 住所情報が登録されていない場合は登録フォームにリダイレクト
        $member = $this->getUser();
        $memberAddress = $this->getDoctrine()->getRepository('Resource:View\MemberAddress')->findOneBy([
            'memberId'                   => $member->getId(),
            'isDefaultBillMemberAddress' => true,
        ]);

        // 支払いに必要な住所情報がセットされていることを確認
        if (!$memberAddress || !$memberAddress->isSetFullMemberAddressForPayment()) {
            return $this->redirect($this->generateUrl('photocreate_order.member_address_edit.form'));
        }

        return $this->render('PhotocreateOrderBundle:order:form.html.twig', [
            'params' => $this->buildParams(),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function completeAction(Request $request)
    {
        $num = $request->get('num');
        $order = $this->getDoctrine()->getRepository('Resource:Order')->findOneBy([
            'num' => $num,
        ]);

        /** @var OrderCompleteMailSentCheckService $orderCompleteMailSentCheckService */
        $orderCompleteMailSentCheckService = $this->get('photocreate_order.service.order_complete_mail_sent_check');

        if (!$orderCompleteMailSentCheckService->isSent($order)) {
            // 注文完了後のイベント（メール送信は OrderEventSubscriber が実行）
            $event = new PostOrderEvent($order->getNum());
            $this->get('event_dispatcher')->dispatch(PostOrderEvent::NAME, $event);

            $orderCompleteMailSentCheckService->add($order);
        }

        return $this->render('PhotocreateOrderBundle:order:complete.html.twig', [
            'num' => $order->getNum(),
        ]);
    }

    /**
     * @return string
     */
    private function buildParams()
    {
        $member = $this->getUser();
        $memberId = $member->getId();
        $notMemberHash = $member->getNotMemberHash();
        $storeId    = $this->getParameter('photocreate_order.store_id');
        $apiRootUri = $this->get('photocreate_order.service.api_uri_builder')->getRoot();
        $params     = [
            'member_id'       => $memberId,
            'not_member_hash' => $notMemberHash,
            'store_id'        => $storeId,
            'api_root'        => $apiRootUri
        ];

        return json_encode($params);
    }
}
