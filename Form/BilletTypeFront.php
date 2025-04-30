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





class BilletTypeFront extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date_voyage', DateType::class, [
                'widget' => 'single_text',
                'html5'=>true,
                'attr'=>['min'=>(new DateTime())->format('Y-m-d')],
            ])
            ->add('payment_status', ChoiceType::class, [
                'choices' => [
                    'Cash' => 'CASH',
                    'Card' => 'CARD',
                ],
                'placeholder' => 'Veuillez choisir le Statut de Paiement.',
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
