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
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


class RegistrationController extends AbstractController
{
  
    #[Route('/register', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
        MailerInterface $mailer,
        UrlGeneratorInterface $urlGenerator
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('motDePasse')->getData();
            $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
            $user->setMotDePasse($hashedPassword);
            $user->setCreatedAt(new \DateTime());
            $user->setActif(true);
            $user->setIsverified(false);
    
            if (!$user->getRole()) {
                $user->setRole('CLIENT');
            }
    
            $em->persist($user);
            $em->flush();
    
            $verificationLink = $urlGenerator->generate('app_verify_user', [
                'id' => $user->getId()
            ], UrlGeneratorInterface::ABSOLUTE_URL);
    
            $email = (new Email())
                ->from('amaltr249@gmail.com')
                ->to($user->getEmail())
                ->subject('Activation de votre compte')
                ->html("<p>Bienvenue ! Cliquez sur ce lien pour activer votre compte :</p><a href='{$verificationLink}'>Activer mon compte</a>");
    
            $mailer->send($email);
    
            $this->addFlash('success', 'Votre compte a été créé ! Vérifiez votre email pour l’activer.');
            return $this->redirectToRoute('login');
        }
    
        return $this->render('registration/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    

    #[Route('/verify/user/{id}', name: 'app_verify_user')]
public function verifyUser(int $id, EntityManagerInterface $em): Response
{
    $user = $em->getRepository(User::class)->find($id);

    if (!$user) {
        throw $this->createNotFoundException("Utilisateur non trouvé.");
    }

    $user->setIsverified(true);
    $em->flush();

    $this->addFlash('success', 'Votre compte est maintenant activé. Vous pouvez vous connecter.');
    return $this->redirectToRoute('login');
}

}
