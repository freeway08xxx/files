<?php

namespace Photocreate\EventBundle\EventListener;

use Doctrine\Common\Annotations\AnnotationReader;
use Photocreate\EventBundle\Service\PreviewModeSessionStorage;
use Photocreate\ResourceBundle\Entity\View\Page;
use Photocreate\ResourceBundle\Repository\View\EventRepository;
use Photocreate\EventBundle\Annotations\AccessControlByPageOpenStatus;
use Photocreate\EventBundle\Exception\AccessDeniedException;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class AccessControlByPageOpenStatusSubscriber implements EventSubscriberInterface
{
    /** @var AnnotationReader  */
    protected $reader;

    /** @var Router  */
    protected $router;

    /** @var EventRepository  */
    protected $repository;

    /** @var PreviewModeSessionStorage  */
    protected $previewModeSessionStorage;

    /** @var int  */
    protected $storeId;

    /**
     * AccessControlByPageOpenStatusSubscriber constructor.
     *
     * @param AnnotationReader          $reader
     * @param Router                    $router
     * @param EventRepository           $repository
     * @param PreviewModeSessionStorage $previewModeSessionStorage
     * @param int                       $storeId
     */
    public function __construct(
        AnnotationReader $reader,
        Router $router,
        EventRepository $repository,
        PreviewModeSessionStorage $previewModeSessionStorage,
        int $storeId
    )
    {
        $this->reader                    = $reader;
        $this->router                    = $router;
        $this->repository                = $repository;
        $this->previewModeSessionStorage = $previewModeSessionStorage;
        $this->storeId                   = $storeId;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => [
                ['onKernelController', 50],
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
        $classAnnotation  = $this->reader->getClassAnnotation($object, AccessControlByPageOpenStatus::class);
        $methodAnnotation = $this->reader->getMethodAnnotation($method, AccessControlByPageOpenStatus::class);

        if (!$classAnnotation && !$methodAnnotation) {
            // アノテーションがない場合はアクセスを許可
            return;
        }

        $request = $event->getRequest();
        $pageId  = $request->get('pageId');
        $eventId = $request->get('eventId');

        // TODO: リファクタリング、ストラテジーパターンとかで書き直せる
        $pathInfo = $request->getPathInfo();
        $routeInfo = $this->router->match($pathInfo);
        $currentRoutingName = $routeInfo['_route'];

        $closeRoutingNames = ['photocreate_event.event_close'];
        $waitingRoutingNames = [
            'photocreate_event.event_waiting.index',
            'photocreate_event.event_waiting.complete',
        ];
        $publishRoutingNames = [
            'photocreate_event.event_unlock',
            'photocreate_event.event_album',
            'photocreate_event.photo_list',
            'photocreate_event.photo_list.photoset',
            'photocreate_event.photo_list.zoom',
            'photocreate_event.photo_delete_request.form',
            'photocreate_event.photo_delete_request.complete',
            'photocreate_event.sponsored_print.index',
            'photocreate_event.sponsored_print.form',
            'photocreate_event.sponsored_print.confirm',
            'photocreate_event.sponsored_print.complete',
            'photocreate_event.sponsored_print.ordered',
        ];

        $waitingUri = $this->router->generate('photocreate_event.event_waiting.index', ['eventId' => $eventId, 'pageId' => $pageId]);
        $publishUri = $this->router->generate('photocreate_event.event_unlock', ['eventId' => $eventId, 'pageId' => $pageId]);

        /** @var  \Photocreate\ResourceBundle\Entity\View\Event $page */
        $page = $this->repository->findOneBy([
            'eventId' => $eventId,
            'pageId'  => $pageId,
            'storeId' => $this->storeId,
        ]);

        if (empty($page)) {
            throw new NotFoundHttpException(sprintf('Not found pages, routing: %s, eventId: %s, pageId: %s, storeId: %s',
                $currentRoutingName,
                $eventId,
                $pageId,
                $this->storeId
            ));
        }

        // プレビューモードの判定
        $isPreviewMode = $this->previewModeSessionStorage->isAllowPreviewMode($pageId);

        // TODO: 再掲載不可案件の対応
        if (in_array($currentRoutingName, $closeRoutingNames, true)) {
            // クローズしているとき
            if ($page->isPublish() || $page->isRepublish() || $isPreviewMode) {
                $redirectUri = $publishUri;
            } elseif ($page->isWaiting()) {
                $redirectUri = $waitingUri;
            } elseif ($page->isClose()) {
                // アクセスを許可
                return;
            }
        } elseif (in_array($currentRoutingName, $publishRoutingNames, true)) {
            // 公開しているとき
            if ($page->isPublish() || $page->isRepublish() || $isPreviewMode) {
                // アクセスを許可
                return;
            } elseif ($page->isWaiting()) {
                $redirectUri = $waitingUri;
            } elseif ($page->isClose()) {
                // TODO: クローズしているときの対応
                $redirectUri = 'hoghoge';
            }
        } elseif (in_array($currentRoutingName, $waitingRoutingNames, true)) {
            // 準備中のとき
            if ($page->isPublish() || $page->isRepublish() || $isPreviewMode) {
                $redirectUri = $publishUri;
            } elseif ($page->isWaiting()) {
                // アクセスを許可
                return;
            } elseif ($page->isClose()) {
                // TODO: クローズしているときの対応
                $redirectUri = 'hoghoge';
            }
        } else {
            // TODO: 適切なエラーハンドリング
            throw new AccessDeniedException();
        }

        $event->setController(function() use ($redirectUri) {
            return new RedirectResponse($redirectUri);
        });
    }
}
