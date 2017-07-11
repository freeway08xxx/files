<?php

namespace Photocreate\EventBundle\Form\Type;

use Photocreate\ResourceBundle\Entity\AlbumPassword;
use Photocreate\ResourceBundle\Entity\PasswordUnlockHistory;
use Photocreate\ResourceBundle\Criteria\SearchAlbumPasswordCriteriaBuilder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class PasswordUnlockHistoryType
 *
 * @package Photocreate\EventBundle\Form\Type
 */
class PasswordUnlockHistoryType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $searchAlbumPasswordUsecase = $options['search_album_password_usecase'];
        $albumPasswordUnlocker = $options['album_password_unlocker'];

        $pageId = $options['page_id'];
        $memberId = $options['member_id'];
        $masterPasswords = $options['master_passwords'];

        $builder
            // TODO: 大文字、小文字の変換
            ->add('password', PasswordType::class, [
                'mapped' => false,
                'constraints' => [
                    new NotBlank(['message' => '閲覧パスワードを正しく入力してください']),
                ],
            ])
            ->addEventListener(FormEvents::POST_SUBMIT, function(FormEvent $event) use (
                $searchAlbumPasswordUsecase,
                $albumPasswordUnlocker,
                $pageId,
                $memberId,
                $masterPasswords
            ) {
                /** @var PasswordUnlockHistory $passwordUnlockHistory */
                $passwordUnlockHistory = $event->getForm()->getData();
                $password = $event->getForm()->get('password')->getData();

                // マスターパスワードで解除した場合
                if (in_array($password, $masterPasswords)) {
                    $passwordUnlockHistory->setPageId($pageId);
                    $passwordUnlockHistory->setMemberId($memberId);
                    $passwordUnlockHistory->setMasterPasswordFlag(true);
                    if ($albumPasswordUnlocker->hasSameUnlockHistory($passwordUnlockHistory)) {
                        $passwordUnlockHistory->setRepeatFlag(true);
                    } else {
                        $passwordUnlockHistory->setRepeatFlag(false);
                    }

                    return;
                }

                $criteriaBuilder = new SearchAlbumPasswordCriteriaBuilder();
                $criteria = $criteriaBuilder
                    ->setPageId($pageId)
                    ->setPassword($password)
                    ->isAlbumIdNotNull()
                    ->build();
                $searchAlbumPasswords = $searchAlbumPasswordUsecase->run($criteria);
                $searchAlbumPasswords = $searchAlbumPasswords->getValues();
                if (empty($searchAlbumPasswords)) {
                    // パスワードが誤っている場合のエラー
                    $event->getForm()->get('password')->addError(new FormError('閲覧パスワードを正しく入力してください'));
                    return;
                }

                $albumPassword = $searchAlbumPasswords[0];
                if ($albumPassword instanceof AlbumPassword) {
                    $passwordUnlockHistory->setPageId($albumPassword->getPageId());
                    $passwordUnlockHistory->setAlbumId($albumPassword->getAlbumId());
                    $passwordUnlockHistory->setAlbumPasswordId($albumPassword->getId());
                    $passwordUnlockHistory->setMemberId($memberId);
                    if ($albumPasswordUnlocker->hasSameUnlockHistory($passwordUnlockHistory)) {
                        $passwordUnlockHistory->setRepeatFlag(true);
                    } else {
                        $passwordUnlockHistory->setRepeatFlag(false);
                    }
                }
            })
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(
                [
                    'data_class'                    => PasswordUnlockHistory::class,
                    'search_album_password_usecase' => '',
                    'album_password_unlocker'       => '',
                    'page_id'                       => '',
                    'member_id'                     => '',
                    'master_passwords'              => '',

                ]
            );
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return self::class;
    }
}
