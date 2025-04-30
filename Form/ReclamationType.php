<?php

namespace App\Form;

use App\Entity\Reclamation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use DateTime;



class ReclamationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $tourismes = $options['tourismes']; // we'll pass this from the controller

        $choices = [];
        foreach ($tourismes as $tourisme) {
            $choices[$tourisme->getNom()] = $tourisme->getId(); // "name" => id
        }

        $builder
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'html5'=>true,
                'attr'=>['min'=>(new DateTime())->format('Y-m-d')],
            ])
            ->add('sujet',TextType::class, [
                'attr' => [
                    'placeholder' => 'Sujet',
                ]
            ])
            ->add('description',TextareaType::class, [
                'attr' => [
                    'placeholder' => 'Description',
                ]
            ])
            ->add('rating',TextType::class, [
                'attr' => [
                    'placeholder' => 'Rating (De 1 à 5)',
                ]
            ])
            ->add('solved', ChoiceType::class, [
                'choices' => [
                    'Yes' => '1',
                    'No' => '0',
                ],
                'placeholder' => 'Veuillez choisir l etat de traitement',
                'required' => true,
            ])
            ->add('tourisme_id', ChoiceType::class, [
                'choices' => $choices,
                'placeholder' => 'Choisir un tourisme',
                'label' => 'Tourisme',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reclamation::class,
            'tourismes' => [], // <-- default value
        ]);
    }
}
