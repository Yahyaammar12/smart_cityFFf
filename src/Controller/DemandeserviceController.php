<?php

namespace App\Controller;

use App\Entity\Demandeservice;
use App\Entity\Service;
use App\Form\DemandeserviceType;
use App\Form\DemandeserviceTypeFront;
use App\Repository\DemandeserviceRepository;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Twilio\Rest\Client;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;



#[Route('/demandeservice')]
class DemandeserviceController extends AbstractController
{
    #[Route('/', name: 'app_demandeservice_index', methods: ['GET'])]
    public function index(DemandeserviceRepository $demandeserviceRepository): Response
    {
        return $this->render('demandeservice/index.html.twig', [
            'demandeservices' => $demandeserviceRepository->findAll(),
        ]);
    }


    #[Route('/sort-statut', name: 'app_demandeservice_index_sort_statut', methods: ['GET'])]
    public function sortByStatut(DemandeserviceRepository $dsr): Response
    {
        $demandeservices = $dsr->findAllSortedByStatut('ASC');

        return $this->render('demandeservice/index.html.twig', [
            'demandeservices' => $demandeservices,
        ]);
    }



    #[Route('/sort-date', name: 'app_demandeservice_index_sort_date', methods: ['GET'])]
    public function sortByDate(DemandeserviceRepository $dsr): Response
    {
        $demandeservices = $dsr->findAllSortedByDate('ASC');

        return $this->render('demandeservice/index.html.twig', [
            'demandeservices' => $demandeservices,
        ]);
    }

    #[Route('/front/mes_demandeService', name: 'app_demandeservice_user_id', methods: ['GET'])]
    public function indexAllDemandeServices(DemandeServiceRepository $rr, Security $security): Response
    {
        $user = $security->getUser(); // get the connected user
        $data = $rr->findDemandeServicesByUserId($user->getId());

        return $this->render('demandeservice/mesDemandeService.html.twig', [
            'demandeservices' => $data,
        ]);
    }


    #[Route('/{id}/approval', name: 'app_demandeservice_approval', methods: ['GET', 'POST'])]
    public function approveDispo(Request $request, $id, DemandeserviceRepository $dr): Response
    {
        $demandeservice = new Demandeservice();
        $demandeservice = $dr->find($id);
        if($demandeservice->getStatus()=="PENDING" || $demandeservice->getStatus()=="REJECTED"){
            $demandeservice->setStatus("APPROVED");
        }
        $dr->save($demandeservice,true);

        return $this->redirectToRoute('app_demandeservice_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/{id}/reject', name: 'app_demandeservice_reject', methods: ['GET', 'POST'])]
    public function rejectDispo(Request $request, $id, DemandeserviceRepository $dr): Response
    {
        $demandeservice = new Demandeservice();
        $demandeservice = $dr->find($id);
        if($demandeservice->getStatus()=="PENDING" || $demandeservice->getStatus()=="APPROVED"){
            $demandeservice->setStatus("REJECTED");
        }
        $dr->save($demandeservice,true);

        return $this->redirectToRoute('app_demandeservice_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/new', name: 'app_demandeservice_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        $demandeservice = new Demandeservice();
        $user = $security->getUser(); // get the connected user
        $demandeservice->setUser($user);
        $demandeservice->setRating(1);
        $form = $this->createForm(DemandeserviceType::class, $demandeservice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($demandeservice);
            $entityManager->flush();

            return $this->redirectToRoute('app_demandeservice_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('demandeservice/new.html.twig', [
            'demandeservice' => $demandeservice,
            'form' => $form,
        ]);
    }

    #[Route('/front/merci', name: 'app_demandeservice_merci', methods: ['GET'])]
    public function merci(DemandeserviceRepository $demandeserviceRepository): Response
    {
        return $this->render('demandeservice/merci.html.twig', [
            'demandeservices' => $demandeserviceRepository->findAll(),
        ]);
    }

    #[Route('/front/newFront/{id}', name: 'app_demandeservice_newFront', methods: ['GET', 'POST'])]
    public function newFront(Request $request, EntityManagerInterface $entityManager, ServiceRepository $sr, $id, Security $security, \Twilio\Rest\Client $twilioClient_2,SessionInterface $session): Response
    {
        $demandeservice = new Demandeservice();
        $service = new Service();
        $user = $security->getUser(); // get the connected user
        $service = $sr->find($id);
        $demandeservice->setService($service);
        $demandeservice->setStatus("PENDING");
        $demandeservice->setUser($user);
        $demandeservice->setRating(1);
        $form = $this->createForm(DemandeserviceTypeFront::class, $demandeservice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $entityManager->persist($demandeservice);
            $entityManager->flush();

            // Twilio credentials directly in the controller
            $accountSid = $_ENV['TWILIO_ACCOUNT_SID_2'];  // Use the SID from .env
            $authToken = $_ENV['TWILIO_AUTH_TOKEN_2'];    // Use the Auth Token from .env
            $fromNumber = $_ENV['TWILIO_FROM_NUMBER_2'];  // From the .env (TWILIO_FROM_NUMBER_2)

            // Instantiate the Twilio Client directly in the controller
            $twilioClient_2 = new \Twilio\Rest\Client($accountSid, $authToken);

            $toNumber = '+21655575704';  // Phone number


            $twilioClient_2->messages->create(
                $toNumber,
                [
                    'from' => $fromNumber,
                    'body' => 'Une demande de service a ete ajoutee avec success.',
                ]
            );
            $session->getFlashBag()->add('success', 'Une demande de service a ete ajoutee avec success.');

            return $this->redirectToRoute('app_demandeservice_merci', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('demandeservice/newFront.html.twig', [
            'demandeservice' => $demandeservice,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_demandeservice_show', methods: ['GET'])]
    public function show(Demandeservice $demandeservice): Response
    {
        return $this->render('demandeservice/show.html.twig', [
            'demandeservice' => $demandeservice,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_demandeservice_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Demandeservice $demandeservice, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DemandeserviceType::class, $demandeservice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_demandeservice_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('demandeservice/edit.html.twig', [
            'demandeservice' => $demandeservice,
            'form' => $form,
        ]);
    }


    #[Route('/front/{id}/edit/front', name: 'app_demandeservice_edit_front', methods: ['GET', 'POST'])]
    public function editFront($id,DemandeserviceRepository $dsr,Request $request, Demandeservice $demandeservice, EntityManagerInterface $entityManager, Security $security): Response
    {
        $demandeservice = $dsr->find($id);
        $demandeservice->setService($demandeservice->getService());
        $demandeservice->setStatus($demandeservice->getStatus());
        $demandeservice->setRating($demandeservice->getRating());
        $demandeservice->setUser($demandeservice->getUser());

        $form = $this->createForm(DemandeserviceTypeFront::class, $demandeservice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_demandeservice_user_id', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('demandeservice/editFront.html.twig', [
            'demandeservice' => $demandeservice,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_demandeservice_delete', methods: ['POST'])]
    public function delete(Request $request, Demandeservice $demandeservice, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$demandeservice->getId(), $request->request->get('_token'))) {
            $entityManager->remove($demandeservice);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_demandeservice_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/front/{id}/front', name: 'app_demandeservice_delete_front', methods: ['POST'])]
    public function deleteFront(Request $request, Demandeservice $demandeservice, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$demandeservice->getId(), $request->request->get('_token'))) {
            $entityManager->remove($demandeservice);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_demandeservice_user_id', [], Response::HTTP_SEE_OTHER);
    }
}
