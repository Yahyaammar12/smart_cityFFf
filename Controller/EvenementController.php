<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Entity\Loisir;
use App\Form\EvenementType;
use App\Form\EvenementTypeFront;
use App\Repository\EvenementRepository;
use App\Repository\LoisirRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use App\Service\CronofyService;



#[Route('/evenement')]
class EvenementController extends AbstractController
{

    //Visiteur
    #[Route('/visiteur', name: 'app_evenement_index_front_visiteur', methods: ['GET'])]
    public function indexFrontVisiteur(EvenementRepository $er, PaginatorInterface $paginator, Request $request): Response
    {
        $data=$er->findAll();

        $evenements=$paginator->paginate(
            $data,
            $request->query->getInt('page',1),
            8
        );

        return $this->render('evenement/indexFrontVisiteur.html.twig', [
            'evenements' => $evenements,
        ]);
    }

    //Fin Visiteur


    


    #[Route('/calendar', name: 'app_calendar_events', methods: ['GET'])]
    public function calendrier(EvenementRepository $evenementRepository): Response
    {
        return $this->render('evenement/calendrier.html.twig', [
            'evenements' => $evenementRepository->findAll(),
        ]);
    }


    #[Route('/', name: 'app_evenement_index', methods: ['GET'])]
    public function index(EvenementRepository $evenementRepository): Response
    {
        return $this->render('evenement/index.html.twig', [
            'evenements' => $evenementRepository->findAll(),
        ]);
    }



    #[Route('/sort-localisation', name: 'app_evenement_index_sort_localisation', methods: ['GET'])]
    public function sortByLocalisation(EvenementRepository $er): Response
    {
        $evenements = $er->findAllSortedByLocalisation('ASC');

        return $this->render('evenement/index.html.twig', [
            'evenements' => $evenements,
        ]);
    }



    #[Route('/sort-heure', name: 'app_evenement_index_sort_heure', methods: ['GET'])]
    public function sortByHeure(EvenementRepository $er): Response
    {
        $evenements = $er->findAllSortedByHeure('ASC');

        return $this->render('evenement/index.html.twig', [
            'evenements' => $evenements,
        ]);
    }



    #[Route('/sort-date', name: 'app_evenement_index_sort_date', methods: ['GET'])]
    public function sortByDate(EvenementRepository $er): Response
    {
        $evenements = $er->findAllSortedByDate('ASC');

        return $this->render('evenement/index.html.twig', [
            'evenements' => $evenements,
        ]);
    }



    #[Route('/sort-nom', name: 'app_evenement_index_sort_nom', methods: ['GET'])]
    public function sortByNom(EvenementRepository $er): Response
    {
        $evenements = $er->findAllSortedByNom('ASC');

        return $this->render('evenement/index.html.twig', [
            'evenements' => $evenements,
        ]);
    }





    #[Route('/front', name: 'app_evenement_index_front', methods: ['GET'])]
    public function indexFront(EvenementRepository $er, PaginatorInterface $paginator, Request $request): Response
    {
        $data=$er->findAll();

        $evenements=$paginator->paginate(
            $data,
            $request->query->getInt('page',1),
            8
        );

        return $this->render('evenement/indexFront.html.twig', [
            'evenements' => $evenements,
        ]);
    }

    #[Route('/new', name: 'app_evenement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, CronofyService $cronofy): Response
    {
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($evenement);
            $entityManager->flush();

            // Envoi de l'événement à Cronofy après l'ajout
            $calendarId = 'cal_XXXXXXXX'; // Ton ID de calendrier Cronofy
            $cronofy->createEvent(
                $calendarId, 
                $evenement->getNom(), 
                $evenement->getDate(), 
                (clone $evenement->getDate())->modify('+1 hour') // Exemple d'heure de fin
            );

            return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('evenement/new.html.twig', [
            'evenement' => $evenement,
            'form' => $form,
        ]);
    }


    #[Route('/front/merci', name: 'app_evenement_merci', methods: ['GET'])]
    public function merci(EvenementRepository $evenementRepository): Response
    {
        return $this->render('evenement/merci.html.twig', [
            'evenements' => $evenementRepository->findAll(),
        ]);
    }

    #[Route('/front/newFront/{id}', name: 'app_evenement_new_front', methods: ['GET', 'POST'])]
    public function newFront(Request $request, EntityManagerInterface $entityManager, LoisirRepository $lr, $id): Response
    {
        $evenement = new Evenement();
        $loisir = new Loisir();
        $loisir = $lr->find($id);
        $evenement->setLoisir($loisir);
        $form = $this->createForm(EvenementTypeFront::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($evenement);
            $entityManager->flush();

            return $this->redirectToRoute('app_evenement_merci', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('evenement/newFront.html.twig', [
            'evenement' => $evenement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_evenement_show', methods: ['GET'])]
    public function show(Evenement $evenement): Response
    {
        return $this->render('evenement/show.html.twig', [
            'evenement' => $evenement,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_evenement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Evenement $evenement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('evenement/edit.html.twig', [
            'evenement' => $evenement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_evenement_delete', methods: ['POST'])]
    public function delete(Request $request, Evenement $evenement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$evenement->getId(), $request->request->get('_token'))) {
            $entityManager->remove($evenement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
    }
}
