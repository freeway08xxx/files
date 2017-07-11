<?php

namespace Photocreate\EventBundle\Form\Type;

use Photocreate\ResourceBundle\Entity\EmailQueue;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class PhotoDeleteRequestType
 *
 * @package Photocreate\EventBundle\Form\Type
 */
class PhotoDeleteRequestType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'お名前',
                'constraints' =>
                    [
                        new NotBlank(['message' => 'お名前を入力してください']),
                    ],
            ])
            // TODO: メールアドレスは会員情報から取得できるため不要？
            ->add('email', EmailType::class, [
                'label' => 'メールアドレス',
                'constraints' =>
                    [
                        new NotBlank(['message' => 'メールアドレスを入力してください']),
                    ],
            ])
            ->add('item1', TextareaType::class, [
                'label' => '削除理由',
                'constraints' =>
                    [
                        new NotBlank(['message' => '削除理由を入力してください']),
                    ],
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(
                [
                    'data_class' => EmailQueue::class,
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
