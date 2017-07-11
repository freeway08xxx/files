<?php

namespace Photocreate\EventBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class EventSignInController
 *
 * @package Photocreate\Event\Controller
 */
class EventSignInController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loginAction(Request $request)
    {
        $authenticationUtils = $this->get('security.authentication_utils');
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        // ログイン成功・失敗後のリダイレクトパスをセット
        $pathInfo = $request->getPathInfo();
        $failurePath = $pathInfo;
        $targetPath = preg_replace('/\/login/', '', $pathInfo);
        $targetPath = rtrim($targetPath, '/');

        return $this->render('PhotocreateEventBundle:event_sign_in:login.html.twig', [
            'login_form_template'   => $this->getParameter('photocreate_event.login_form_template') ,
            'sign_up_link_template' => $this->getParameter('photocreate_event.sign_up_link_template'),
            'target_path'           => $targetPath,
            'failure_path'          => $failurePath,
            'last_username'         => $lastUsername,
            'error'                 => $error,
        ]);
    }
}
