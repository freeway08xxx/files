<?php

namespace Photocreate\EventBundle\Service;

use Photocreate\MemberBundle\Entity\Member;
use Photocreate\ResourceBundle\Criteria\SearchOrderCriteriaBuilder;
use Photocreate\ResourceBundle\Criteria\SearchSponsoredOrderCriteriaBuilder;
use Photocreate\ResourceBundle\Entity\Order;
use Photocreate\ResourceBundle\Entity\View\Page;
use Photocreate\ResourceBundle\Usecase\SearchOrderUsecase;
use Photocreate\ResourceBundle\Usecase\SearchSponsoredOrderUsecase;

/**
 * Class SponsredPrintCheckService
 * @package Photocreate\EventBundle\Service
 */
class SponsredPrintCheckService
{
    /** @var SearchSponsoredOrderUsecase */
    protected $searchSponsoredOrderUsecase;

    /** @var SearchOrderUsecase */
    protected $searchOrderUsecase;

    /**
     * AccessControlByPageOpenStatusSubscriber constructor.
     *
     * @param EventRepository $repository
     * @param PreviewModeSessionStorage $previewModeSessionStorage
     */
    public function __construct(
        SearchSponsoredOrderUsecase $searchSponsoredOrderUsecase,
        SearchOrderUsecase $searchOrderUsecase
    ) {
        $this->searchSponsoredOrderUsecase = $searchSponsoredOrderUsecase;
        $this->searchOrderUsecase          = $searchOrderUsecase;
    }

    /**
     * キャンペーンが実施中かを確認する
     *
     * @param Page $page
     * @return bool
     */
    public function checkCampaignInService(Page $page): bool
    {
        if (!$this->checkCampaignRange($page)) {
            return false;
        }

        if (!$this->checkCampaignMaxApplied($page)) {
            return false;
        }

        return true;
    }

    /**
     * キャンペーンが期間内かを確認
     *
     * @param Page $page
     * @return bool
     */
    private function checkCampaignRange(Page $page): bool
    {
        if (!$page->getCampaignStartTs()) {
            return true;
        }

        if (!$page->getCampaignEndTs()) {
            return true;
        }

        $now = new \DateTime();

        if ($now < $page->getCampaignStartTs()) {
            return false;
        }

        if ($page->getCampaignEndTs() < $now) {
            return false;
        }

        return true;
    }

    /**
     * キャンペーンに注文済みかを確認
     *
     * @param Page $page
     * @param Member $member
     * @return bool
     */
    public function checkCampaignOrdered(Page $page, Member $member): bool
    {
        $criteria = (new SearchSponsoredOrderCriteriaBuilder())
            ->setCampaignCode($page->getCampaignCode())
            ->setMemberId($member->getId())
            ->isNotSearchSoftDeleted()
            ->build();

        /** @var Collection $sponsoredOrderCollection */
        $sponsoredOrderCollection = $this->searchSponsoredOrderUsecase->run($criteria);
        if ($sponsoredOrderCollection->isEmpty()) {
            return true;
        }

        $orderNumList = [];
        /** @var SponsoredOrder $sponsoredOrder */
        foreach ($sponsoredOrderCollection as $sponsoredOrder) {
            $orderNumList[] = $sponsoredOrder->getOrderNum();
        }

        $criteria = (new SearchOrderCriteriaBuilder())->setNums($orderNumList)->build();
        $orderCollection = $this->searchOrderUsecase->run($criteria);
        /** @var Order $order */
        foreach ($orderCollection as $order) {
            // キャンセルされた注文は除く
            if(in_array($order->getStatusCode(),[Order::STATIS_CODE_CANCEL, Order::STATIS_CODE_CANCEL_RETURNED])){
                continue;
            }
            return false;
        }

        return true;
    }

    /**
     * キャンペーンの応募上限に達しているかを確認
     *
     * @param Page $page
     * @return bool
     */
    private function checkCampaignMaxApplied(Page $page): bool
    {
        $criteria = (new SearchSponsoredOrderCriteriaBuilder())
            ->setCampaignCode($page->getCampaignCode())
            ->isNotSearchSoftDeleted()
            ->build();

        /** @var Collection $sponsoredOrderCollection */
        $sponsoredOrderCollection = $this->searchSponsoredOrderUsecase->run($criteria);

        if ($sponsoredOrderCollection->count() >= $page->getCampaignMaxApplied()) {
            return false;
        }

        return true;
    }
}
