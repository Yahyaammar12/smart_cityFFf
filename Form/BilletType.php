<?php

namespace App\Form;

use App\Entity\Billet;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use DateTime;





class BilletType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date_voyage', DateType::class, [
                'widget' => 'single_text',
                'html5'=>true,
                'attr'=>['min'=>(new DateTime())->format('Y-m-d')],
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Confirmed' => 'CONFIRMED',
                    'Cancelled' => 'CANCELLED',
                ],
                'placeholder' => 'Veuillez choisir le Statut.',
            ])
            ->add('payment_status', ChoiceType::class, [
                'choices' => [
                    'Cash' => 'CASH',
                    'Card' => 'CARD',
                ],
                'placeholder' => 'Veuillez choisir le Statut de Paiement.',
            ])
            ->add('transport', EntityType::class, [
                'class' => 'App\Entity\Transport',
                'choice_label' => 'depart',
                'placeholder' => 'Veuillez choisir le Transport.'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Billet::class,
        ]);
    }
}
