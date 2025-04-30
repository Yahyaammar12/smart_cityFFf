<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use App\Repository\BilletRepository;
use App\Repository\DemandeserviceRepository;
use App\Repository\TourismeRepository;
use App\Repository\ReclamationRepository;



class HomeController extends AbstractController
{
    #[Route('/back', name: 'app_home')]
    public function index(UserRepository $ur, BilletRepository $br, DemandeserviceRepository $dsr, TourismeRepository $tr, ReclamationRepository $rr): Response
    {
        $best_reclamations = $rr->findHighRatedReclamations();
        $satisfaction_perc = $tr->getSatisfactionPercentage();
        $stats = $dsr->getServiceRequestStats();
        $number_billets = $br->countBillets();
        $number_users = $ur->countUsers();

        return $this->render('home/index.html.twig', [
            'number_users' => $number_users,
            'number_billets' => $number_billets,
            'percentage_services_requested' => $stats['percentage_services_requested'],
            'satisfaction_perc' => $satisfaction_perc,
            'best_reclamations' => $best_reclamations
        ]);
    }


    #[Route('/front', name: 'app_front')]
    public function indexFront(): Response
    {
        return $this->render('baseFront.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }


    #[Route('/visiteur', name: 'app_front_visiteur')]
    public function indexFrontVisiteur(): Response
    {
        return $this->render('baseVisiteur.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }


    #[Route('/', name: 'app_redirect')]
    public function redirectToVisiteur(): Response
    {
        return $this->redirectToRoute('app_front_visiteur');
    }
}
