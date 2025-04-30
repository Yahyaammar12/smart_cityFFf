<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType; 
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class UserTypeFront extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom',TextType::class, [
                'attr' => [
                    'placeholder' => 'Nom',
                ]
            ])
            ->add('prenom',TextType::class, [
                'attr' => [
                    'placeholder' => 'Prénom',
                ]
            ])
            ->add('email',EmailType::class, [
                'attr' => [
                    'placeholder' => 'Email',
                ]
            ])
            ->add('num_carte',TextType::class, [
                'attr' => [
                    'placeholder' => 'Numéro de Carte',
                ]
            ])
            ->add('adresse',TextType::class, [
                'attr' => [
                    'placeholder' => 'Adresse',
                ]
            ])
            ->add('motDePasse',PasswordType::class, [
                'attr' => [
                    'placeholder' => 'Mot De Passe',
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
