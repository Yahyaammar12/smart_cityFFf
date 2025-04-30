<?php

namespace App\Form;

use App\Entity\Rendezvou;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use DateTime;



class RendezvouTypeFront extends AbstractType
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Fetch users with the "MEDECIN" role
        $medecins = $this->userRepository->findByRole('MEDECIN');

        $choices = [];
        foreach ($medecins as $medecin) {
            $choices[$medecin->getNom() . ' ' . $medecin->getPrenom()] = $medecin->getNom() . ' ' . $medecin->getPrenom();
        }


        $builder
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
            ->add('nomMedecin', ChoiceType::class, [
                'choices' => $choices,
                'placeholder' => 'Veuillez choisir un médecin',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Rendezvou::class,
        ]);
    }
}
