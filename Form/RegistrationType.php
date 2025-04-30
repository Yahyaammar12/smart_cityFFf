<?php
namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class RegistrationType extends AbstractType
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
            ->add('motDePasse', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Confirmer le mot de passe'],
                'invalid_message' => 'Les mots de passe ne correspondent pas.',
                'attr' => ['placeholder' => 'Entrez votre mot de passe'],
            ])
            ->add('role', ChoiceType::class, [
                'choices' => [
                    'Patient' => 'PATIENT',
                    'Medecin' => 'MEDECIN',
                    'Client' => 'CLIENT',
                ],
                'placeholder' => 'Veuillez choisir le Role.',
            ]);
            // Supprime 'created_at' si tu veux le gérer automatiquement dans ton contrôleur
            // ->add('created_at')
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
