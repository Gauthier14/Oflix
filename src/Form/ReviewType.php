<?php

namespace App\Form;

use App\Entity\Review;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class , [
                'label' => 'Nom d\'utilisateur',
            ])
            ->add('email', EmailType::class, [
                'label' => 'Courriel',
            ] )
            ->add('content', TextareaType::class, [
                'label' => 'Critique',
            ])
            ->add('rating', ChoiceType::class, [
                'label' => 'Avis',
                'placeholder' => 'Votre Choix...',
                'choices' => [
                    'Excellent' => 5,
                    'Très bon' => 4,
                    'Bon' => 3,
                    'Peut mieux faire' => 2,
                    'A éviter' => 1,
                ]
            ])
            ->add('reactions', ChoiceType::class, [
                'label' => 'Ce film vous a fait',
                'choices' => [
                    'Rire' => 'rire',
                    'Pleurer' => 'pleurer',
                    'Réfléchir' => 'réfléchir',
                    'Dormir' => 'dormir',
                    'Rêver' => 'rêver',
                ],
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('watchedAt', DateType::class, [
                'label' => 'Vous avez vu ce film le',
                'input' => 'datetime_immutable',
                'years' => range(date('Y'), 1980),
            ])
            // ->add('movie')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Review::class,
        ]);
    }
}
