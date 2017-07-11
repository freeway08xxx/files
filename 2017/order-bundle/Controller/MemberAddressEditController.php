<?php

namespace Photocreate\OrderBundle\Controller;

use Photocreate\ResourceBundle\Entity\MemberAddress;
use Photocreate\ResourceBundle\Form\Type\MemberAddressType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class MemberAddressEditController
 *
 * @package Photocreate\OrderBundle\Controller
 */
class MemberAddressEditController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function formAction(Request $request)
    {
        $member = $this->getDoctrine()->getRepository('Resource:Member')->findOneBy([
            'id' => $this->getUser()->getId(),
        ]);
        $memberAddress = $this->getDoctrine()->getRepository('Resource:MemberAddress')->findOneBy([
            'id' => $member->getDefaultBillMemberAddressId(),
        ]);

        // 会員住所情報が存在しない場合は新規にデータをセット
        $newFlg = false;
        if (!$memberAddress instanceof MemberAddress) {
            $newFlg = true;
            $memberAddress = new MemberAddress();
            $memberAddress->setMemberId($member->getId());
        }

        $form = $this->createForm(MemberAddressType::class, $memberAddress, [
            'method' => 'POST',
            'attr'   => ['novalidate' => 'nonvalidate'],
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // member_addresses にデータを登録
            $em = $this->getDoctrine()->getManager();
            if ($newFlg) {
                $em->persist($memberAddress);
            } else {
                $em->merge($memberAddress); // @see: https://goo.gl/jx40Ld
            }
            $em->flush();

            if ($newFlg) {
                $member->setDefaultBillMemberAddressId($memberAddress->getId());
                $em->persist($member);
                $em->flush();// ここは2回分けてflush
            }

            // 確認画面・完了画面は不要で注文フォームにリダイレクトする
            return $this->redirect($this->generateUrl('photocreate_order.order_form'));
        }

        return $this->render('PhotocreateOrderBundle:member_address_edit:form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
