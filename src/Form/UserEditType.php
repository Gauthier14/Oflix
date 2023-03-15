<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    // libellé, valeur
                    'Membre' => 'ROLE_USER',
                    'Manager' => 'ROLE_MANAGER',
                    'Administrateur' => 'ROLE_ADMIN',
                ],
                // choix multiple, parce que roles = tableau PHP
                'multiple' => true,
                // plusieurs "widgets" HTML
                'expanded' => true,

                'label' => 'Rôle(s)',
                'label_attr' => [
                    'class' => 'checkbox-inline',
                ],
            ])
            ->add('password', PasswordType::class, [
                // ce champ n'est plus "mappé" entre le form et l'entité
                // le form ne mettra pas à jour automatiquement ce champ dans l'entité
                // @see https://symfony.com/doc/current/reference/forms/types/text.html#mapped
                'mapped' => false,
                'attr' => [
                    'placeholder' => 'Laissez vide si inchangé...',
                ],
                'help' => 'Make sure it\'s at least 15 characters OR at least 8 characters including a number and a lowercase letter.',
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
