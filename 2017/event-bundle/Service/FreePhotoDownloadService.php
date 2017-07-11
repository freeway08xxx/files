<?php

namespace Photocreate\EventBundle\Service;

use Doctrine\ORM\EntityManager;
use Photocreate\ResourceBundle\Entity\View\Page;

/**
 * Class FreePhotoDownload
 * @package Photocreate\EventBundle\Service
 */
class FreePhotoDownloadService
{
    /** @var  EntityManager $entityManager */
    private $entityManager;
    /** @var array $freePhotoDownloadSubcategoryIds */
    private $freePhotoDownloadSubcategoryIds;

    /**
     * FreePhotoDownload constructor.
     * @param array $freePhotoDownloadSubcategoryIds
     */
    public function __construct(
        EntityManager $entityManager,
        array $freePhotoDownloadSubcategoryIds
    ) {
        $this->entityManager                   = $entityManager;
        $this->freePhotoDownloadSubcategoryIds = $freePhotoDownloadSubcategoryIds;
    }

    /**
     * @param int $eventId
     * @return bool
     */
    public function isFreePhotoDownloadEvent(int $eventId): bool
    {
        /** @var Page $page */
        $page = $this->entityManager->getRepository('Resource:View\Page')->findOneBy(['eventId' => $eventId]);

        assert($page instanceof Page);

        if (!in_array($page->getSubcategoryId(), $this->freePhotoDownloadSubcategoryIds)) {
            return false;
        }

        if ($page->getPhotoSearchType() !== Page::PHOTO_SEARCH_TYPE_SERIALCODE) {
            return false;
        }

        return true;
    }
}
