<?php

namespace App\Controller;

use App\Entity\Rendezvou;
use App\Entity\Centremedicale;
use App\Form\RendezvouType;
use App\Form\RendezvouTypeFront;
use App\Repository\RendezvouRepository;
use App\Repository\CentremedicaleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Twilio\Rest\Client;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use DateTime;




#[Route('/rendezvou')]
class RendezvouController extends AbstractController
{
    #[Route('/calendrier', name: 'app_calendar')]
    public function CalendarView(RendezvouRepository $repo): Response
    {
        $events = $repo->findAll();

        $rdvs = [];

        foreach ($events as $event) {
            // Ensure that the event has a valid user (patient) and both the 'nom' and 'prenom' are available
            if ($event->getUser()) {
                $patientName = $event->getUser()->getPrenom() . " " . $event->getUser()->getNom();
            } else {
                $patientName = "Unknown"; // Fallback in case there's no associated user
            }

            $rdvs[] = [
                'id' => $event->getId(),
                'title' => $patientName . " Avec DR. " . $event->getNomMedecin(), // This will display the patient's name
                'start' => $event->getDate()->format('Y-m-d') . 'T' . $event->getHeure()->format('H:i'),
                'heure' => $event->getHeure()->format('H:i'),
                'statut' => $event->getStatus(),
                'user' => $patientName,
                'nomMedecin' => $event->getNomMedecin(),
                'backgroundColor' => "#3c8dbc",
                'borderColor' => "#3c8dbc",
                'textColor' => "#ffffff",
            ];
        }

        $data = json_encode($rdvs);

        return $this->render('rendezvou/calendrier.html.twig', compact('data'));
    }


    #[Route('/', name: 'app_rendezvou_index', methods: ['GET'])]
    public function index(RendezvouRepository $rendezvouRepository, Client $twilioClient, MailerInterface $mailer): Response
    {
        $msg_notif=null;
        // Fetch all rendezvous
        $rendezvous = $rendezvouRepository->findAll();

        // Get current time
        $now = new DateTime();

        foreach ($rendezvous as $rdv) {
            $dateRdv = $rdv->getDate(); // Make sure getDate returns a DateTime object

            // Calculate the difference between now and the rendezvous time
            $interval = $dateRdv->getTimestamp() - $now->getTimestamp();

            // Check if the rendezvous is within the next 24 hours (86400 seconds)
            if ($interval > 0 && $interval <= 86400) {
                $toNumber = '+21623232457'; // Phone number
                $fromNumber = '+17197457032'; // Your Twilio phone number

                // Send SMS using Twilio
                $twilioClient->messages->create(
                    $toNumber,
                    [
                        'from' => $fromNumber,
                        'body' => 'Vous avez un Rendez-vous dans 24 heures.',
                    ]
                );
                $msg_notif = "Le message de notification est récu pour tous les Rendezvous dans 24H.";
            }
            $user = $rdv->getUser(); // ou getClient() selon ton entité
            if ($user && $user->getEmail()) {
                $email = (new Email())
                    ->from('noreply@votre-app.com')
                    ->to($user->getEmail())
                    ->subject('📅 Rappel : Rendez-vous dans 24h')
                    ->html("
                        <p>Bonjour {$user->getNom()},</p>
                        <p>Rappel : vous avez un rendez-vous prévu le <strong>{$dateRdv->format('d/m/Y')}</strong>.</p>
                        <p>Merci de votre ponctualité.</p>
                    ");
                $mailer->send($email);
            }

            $msg_notif = "Le message de notification est récu pour tous les Rendezvous dans 24H.";
        }
    
        

        // Render the view and pass the rendezvous list
        return $this->render('rendezvou/index.html.twig', [
            'rendezvous' => $rendezvous,
            'msg_notif' => $msg_notif
        ]);
    }


    #[Route('/sort-date', name: 'app_rendezvou_index_sort_date', methods: ['GET'])]
    public function sortByDate(RendezvouRepository $rr): Response
    {
        $rendezvous = $rr->findAllSortedByDate('ASC');

        return $this->render('rendezvou/index.html.twig', [
            'rendezvous' => $rendezvous,
        ]);
    }

    #[Route('/sort-heure', name: 'app_rendezvou_index_sort_heure', methods: ['GET'])]
    public function sortByHeure(RendezvouRepository $rr): Response
    {
        $rendezvous = $rr->findAllSortedByHeure('ASC');

        return $this->render('rendezvou/index.html.twig', [
            'rendezvous' => $rendezvous,
        ]);
    }


    #[Route('/sort-statut', name: 'app_rendezvou_index_sort_statut', methods: ['GET'])]
    public function sortByStatut(RendezvouRepository $rr): Response
    {
        $rendezvous = $rr->findAllSortedByStatut('ASC');

        return $this->render('rendezvou/index.html.twig', [
            'rendezvous' => $rendezvous,
        ]);
    }


    #[Route('/sort-nomMedecin', name: 'app_rendezvou_index_sort_nomMedecin', methods: ['GET'])]
    public function sortByNomMedecin(RendezvouRepository $rr): Response
    {
        $rendezvous = $rr->findAllSortedByNomMedecin('ASC');

        return $this->render('rendezvou/index.html.twig', [
            'rendezvous' => $rendezvous,
        ]);
    }



    #[Route('/front/mes_rendezvous', name: 'app_rendezvou_user_id', methods: ['GET'])]
    public function indexAllrendezvous(RendezvouRepository $rr, Security $security): Response
    {
        $user = $security->getUser(); // get the connected user
        $data = $rr->findRendezVousByUserId($user->getId());

        return $this->render('rendezvou/mesRendezvous.html.twig', [
            'rendezvous' => $data,
        ]);
    }

    #[Route('/{id}/annuler', name: 'app_rendezvou_annuler', methods: ['GET', 'POST'])]
    public function annulerRendezVous(Request $request, $id, RendezvouRepository $rr): Response
    {
        $rendezvou = new Rendezvou();
        $rendezvou = $rr->find($id);
        $rendezvou->setStatus('Annulé');
        $rr->save($rendezvou,true);

        return $this->redirectToRoute('app_rendezvou_user_id', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/new', name: 'app_rendezvou_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        $rendezvou = new Rendezvou();
        $user = $security->getUser(); // get the connected user
        $rendezvou->setUser($user);
        $form = $this->createForm(RendezvouType::class, $rendezvou);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($rendezvou);
            $entityManager->flush();

            return $this->redirectToRoute('app_rendezvou_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('rendezvou/new.html.twig', [
            'rendezvou' => $rendezvou,
            'form' => $form,
        ]);
    }


    #[Route('/front/merci', name: 'app_rendezvou_merci', methods: ['GET'])]
    public function merci(RendezvouRepository $rendezvouRepository): Response
    {
        return $this->render('rendezvou/merci.html.twig', [
            'rendezvous' => $rendezvouRepository->findAll(),
        ]);
    }

    #[Route('/front/newFront/{id}', name: 'app_rendezvou_new_front', methods: ['GET', 'POST'])]
    public function newFront(Request $request, EntityManagerInterface $entityManager, CentremedicaleRepository $cmr, $id, Security $security): Response
    {
        $rendezvou = new Rendezvou();
        $user = $security->getUser(); // get the connected user
        $centremedicale = new Centremedicale();
        $centremedicale = $cmr->find($id);
        $rendezvou->setStatus("En Attente");
        $rendezvou->setCentremedicale($centremedicale);
        $rendezvou->setUser($user);
        $form = $this->createForm(RendezvouTypeFront::class, $rendezvou);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($rendezvou);
            $entityManager->flush();

            return $this->redirectToRoute('app_rendezvou_merci', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('rendezvou/newFront.html.twig', [
            'rendezvou' => $rendezvou,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_rendezvou_show', methods: ['GET'])]
    public function show(Rendezvou $rendezvou): Response
    {
        return $this->render('rendezvou/show.html.twig', [
            'rendezvou' => $rendezvou,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_rendezvou_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Rendezvou $rendezvou, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RendezvouType::class, $rendezvou);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_rendezvou_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('rendezvou/edit.html.twig', [
            'rendezvou' => $rendezvou,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_rendezvou_delete', methods: ['POST'])]
    public function delete(Request $request, Rendezvou $rendezvou, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$rendezvou->getId(), $request->request->get('_token'))) {
            $entityManager->remove($rendezvou);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_rendezvou_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/api/calendarOnClick/{id}', name: 'calendar_onClick', methods: ['GET'])]
    public function onClick(Rendezvous $rendezvou, Request $request): Response
    {
        $nom = $request->request->get('date');

        return $this->render('rendezvou/show.html.twig', [
            'rendezvou' => $rendezvou,
        ]);
    }
}
