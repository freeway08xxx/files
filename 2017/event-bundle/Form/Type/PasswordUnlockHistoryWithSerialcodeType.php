<?php

namespace Photocreate\EventBundle\Form\Type;

use Photocreate\ResourceBundle\Entity\Page;
use Photocreate\ResourceBundle\Entity\PasswordUnlockHistory;
use Photocreate\ResourceBundle\Entity\Serialcode;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class PasswordUnlockHistoryWithSerialcodeType
 *
 * @package Photocreate\EventBundle\Form\Type
 */
class PasswordUnlockHistoryWithSerialcodeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $doctrine = $options['doctrine'];
        $member   = $options['member'];
        $storeId  = $options['store_id'];
        $albumPasswordUnlocker = $options['album_password_unlocker'];

        $builder
            ->add('code1', TextType::class, [
                'mapped' => false,
                'constraints' => [
                    new NotBlank(),
                    new Length(['max' => 4]),
                ],
            ])
            ->add('code2', TextType::class, [
                'mapped' => false,
                'constraints' => [
                    new NotBlank(),
                    new Length(['max' => 4]),
                ],
            ])
            ->add('code3', TextType::class, [
                'mapped' => false,
                'constraints' => [
                    new NotBlank(),
                    new Length(['max' => 4]),
                ],
            ])
            ->add('code4', TextType::class, [
                'mapped' => false,
                'constraints' => [
                    new NotBlank(),
                    new Length(['max' => 4]),
                ],
            ])
            ->addEventListener(FormEvents::POST_SUBMIT, function(FormEvent $event) use ($doctrine, $member, $storeId, $albumPasswordUnlocker) {
                // TODO: リファクタリング
                // TODO: pcserialcodes.unlock_count は解除数で、カウントアップしないといけない？要調査。
                $form = $event->getForm();
                $serialcodeAsString = sprintf(
                    '%s%s%s%s',
                    $form->get('code1')->getData(),
                    $form->get('code2')->getData(),
                    $form->get('code3')->getData(),
                    $form->get('code4')->getData()
                );

                /** @var Serialcode $serialcode */
                $serialcode = $doctrine->getRepository('Resource:Serialcode')->findOneBy([
                    'serialcode' => $serialcodeAsString,
                ]);

                if (!$serialcode) {
                    $event->getForm()->get('code1')->addError(new FormError('お客様専用パスワードを正しく入力してください。'));
                    return;
                }

                /** @var Page $page */
                $page = $doctrine->getRepository('Resource:Page')->findOneBy([
                    'eventId'         => $serialcode->getEventId(),
                    'storeId'         => $storeId,
                    'photoSearchType' => Page::PHOTO_SEARCH_TYPE_SERIALCODE,
                    'passwordType'    => Page::PASSWORD_TYPE_SERIALCODE,
                ]);

                if (!$page) {
                    $event->getForm()->get('code1')->addError(new FormError('お客様専用パスワードを正しく入力してください。'));
                    return;
                }

                /** @var PasswordUnlockHistory $data */
                $passwordUnlockHistory = $event->getForm()->getData();
                $passwordUnlockHistory->setPageId($page->getId());
                $passwordUnlockHistory->setMemberId($member->getId());
                $passwordUnlockHistory->setSerialcodeId($serialcode->getId());
                $passwordUnlockHistory->setSerialcode($serialcode);
                $passwordUnlockHistory->setEvent($page);
                if ($albumPasswordUnlocker->hasSameUnlockHistory($passwordUnlockHistory)) {
                    $passwordUnlockHistory->setRepeatFlag(true);
                } else {
                    $passwordUnlockHistory->setRepeatFlag(false);
                }
            })
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'              => PasswordUnlockHistory::class,
            'doctrine'                => '',
            'member'                  => '',
            'store_id'                => '',
            'album_password_unlocker' => '',
        ]);
    }
}
