<?php

namespace Photocreate\EventBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Photocreate\ResourceBundle\Entity\AlbumPassword;
use Photocreate\ResourceBundle\Entity\Page;
use Photocreate\ResourceBundle\Entity\PasswordUnlockHistory;
use Photocreate\ResourceBundle\Entity\Security\Member;
use Photocreate\ResourceBundle\Entity\Serialcode;
use Photocreate\ResourceBundle\Repository\AlbumPasswordRepository;
use Photocreate\ResourceBundle\Repository\PasswordUnlockHistoryRepository;

/**
 * TODO: クソコードなので修正する
 * TODO: 個別パス対応
 *
 * Class AlbumPasswordUnlocker
 *
 * @package Photocreate\EventBundle\Service
 */
class AlbumPasswordUnlocker
{
    /** @var AlbumPasswordRepository  */
    protected $albumPasswordRepository;

    /** @var PasswordUnlockHistoryRepository  */
    protected $passwordUnlockHistoryRepository;

    /** @var EntityManagerInterface  */
    protected $entityManager;

    /**
     * AlbumPasswordUnlocker constructor.
     *
     * @param AlbumPasswordRepository         $albumPasswordRepository
     * @param PasswordUnlockHistoryRepository $passwordUnlockHistoryRepository
     * @param EntityManagerInterface          $entityManager
     */
    public function __construct(
        AlbumPasswordRepository $albumPasswordRepository,
        PasswordUnlockHistoryRepository $passwordUnlockHistoryRepository,
        EntityManagerInterface $entityManager
    )
    {
        $this->albumPasswordRepository = $albumPasswordRepository;
        $this->passwordUnlockHistoryRepository = $passwordUnlockHistoryRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @param Page            $page
     * @param Member          $member
     * @param string          $password
     * @param Serialcode|null $serialcode
     *
     * @return bool
     */
    public function unlock(Page $page, Member $member, $password, Serialcode $serialcode = null)
    {
        if ($serialcode) {
            if ($page->getEventId() === $serialcode->getEventId()) {
                if ($this->registerPasswordUnlockHistory($page, $member, $serialcode)) {
                    return true;
                }
            }
            return false;
        }

        /** @var $albumPassword */
        $albumPasswords = $this->albumPasswordRepository->findBy([
            'pageId'   => $page->getId(),
        ]);

        foreach ($albumPasswords as $albumPassword) {
            if ($albumPassword->getPassword() === $password) {
                if ($this->registerPasswordUnlockHistory($page, $member, $albumPassword)) {
                    return true;
                }

                // 登録失敗の例外処理
                return false;
            }

            continue;
        }

        return false;
    }

    /**
     * pages.password_type === none の場合はパスワード設定がないため強制的に解除してよい
     *
     * @param Page   $page
     * @param Member $member
     *
     * @return bool
     */
    public function unlockIfNonePasswordTypePage(Page $page, Member $member)
    {
        if ($page->isNonePasswordType()) {
            // 閲覧パスワードが設定されていない場合は解除
            if ($this->registerPasswordUnlockHistory($page, $member)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Page               $page
     * @param Member             $member
     * @param AlbumPassword|null $albumPassword
     * @param Serialcode         $serialcode
     *
     * @return bool
     */
    private function registerPasswordUnlockHistory(
        Page $page,
        Member $member,
        AlbumPassword $albumPassword = null,
        Serialcode $serialcode = null
    )
    {
        $pageId = $page->getId();
        $memberId = $member->getId();
        $rootAlbumId = $page->getRootAlbumId();

        $serialcodeId = null;
        if ($serialcode) {
            $serialcodeId = $serialcode->getId();
        }

        $passwordUnlockHistory = new PasswordUnlockHistory();
        $passwordUnlockHistory->setPageId($pageId);
        $passwordUnlockHistory->setAlbumId($rootAlbumId);
        $passwordUnlockHistory->setMemberId($memberId);
        $passwordUnlockHistory->setSerialcodeId($serialcodeId);

        if ($albumPassword) {
            $albumPasswordId = $albumPassword->getId();
            $passwordUnlockHistory->setAlbumPasswordId($albumPasswordId);
        }

        $repeatFlag = $this->hasSameUnlockHistory($passwordUnlockHistory);
        $passwordUnlockHistory->setRepeatFlag($repeatFlag);

        $this->entityManager->persist($passwordUnlockHistory);
        $this->entityManager->flush();

        return true;
    }

    /**
     * @param Page $page
     *
     * @return bool
     */
    public function hasAlbumPassword(Page $page)
    {
        $pageId = $page->getId();
        $albumPasswords = $this->albumPasswordRepository->findBy([
            'pageId' => $pageId,
        ]);

        return !empty($albumPasswords);
    }

    /**
     * @param PasswordUnlockHistory $passwordUnlockHistory
     *
     * @return bool
     */
    public function hasSameUnlockHistory(PasswordUnlockHistory $passwordUnlockHistory)
    {
        $result = $this->passwordUnlockHistoryRepository->findOneBy([
            'pageId'             => $passwordUnlockHistory->getPageId(),
            'albumId'            => $passwordUnlockHistory->getAlbumId(),
            'albumPasswordId'    => $passwordUnlockHistory->getAlbumPasswordId(),
            'memberId'           => $passwordUnlockHistory->getMemberId(),
            'serialcodeId'       => $passwordUnlockHistory->getSerialcodeId(),
            'masterPasswordFlag' => $passwordUnlockHistory->getMasterPasswordFlag(),
        ]);

        return !empty($result);
    }
}