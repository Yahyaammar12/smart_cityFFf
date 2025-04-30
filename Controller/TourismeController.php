<?php

namespace App\Controller;

use App\Entity\Tourisme;
use App\Form\TourismeType;
use App\Repository\TourismeRepository;
use App\Repository\ReclamationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;


#[Route('/tourisme')]
class TourismeController extends AbstractController
{

    //Visiteur
    #[Route('/visiteur', name: 'app_tourisme_index_front_visiteur', methods: ['GET'])]
    public function indexFrontVisiteur(TourismeRepository $tr, Request $request, PaginatorInterface $paginator): Response
    {
        $data=$tr->findAll();

        $tourismes=$paginator->paginate(
            $data,
            $request->query->getInt('page',1),
            8
        );

        return $this->render('tourisme/indexFrontVisiteur.html.twig', [
            'tourismes' => $tourismes,
        ]);
    }


    //Fin Visiteur

    #[Route('/', name: 'app_tourisme_index', methods: ['GET'])]
    public function index(TourismeRepository $tourismeRepository, ReclamationRepository $rr): Response
    {
        return $this->render('tourisme/index.html.twig', [
            'tourismes' => $tourismeRepository->findAll(),
        ]);
    }


    #[Route('/sort-nbEtoiles', name: 'app_tourisme_index_sort_nbEtoiles', methods: ['GET'])]
    public function sortByNbEtoiles(TourismeRepository $tr): Response
    {
        $tourismes = $tr->findAllSortedByNbEtoiles('ASC');

        return $this->render('tourisme/index.html.twig', [
            'tourismes' => $tourismes,
        ]);
    }


    #[Route('/sort-localisation', name: 'app_tourisme_index_sort_localisation', methods: ['GET'])]
    public function sortByLocalisation(TourismeRepository $tr): Response
    {
        $tourismes = $tr->findAllSortedByLocalisation('ASC');

        return $this->render('tourisme/index.html.twig', [
            'tourismes' => $tourismes,
        ]);
    }


    #[Route('/sort-nom', name: 'app_tourisme_index_sort_nom', methods: ['GET'])]
    public function sortByNom(TourismeRepository $tr): Response
    {
        $tourismes = $tr->findAllSortedByNom('ASC');

        return $this->render('tourisme/index.html.twig', [
            'tourismes' => $tourismes,
        ]);
    }


    #[Route('/sort-type', name: 'app_tourisme_index_sort_type', methods: ['GET'])]
    public function sortByType(TourismeRepository $tr): Response
    {
        $tourismes = $tr->findAllSortedByType('ASC');

        return $this->render('tourisme/index.html.twig', [
            'tourismes' => $tourismes,
        ]);
    }


    #[Route('/stat', name: 'app_tourisme_index_stat', methods: ['GET'])]
    public function statTourismes(TourismeRepository $tr, ReclamationRepository $rr): Response
    {
        $number_reclamations = $rr->countReclamationsByTourisme();

        return $this->render('tourisme/stat.html.twig', [
            'tourismes' => $tr->findAll(),
            'number_reclamations' => $number_reclamations,
        ]);
    }

    #[Route('/front', name: 'app_tourisme_index_front', methods: ['GET'])]
    public function indexFront(TourismeRepository $tr, Request $request, PaginatorInterface $paginator): Response
    {
        $data=$tr->findAll();

        $tourismes=$paginator->paginate(
            $data,
            $request->query->getInt('page',1),
            8
        );

        return $this->render('tourisme/indexFront.html.twig', [
            'tourismes' => $tourismes,
        ]);
    }

    #[Route('/new', name: 'app_tourisme_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $tourisme = new Tourisme();
        $form = $this->createForm(TourismeType::class, $tourisme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($tourisme);
            $entityManager->flush();

            return $this->redirectToRoute('app_tourisme_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('tourisme/new.html.twig', [
            'tourisme' => $tourisme,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tourisme_show', methods: ['GET'])]
    public function show(Tourisme $tourisme): Response
    {
        return $this->render('tourisme/show.html.twig', [
            'tourisme' => $tourisme,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_tourisme_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Tourisme $tourisme, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TourismeType::class, $tourisme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_tourisme_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('tourisme/edit.html.twig', [
            'tourisme' => $tourisme,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tourisme_delete', methods: ['POST'])]
    public function delete(Request $request, Tourisme $tourisme, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tourisme->getId(), $request->request->get('_token'))) {
            $entityManager->remove($tourisme);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_tourisme_index', [], Response::HTTP_SEE_OTHER);
    }
}
