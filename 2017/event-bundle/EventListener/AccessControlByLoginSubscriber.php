<?php

namespace Photocreate\EventBundle\EventListener;

use Doctrine\Common\Annotations\AnnotationReader;
use Photocreate\EventBundle\Annotations\AccessControlByLogin;
use Photocreate\MemberBundle\Entity\Member;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class AccessControlByLoginSubscriber
 *
 * @package Photocreate\EventBundle\EventListener
 */
class AccessControlByLoginSubscriber implements EventSubscriberInterface
{
    /** @var AnnotationReader  */
    protected $reader;

    /** @var Router  */
    protected $router;

    /** @var TokenStorageInterface  */
    protected $tokenStorage;

    /**
     * AccessControlByLoginSubscriber constructor.
     *
     * @param AnnotationReader      $reader
     * @param Router                $router
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(
        AnnotationReader $reader,
        Router $router,
        TokenStorageInterface $tokenStorage
    )
    {
        $this->reader       = $reader;
        $this->router       = $router;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => [
                ['onKernelController', 5],
           ],
        ];
    }

    /**
     * @param FilterControllerEvent $event
     *
     * @return RedirectResponse|void
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();

        if ($controller instanceof \Closure) {
            return;
        }

        $object = new \ReflectionObject($controller[0]);
        $method = $object->getMethod($controller[1]);

        // アノテーションの取得
        $classAnnotation  = $this->reader->getClassAnnotation($object, AccessControlByLogin::class);
        $methodAnnotation = $this->reader->getMethodAnnotation($method, AccessControlByLogin::class);

        if (!$classAnnotation && !$methodAnnotation) {
            // アノテーションがない場合はアクセスを許可
            return;
        }

        if ($this->tokenStorage->getToken()->getUser() instanceof Member) {
            // ログインしている場合はアクセス許可
            return;
        }

        // ログインしていない場合はログイン画面にリダイレクト
        $request = $event->getRequest();
        $redirectUri = $request->getSchemeAndHttpHost().$request->getBaseUrl().$request->getPathInfo().'/login'.($request->getQueryString()?'?'.$request->getQueryString():'');
        $event->setController(function() use ($redirectUri) {
            return new RedirectResponse($redirectUri);
        });
    }
}
