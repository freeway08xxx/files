<?php

namespace Photocreate\EventBundle\Form\Type;

use Photocreate\ResourceBundle\Entity\EmailQueue;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class WaitingEmailQueueType
 *
 * @package Photocreate\EventBundle\Form\Type
 */
class WaitingEmailQueueType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('item1', ChoiceType::class, [
                'label' => 'イベント参加者とのご関係',
                'choices'  => [
                    1 => '本人',
                    2 => '家族',
                    3 => 'イベント関係者',
                    4 => '無関係',
                ],
                'expanded' => true,
                'constraints' => [
                    new NotBlank(['message' => 'イベント参加者とのご関係を選択してください']),
                ],
            ])->addEventListener(FormEvents::POST_SUBMIT, function(FormEvent $event) {
                $form = $event->getForm();
                $item1 = $form->get('item1')->getData();

                // 無関係を選択した場合はエラーとする
                if ($item1 == 4) {
                    $event->getForm()->get('item1')->addError(new FormError('イベント参加者と無関係の方はご覧いただけません。'));
                    return;
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
            'data_class' => EmailQueue::class,
        ]);
    }
}
