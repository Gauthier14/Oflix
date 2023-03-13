<?php

namespace App\Form;

use App\Entity\Genre;
use App\Entity\Movie;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class MovieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre du film',
            ])
            ->add('releaseDate', DateType::class, [
                'label' => 'Date de sortie',
                'placeholder' => [
                    'year' => 'Année', 'month' => 'Mois', 'day' => 'Jour',],
                'years' => range(date('Y'), 1900),
            ])
            ->add('duration', NumberType::class, [
                'label' => 'Durée'
            ])
            ->add('summary', TextareaType::class, [
                'label' => 'Résumé',
                'help' => 'Environ 150 caractères.',
            ])
            ->add('synopsis', TextareaType::class, [
                'label' => 'Synopsis',
                'help' => '4000 caractères max.',
            ])
            ->add('poster', UrlType::class, [
                'label' => 'Affiche',
                'attr' => ['placeholder' => 'par ex. htttps:\\...']
            ])
            // le rating sera calculé sur la base des reviews 
            // ->add('rating', IntegerType::class, [
            //     'label' => 'Note'
            //     ]) me
            ->add('type', ChoiceType::class, [
                'label' => 'Type',
                'placeholder' => 'Votre Choix...',
                'choices' => [
                    'Film' => 'Film',
                    'Série' => 'Série',
                ],
                'multiple' => false,
                'expanded' => true,
            ])
            ->add('genres', EntityType::class, [
                'class' => Genre::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'query_builder' => function (EntityRepository $er) {
                    // requête écrite ici, ou appelée depuis le Repository de l'entité concernée
                    return $er->createQueryBuilder('g')
                        ->orderBy('g.name', 'ASC');
                },
                'label_attr' => [
                    'class' => 'checkbox-inline',
                ],

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Movie::class,
        ]);
    }
}
