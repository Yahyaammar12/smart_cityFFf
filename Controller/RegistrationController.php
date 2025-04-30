<?php
// src/Controller/RegistrationController.php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType; // Assure-toi que ton formulaire UserType existe
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface; // Ajoute cette ligne
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface; // Utilise ce service pour l'encodage du mot de passe
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface; // Cette ligne n'est plus nécessaire avec Symfony 5.3+ si tu utilises UserPasswordHasherInterface

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // 🔐 Encoder le mot de passe
            $plainPassword = $form->get('motDePasse')->getData();
            $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
            $user->setMotDePasse($hashedPassword);

            // Set default values
            $user->setCreatedAt(new \DateTime());
            $user->setActif(true);
            $user->setIsverified(true);
            
            // If no role is set, default to USER
            if (!$user->getRole()) {
                $user->setRole('CLIENT');
            }

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Votre compte a été créé avec succès! Vous pouvez maintenant vous connecter.');
            return $this->redirectToRoute('login');
        }

        return $this->render('registration/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
