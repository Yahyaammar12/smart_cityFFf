<?php

namespace App\Form;

use App\Entity\Transport;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use App\Service\CountryApiService;




class TransportType extends AbstractType
{
    private CountryApiService $countryApi;

    public function __construct(CountryApiService $countryApi)
    {
        $this->countryApi = $countryApi;
    }


    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $countries = $this->countryApi->getAllCountries();

        $builder
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Bus' => 'BUS',
                    'Train' => 'TRAIN',
                    'Flight' => 'FLIGHT',
                ],
                'placeholder' => 'Veuillez choisir le Type.',
            ])
            ->add('image',FileType::class,[
                'label' => 'Choisir une photo',
                'data_class' => null,
            ])
            ->add('horaire',TimeType::class, [
                'widget' => 'single_text', // Optional: makes it a single input field with a time picker
                'required' => true,
                'attr' => [
                    'placeholder' => 'Horaire',
                ]
            ])
            ->add('tarif',TextType::class, [
                'attr' => [
                    'placeholder' => 'Tarif',
                ]
            ])
            ->add('places_libres',TextType::class, [
                'attr' => [
                    'placeholder' => 'Places Libres',
                ]
            ])
            ->add('depart', ChoiceType::class, [
                'label' => 'Pays de départ',
                'choices' => array_combine($countries, $countries),
                'placeholder' => 'Choisir un pays départ'
            ])
            ->add('destination', ChoiceType::class, [
                'label' => 'Pays d\'arrivée',
                'choices' => array_combine($countries, $countries),
                'placeholder' => 'Choisir un pays destination'
            ])
            ->add('company',TextType::class, [
                'attr' => [
                    'placeholder' => 'Company',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Transport::class,
        ]);
    }
}
