<?php

namespace App\Form;

use App\Entity\Centremedicale;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class CentremedicaleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom',TextType::class, [
                'attr' => [
                    'placeholder' => 'Nom',
                ]
            ])
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Hospital' => 'HOSPITAL',
                    'Clinic' => 'CLINIC',
                    'Pharmacy' => 'PHARMACY',
                ],
                'placeholder' => 'Veuillez choisir le Type.',
            ])
            ->add('localisation',TextType::class, [
                'attr' => [
                    'placeholder' => 'Localisation',
                ]
            ])
            ->add('disponibilite', ChoiceType::class, [
                'choices' => [
                    'Yes' => '1',
                    'No' => '0',
                ],
                'placeholder' => 'Veuillez choisir la disponibilité',
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Centremedicale::class,
        ]);
    }
}
