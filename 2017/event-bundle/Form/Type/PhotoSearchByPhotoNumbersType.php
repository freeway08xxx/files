<?php

namespace Photocreate\EventBundle\Form\Type;

use Photocreate\EventBundle\Entity\PhotoNumbersQuery;
use Photocreate\EventBundle\Form\DataTransformer\PhotoNumbersStringToHttpQueryStringTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class PhotoSearchByPhotoNumbersType
 *
 * @package Photocreate\EventBundle\Form\Type
 */
class PhotoSearchByPhotoNumbersType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('query', TextareaType::class, [
                'constraints' => [
                    new NotBlank(['message' => '写真番号を入力してください']),
                ],
            ])
        ;

        $builder->get('query')->addModelTransformer(new PhotoNumbersStringToHttpQueryStringTransformer());
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(
                [
                    'data_class' => PhotoNumbersQuery::class,
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
