<?php

namespace Photocreate\EventBundle\Service;

use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class PreviewModeSessionStorage
 *
 * プレビューモード（イベント公開前でもイベント公開しているかのように写真を閲覧・購入できる）
 * の対象ページを管理するセッションストレージ
 *
 * @package Photocreate\EventBundle\Service
 */
class PreviewModeSessionStorage
{
    const KEY = 'preview_mode';

    /** @var null|\Symfony\Component\HttpFoundation\Session\SessionInterface  */
    private $session;

    /**
     * PreviewModeSessionStorage constructor.
     *
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->session = $requestStack->getCurrentRequest()->getSession();
    }

    /**
     * @param $pageId
     */
    public function register($pageId)
    {
        $pageIds = $this->session->get(self::KEY, []);
        $pageIds[] = $pageId;

        $this->session->set(self::KEY, $pageIds);
    }

    /**
     * @param $pageId
     *
     * @return bool
     */
    public function isAllowPreviewMode($pageId)
    {
        if (in_array($pageId, $this->session->get(self::KEY, []))) {
            return true;
        }

        return false;
    }
}