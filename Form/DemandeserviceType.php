<?php

namespace App\Form;

use App\Entity\Demandeservice;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use DateTime;


class DemandeserviceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'html5'=>true,
                'attr'=>['min'=>(new DateTime())->format('Y-m-d')],
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Pending' => 'PENDING',
                    'Approved' => 'APPROVED',
                    'Rejected' => 'REJECTED',
                ],
                'placeholder' => 'Veuillez choisir le Statut.',
            ])
            ->add('service', EntityType::class, [
                'class' => 'App\Entity\Service',
                'choice_label' => 'nom',
                'placeholder' => 'Veuillez choisir le service.'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Demandeservice::class,
        ]);
    }
}
