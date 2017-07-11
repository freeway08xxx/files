<?php

namespace Photocreate\EventBundle\EventListener;

use Doctrine\Common\Annotations\AnnotationReader;
use Photocreate\ResourceBundle\Entity\View\Event;
use Photocreate\ResourceBundle\Repository\PageRepository;
use Photocreate\EventBundle\Annotations\LockByPasswordUnlockHistory;
use Photocreate\EventBundle\Exception\AccessDeniedException;
use Photocreate\EventBundle\Service\AlbumPasswordUnlocker;
use Photocreate\EventBundle\Service\UnlockPageChecker;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class LockByPasswordUnlockHistorySubscriber
 *
 * イベントの閲覧パスワードを解除していない場合にリダイレクト処理を行いアクセスを拒否する。
 * LockByPasswordUnlockHistory アノテーションを宣言して利用する。
 *
 * <非会員対応>
 * 会員限定の動作を想定しており非会員の場合は利用できない。（非会員の場合は、DB ではなくセッション管理になりそう）
 * もし非会員に対応するために拡張する場合は以下を行うと良さそう。
 *
 * 1) password_unlock_histories のテーブルに not_member_hash を登録できるようする（カラム追加）。
 * 2) 閲覧パスワード解除時に password_unlock_histories に member_id = 0 と not_member_hash を登録する。
 * 3) $this->checker->checkUnlock() の検索条件に not_member_hash を加える。
 *    そうすると以下のように会員非会員どちらもこのクラスにおいては取扱が等しくなる。（会員、非会員を意識しなくていい）
 *
 *    - 会員  : member_id: $user->getId(),                   not_member_hash: $user->getNotMemberHash() (必ず空文字になる)
 *    - 非会員: member_id: $user->getId() （必ず 0 になる）, not_member_hash: $user->getNotMemberHash() (必ずランダムなハッシュ値になる)
 *
 * @package Photocreate\EventBundle\EventListener
 */
class LockByPasswordUnlockHistorySubscriber implements EventSubscriberInterface
{
    /** @var AnnotationReader  */
    protected $reader;

    /** @var UnlockPageChecker  */
    protected $checker;

    /** @var TokenStorageInterface  */
    protected $tokenStorage;

    /** @var Router  */
    protected $router;

    /** @var string  */
    protected $redirectRoutingName;

    /** @var AlbumPasswordUnlocker  */
    protected $unlocker;

    /** @var PageRepository  */
    protected $pageRepository;

    /**
     * LockByPasswordUnlockHistorySubscriber constructor.
     *
     * @param AnnotationReader      $reader
     * @param UnlockPageChecker     $checker
     * @param TokenStorageInterface $tokenStorage
     * @param Router                $router
     * @param string                $redirectRoutingName
     * @param AlbumPasswordUnlocker $unlocker
     * @param PageRepository        $pageRepository
     */
    public function __construct(
        AnnotationReader $reader,
        UnlockPageChecker $checker,
        TokenStorageInterface $tokenStorage,
        Router $router,
        string $redirectRoutingName,
        AlbumPasswordUnlocker $unlocker,
        PageRepository $pageRepository
    )
    {
        $this->reader              = $reader;
        $this->checker             = $checker;
        $this->tokenStorage        = $tokenStorage;
        $this->router              = $router;
        $this->redirectRoutingName = $redirectRoutingName;
        $this->unlocker            = $unlocker;
        $this->pageRepository      = $pageRepository;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => [
                ['onKernelController', 30],
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
        $classAnnotation  = $this->reader->getClassAnnotation($object, LockByPasswordUnlockHistory::class);
        $methodAnnotation = $this->reader->getMethodAnnotation($method, LockByPasswordUnlockHistory::class);

        if (!$classAnnotation && !$methodAnnotation) {
            // アノテーションがない場合はアクセスを許可
            return;
        }

        $request = $event->getRequest();
        $pageId  = $request->get('pageId');
        $eventId = $request->get('eventId');
        $tag     = $request->get('tag', null);
        $tagCode = $request->get('tag_code', null);

        // シリアルコード検索のパラメーターが存在する場合はシリアルコードをセット
        $serialcode = null;
        if ($tagCode === Event::PHOTO_SEARCH_TYPE_SERIALCODE) {
            $serialcode = $tag;
        }

        if (null === $token = $this->tokenStorage->getToken()) {
            throw new AccessDeniedException('Access denied at unlock page checking. Must be login when this check.');
        }

        if (!is_object($user = $token->getUser())) {
            throw new AccessDeniedException('Access denied at unlock page checking. Must be not anonymous authentication.');
        }

        // 閲覧パスワード解除履歴のチェック
        // TODO: 区分別パスワードの実装
        $albumId = null;
        $userId = $user->getId();
        if (!$this->checker->checkUnlock($userId, $pageId, $albumId, $serialcode)) {
            // 閲覧パスワードが設定されていないイベントの場合は強制解除
            $page = $this->pageRepository->findOneBy(['id' => $pageId]);
            if ($this->unlocker->unlockIfNonePasswordTypePage($page, $user)) {
                // 閲覧パスワード解除しアクセスを許可
                return;
            }

            // 閲覧パスワードを解除していないためリダイレクト
            $redirectUri = $this->router->generate($this->redirectRoutingName, ['eventId' => $eventId, 'pageId' => $pageId]);
            $event->setController(function() use ($redirectUri) {
                return new RedirectResponse($redirectUri);
            });
        }

        // 閲覧パスワード解除履歴があるためアクセスを許可
        return;
    }
}
