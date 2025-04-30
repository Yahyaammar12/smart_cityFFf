<?php

namespace App\Form;

use App\Entity\Service;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;


class ServiceType extends AbstractType
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
                    'Healthcare' => 'HEALTHCARE',
                    'Transport' => 'TRANSPORT',
                    'Tourism' => 'TOURISM',
                ],
                'placeholder' => 'Veuillez choisir le Type.',
            ])
            ->add('description',TextareaType::class, [
                'attr' => [
                    'placeholder' => 'Description',
                ]
            ])
            ->add('imagePath',FileType::class,[
                'label' => 'Choisir une photo',
                'data_class' => null,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Service::class,
        ]);
    }
}
