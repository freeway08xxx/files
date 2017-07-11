<?php

namespace Photocreate\EventBundle\EventListener;

use Doctrine\Common\Annotations\AnnotationReader;
use Photocreate\ResourceBundle\Repository\View\EventRepository;
use Photocreate\EventBundle\Annotations\AccessControlByPageOpenStatus;
use Photocreate\EventBundle\Annotations\AccessControlByPagePhotoSearchType;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class AccessControlByPagePhotoSearchTypeSubscriber
 *
 * @package Photocreate\EventBundle\EventListener
 */
class AccessControlByPagePhotoSearchTypeSubscriber implements EventSubscriberInterface
{
    /** @var AnnotationReader  */
    protected $reader;

    /** @var Router  */
    protected $router;

    /** @var EventRepository  */
    protected $repository;

    /** @var  int */
    protected $storeId;

    /**
     * AccessControlByPagePhotoSearchTypeSubscriber constructor.
     *
     * @param AnnotationReader $reader
     * @param Router           $router
     * @param EventRepository  $repository
     * @param                  $storeId
     */
    public function __construct(
        AnnotationReader $reader,
        Router $router,
        EventRepository $repository,
        int $storeId
    )
    {
        $this->reader     = $reader;
        $this->router     = $router;
        $this->repository = $repository;
        $this->storeId    = $storeId;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => [
                ['onKernelController', 10],
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
        $classAnnotation  = $this->reader->getClassAnnotation($object, AccessControlByPagePhotoSearchType::class);
        $methodAnnotation = $this->reader->getMethodAnnotation($method, AccessControlByPagePhotoSearchType::class);

        if (!$classAnnotation && !$methodAnnotation) {
            // アノテーションがない場合はアクセスを許可
            return;
        }

        $request = $event->getRequest();
        $pageId  = $request->get('pageId');
        $eventId = $request->get('eventId');

        /** @var  \Photocreate\ResourceBundle\Entity\View\Event $page */
        $page = $this->repository->findOneBy([
            'eventId' => $eventId,
            'pageId'  => $pageId,
            'storeId' => $this->storeId,
        ]);

        if ($page->isSerialcodePhotoSearchType()) {
            // シリアルコード検索の場合は種目トップにリダイレクト
            $redirectUri = $this->router->generate('photocreate_event.event_subcategory.studio');
            $event->setController(function() use ($redirectUri) {
                return new RedirectResponse($redirectUri);
            });
        }
    }
}
