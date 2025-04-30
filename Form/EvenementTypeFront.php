<?php

namespace App\Form;

use App\Entity\Evenement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use DateTime;


class EvenementTypeFront extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom',TextType::class, [
                'attr' => [
                    'placeholder' => 'Nom',
                ]
            ])
            ->add('description',TextareaType::class, [
                'attr' => [
                    'placeholder' => 'Description',
                ]
            ])
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'html5'=>true,
                'attr'=>['min'=>(new DateTime())->format('Y-m-d')],
            ])
            ->add('heure',TimeType::class, [
                'widget' => 'single_text', // Optional: makes it a single input field with a time picker
                'attr' => [
                    'placeholder' => 'Heure',
                ]
            ])
            ->add('localisation',TextType::class, [
                'attr' => [
                    'placeholder' => 'Localisation',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Evenement::class,
        ]);
    }
}
