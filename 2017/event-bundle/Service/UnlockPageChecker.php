<?php

namespace Photocreate\EventBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Photocreate\ResourceBundle\Entity\Serialcode;

/**
 * Class UnlockPageChecker
 *
 * 閲覧パスワードを解除しているかどうかをチェックする。
 * 閲覧パスワードを解除している状態とは以下を指す。
 *
 *   password_unlock_histories に、
 *   アクセスしている会員ID(member_id)とページID(page_id)とシリアルコードID(pcserialcode_id)のレコードが存在している
 *
 * @package Photocreate\EventBundle\Service
 */
class UnlockPageChecker
{
    /** @var EntityManagerInterface  */
    protected $entityManager;

    /**
     * UnlockPageChecker constructor.
     *
     * @param EntityManagerInterface       $entityManager
     */
    public function __construct(
        EntityManagerInterface $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param int  $userId
     * @param int  $pageId
     * @param null $albumId
     * @param null $serialcode
     *
     * @return bool
     */
    public function checkUnlock(int $userId, int $pageId, $albumId = null, $serialcode = null): bool
    {
        $serialcodeId = null;
        if (!empty($serialcode)) {
            $serialcode = $this->entityManager->getRepository('Resource:Serialcode')
                ->findOneBy(['serialcode' => $serialcode]);
            if (!$serialcode instanceof Serialcode) {
                return false;
            }
            $serialcodeId = $serialcode->getId();
        }

        $result = $this->entityManager->getRepository('Resource:PasswordUnlockHistory')->findOneBy([
            'memberId'     => $userId,
            'pageId'       => $pageId,
            'serialcodeId' => $serialcodeId,
        ]);

        return !empty($result);
    }
}