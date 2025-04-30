<?php
// src/Controller/AuthController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpKernel\UriSigner;
use App\Repository\UserRepository;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;




class AuthController extends AbstractController
{
    #[Route('/login', name: 'login')]
    public function login(Request $request, AuthenticationUtils $authenticationUtils): Response
    {
        // If already logged in, redirect based on role
        if ($this->getUser()) {
            $roles = $this->getUser()->getRoles();
            if (in_array('ADMIN', $roles)) {
                return $this->redirectToRoute('app_home');
            }
            return $this->redirectToRoute('app_front'); // For MEDECIN, PATIENT, CLIENT, etc.
        }

        // Render the login form with possible error and last email
        $error = $authenticationUtils->getLastAuthenticationError();
        $email = $authenticationUtils->getLastUsername();
        $rememberedEmail = $request->cookies->get('remembered_email');

        return $this->render('auth/login.html.twig', [
            'error' => $error,
            'email' => $email,
            'last_email' => $rememberedEmail,
            'recaptcha_site_key' => $this->getParameter('NOCAPTCHA_SITEKEY'),
        ]);
    }

    #[Route('/logout', name: 'logout')]
    public function logout()
    {
        // Symfony gère la déconnexion automatiquement
    }



    #[Route('/forgot-password', name: 'forgot_password')]
    public function showForgotPasswordForm(Request $request, UserRepository $userRepository, UriSigner $urlSigner, MailerInterface $mailer): Response
    {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');

            if (!$email) {
                $this->addFlash('danger', 'Veuillez entrer une adresse email.');
                return $this->redirectToRoute('forgot_password');
            }

            $user = $userRepository->findOneBy(['email' => $email]);
            if ($user) {
                try {
                    // Generate and send reset link
                    $expires = (new \DateTime('+30 minutes'))->getTimestamp();
                    $url = $this->generateUrl('app_reset_password', [
                        'id' => $user->getId(),
                        'expires' => $expires,
                    ], \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_URL); // Use absolute URL
    
                    $signedUrl = $urlSigner->sign($url);
    
                    // Send email
                    $emailMessage = (new Email())
                        ->from("amaltr249@gmail.com")
                        ->to($user->getEmail())
                        ->subject('Reset your OneUrban password')
                        ->html("
                            <div style='font-family: Arial, sans-serif; border: 1px solid #ddd; border-radius: 6px; padding: 20px; max-width: 600px; margin: auto;'>
                                <p><b>One</b>Urban</p>
                                <h3 style='color:rgb(9, 146, 27);'>Reset your password</h3>
                                <p>We heard that you lost your password. Sorry about that!</p>
                                <p>But don’t worry! You can use the button below to reset your password:</p>
                                <br>
                                <p style='text-align: center; margin: 30px 0;'>
                                    <a href='{$signedUrl}' style='background-color: #28a745; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold;'>Reset your password</a>
                                </p>
                                <br>
                                <p>If you don’t use this link within 30 minutes, it will expire. To get a new password reset link, visit: 
                                    <a style='color:black;' href='http://localhost:8000/forgot-password'><strong>Forgot Password</strong></a>
                                </p>
                                <br><br>
                                <p>Thanks,<br><strong>The OneUrban Team<strong></p>
                            </div>
                        ");
    
                    $mailer->send($emailMessage);
    
                    $this->addFlash('success', 'Un lien de réinitialisation a été envoyé à votre adresse email.');
                } catch (\Exception $e) {
                    $this->addFlash('danger', 'Erreur lors de l\'envoi de l\'email : ' . $e->getMessage());
                }
            } else {
                $this->addFlash('danger', 'Aucun compte trouvé avec cet email.');
            }
        }

        return $this->render('auth/resetPassword.html.twig');
    }




    #[Route('/reset-password/{id}', name: 'app_reset_password')]
    public function resetPassword($id,Request $request,UriSigner $urlSigner,UserRepository $userRepository,EntityManagerInterface $entityManager,UserPasswordHasherInterface $passwordHasher): Response 
    {
        $user = $userRepository->find($id);
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur introuvable.');
        }

        if ($request->isMethod('POST')) {
            $password = $request->request->get('password');
            $confirmPassword = $request->request->get('confirm_password');

            if (!$password || !$confirmPassword) {
                $this->addFlash('danger', 'Veuillez remplir tous les champs.');
            } elseif ($password !== $confirmPassword) {
                $this->addFlash('danger', 'Les mots de passe ne correspondent pas.');
            } else {
                $hashedPassword = $passwordHasher->hashPassword($user, $password);
                $user->setMotDePasse($hashedPassword);

                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Mot de passe réinitialisé avec succès. Vous pouvez maintenant vous connecter.');

                return $this->redirectToRoute('login');
            }
        }

        return $this->render('auth/retypePassword.html.twig', [
            'userId' => $user->getId(),
        ]);
    }

}
