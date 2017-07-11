<?php

namespace Photocreate\EventBundle\EventListener;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Collections\Collection;
use Photocreate\EventBundle\Annotations\AccessControlBySponsoredPrint;
use Photocreate\EventBundle\Service\PreviewModeSessionStorage;
use Photocreate\EventBundle\Service\SponsredPrintCheckService;
use Photocreate\ResourceBundle\Criteria\SearchOrderCriteriaBuilder;
use Photocreate\ResourceBundle\Criteria\SearchSponsoredOrderCriteriaBuilder;
use Photocreate\ResourceBundle\Entity\Order;
use Photocreate\ResourceBundle\Entity\Photo;
use Photocreate\ResourceBundle\Entity\Security\Member;
use Photocreate\ResourceBundle\Entity\SponsoredOrder;
use Photocreate\ResourceBundle\Entity\View\Page;
use Photocreate\EventBundle\Annotations\AccessControlByPageOpenStatus;
use Photocreate\EventBundle\Exception\AccessDeniedException;
use Photocreate\ResourceBundle\Repository\PhotoRepository;
use Photocreate\ResourceBundle\Repository\View\PageRepository;
use Photocreate\ResourceBundle\Usecase\SearchOrderUsecase;
use Photocreate\ResourceBundle\Usecase\SearchSponsoredOrderUsecase;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class AccessControlBySponsoredPrintSubscriber implements EventSubscriberInterface
{
    /** @var AnnotationReader */
    protected $reader;

    /** @var Router */
    protected $router;

    /** @var TokenStorage */
    protected $tokenStorage;

    /** @var PageRepository */
    protected $pageRepository;

    /** @var PhotoRepository */
    protected $photoRepository;

    /** @var SponsredPrintCheckService */
    protected $sponsredPrintCheckService;

    /** @var int */
    protected $storeId;

    /** @var array */
    protected $notOrderedCheckRoutings = [
        'photocreate_event.sponsored_print.ordered',
    ];

    /**
     * AccessControlBySponsoredPrintSubscriber constructor.
     *
     * @param AnnotationReader $reader
     * @param Router $router
     * @param TokenStorage $tokenStorage
     * @param PageRepository $repository
     * @param SponsredPrintCheckService $sponsredPrintCheckService
     * @param int $storeId
     */
    public function __construct(
        AnnotationReader $reader,
        Router $router,
        TokenStorage $tokenStorage,
        PageRepository $pageRepository,
        PhotoRepository $photoRepository,
        SponsredPrintCheckService $sponsredPrintCheckService,
        int $storeId
    ) {
        $this->reader                    = $reader;
        $this->router                    = $router;
        $this->tokenStorage              = $tokenStorage;
        $this->pageRepository            = $pageRepository;
        $this->photoRepository           = $photoRepository;
        $this->sponsredPrintCheckService = $sponsredPrintCheckService;
        $this->storeId                   = $storeId;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => [
                ['onKernelController', 40],
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
        $classAnnotation  = $this->reader->getClassAnnotation($object, AccessControlBySponsoredPrint::class);
        $methodAnnotation = $this->reader->getMethodAnnotation($method, AccessControlBySponsoredPrint::class);

        if (!$classAnnotation && !$methodAnnotation) {
            // アノテーションがない場合はアクセスを許可
            return;
        }

        $request   = $event->getRequest();
        $pathInfo  = $request->getPathInfo();
        $routeInfo = $this->router->match($pathInfo);

        $pageId       = $request->get('pageId');
        $eventId      = $request->get('eventId');
        $photoId      = $request->get('photoId');
        $campaignCode = $request->get('campaignCode');

        /** @var Member $member */
        $member = $this->tokenStorage->getToken()->getUser();

        /** @var Page $viewPage */
        $viewPage = $this->pageRepository->findOneBy([
            'eventId' => $eventId,
            'pageId'  => $pageId,
        ]);
        if (!$viewPage instanceof Page) {
            throw new NotFoundHttpException(sprintf('Not found pages, routing: %s, eventId: %s, pageId: %s, storeId: %s',
                $routeInfo['_route'],
                $eventId,
                $pageId,
                $this->storeId
            ));
        }

        /** @var Photo $photo */
        $photo = $this->photoRepository->find($photoId);
        if (!$photo instanceof Photo) {
            throw new NotFoundHttpException(sprintf('Not found photo, routing: %s, photoId: %s',
                $routeInfo['_route'],
                $photoId
            ));
        }

        if ($photo->getCampaignCode() !== $campaignCode) {
            throw new AccessDeniedException();
        }

        // キャンペーンが実施中かを確認
        if (!$this->sponsredPrintCheckService->checkCampaignInService($viewPage)) {
            throw new AccessDeniedException();
        }

        // 応募済み判定をしないActionを制御
        if (in_array($routeInfo['_route'], $this->notOrderedCheckRoutings)){
            return;
        }

        // 応募済みかを確認
        if (!$this->sponsredPrintCheckService->checkCampaignOrdered($viewPage, $member)) {
            $orderedAction = $this->router->generate('photocreate_event.sponsored_print.ordered', [
                'eventId'      => $eventId,
                'pageId'       => $pageId,
                'photoId'      => $photoId,
                'campaignCode' => $campaignCode,
            ]);
            $event->setController(function() use ($orderedAction) {
                return new RedirectResponse($orderedAction);
            });
        }
    }
}
