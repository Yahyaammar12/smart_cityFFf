<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Entity\Tourisme;
use App\Form\ReclamationType;
use App\Form\ReclamationTypeFront;
use App\Repository\ReclamationRepository;
use App\Repository\TourismeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;
use Symfony\Component\Security\Core\Security;



#[Route('/reclamation')]
class ReclamationController extends AbstractController
{
    #[Route('/', name: 'app_reclamation_index', methods: ['GET'])]
    public function index(ReclamationRepository $reclamationRepository, TourismeRepository $tr): Response
    {
        // Fetch all tourismes
        $tourismes = $tr->findAll();

        // Create an array with the ids as integers
        $tourismeNames = [];
        foreach ($tourismes as $tourisme) {
            $tourismeNames[$tourisme->getId()] = $tourisme->getNom();
        }
        
        return $this->render('reclamation/index.html.twig', [
            'reclamations' => $reclamationRepository->findAll(),
            'tourismeNames' => $tourismeNames,
        ]);
    }


    #[Route('/{id}/editTraitement', name: 'app_reclamation_edit_traitement', methods: ['GET', 'POST'])]
    public function editTraitement(Request $request, $id, ReclamationRepository $rr): Response
    {
        $reclamation = new Reclamation();
        $reclamation = $rr->find($id);
        if($reclamation->isSolved()==true){
            $reclamation->setSolved(false);
        }else{
            $reclamation->setSolved(true);
        }
        $rr->save($reclamation,true);

        return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/sort-date', name: 'app_reclamation_index_sort_date', methods: ['GET'])]
    public function sortByDate(ReclamationRepository $ttr, TourismeRepository $tr): Response
    {
        // Fetch all tourismes
        $tourismes = $tr->findAll();

        // Create an array with the ids as integers
        $tourismeNames = [];
        foreach ($tourismes as $tourisme) {
            $tourismeNames[$tourisme->getId()] = $tourisme->getNom();
        }

        $reclamations = $ttr->findAllSortedByDate('ASC');

        return $this->render('reclamation/index.html.twig', [
            'reclamations' => $reclamations,
            'tourismeNames' => $tourismeNames,
        ]);
    }

    #[Route('/sort-solved', name: 'app_reclamation_index_sort_solved', methods: ['GET'])]
    public function sortBySolved(ReclamationRepository $ttr, TourismeRepository $tr): Response
    {
        // Fetch all tourismes
        $tourismes = $tr->findAll();

        // Create an array with the ids as integers
        $tourismeNames = [];
        foreach ($tourismes as $tourisme) {
            $tourismeNames[$tourisme->getId()] = $tourisme->getNom();
        }

        $reclamations = $ttr->findAllSortedBySolved('ASC');

        return $this->render('reclamation/index.html.twig', [
            'reclamations' => $reclamations,
            'tourismeNames' => $tourismeNames,
        ]);
    }


    #[Route('/sort-sujet', name: 'app_reclamation_index_sort_sujet', methods: ['GET'])]
    public function sortBySujet(ReclamationRepository $ttr, TourismeRepository $tr): Response
    {
        // Fetch all tourismes
        $tourismes = $tr->findAll();

        // Create an array with the ids as integers
        $tourismeNames = [];
        foreach ($tourismes as $tourisme) {
            $tourismeNames[$tourisme->getId()] = $tourisme->getNom();
        }

        $reclamations = $ttr->findAllSortedBySujet('ASC');

        return $this->render('reclamation/index.html.twig', [
            'reclamations' => $reclamations,
            'tourismeNames' => $tourismeNames,
        ]);
    }


    #[Route('/sort-rating', name: 'app_reclamation_index_sort_rating', methods: ['GET'])]
    public function sortByRating(ReclamationRepository $ttr, TourismeRepository $tr): Response
    {
        // Fetch all tourismes
        $tourismes = $tr->findAll();

        // Create an array with the ids as integers
        $tourismeNames = [];
        foreach ($tourismes as $tourisme) {
            $tourismeNames[$tourisme->getId()] = $tourisme->getNom();
        }

        $reclamations = $ttr->findAllSortedByRating('ASC');

        return $this->render('reclamation/index.html.twig', [
            'reclamations' => $reclamations,
            'tourismeNames' => $tourismeNames,
        ]);
    }



    #[Route('/front/merci', name: 'app_reclamation_merci', methods: ['GET'])]
    public function merci(ReclamationRepository $reclamationRepository): Response
    {
        return $this->render('reclamation/merci.html.twig', [
            'reclamations' => $reclamationRepository->findAll(),
        ]);
    }


    #[Route('/front/mes_reclamations', name: 'app_reclamation_user_id', methods: ['GET'])]
    public function indexAllReclamations(ReclamationRepository $rr, Security $security, TourismeRepository $tr): Response
    {
        // Fetch all tourismes
        $tourismes = $tr->findAll();

        // Create an array with the ids as integers
        $tourismeNames = [];
        foreach ($tourismes as $tourisme) {
            $tourismeNames[$tourisme->getId()] = $tourisme->getNom();
        }

        $user = $security->getUser(); // get the connected user
        $data = $rr->findReclamationsByUserId($user->getId());

        return $this->render('reclamation/mesReclamations.html.twig', [
            'reclamations' => $data,
            'tourismeNames' => $tourismeNames,
        ]);
    }


    public function filterwords($text)
    {
        $filterWords = array('fokaleya', 'bhim', 'msatek', 'fuck', 'slut', 'fucku');
        $str = "";
        $containsBadWords = false;

        $data = preg_split('/\s+/', $text);
        foreach ($data as $s) {
            $found = false;
            foreach ($filterWords as $lib) {
                if (strtolower($s) == strtolower($lib)) {
                    $containsBadWords = true;
                    $str .= str_repeat('*', strlen($s)) . " ";
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $str .= $s . " ";
            }
        }

        return ['filtered' => trim($str), 'hasBadWords' => $containsBadWords];
    }


    #[Route('/front/newFront/{id}', name: 'app_reclamation_new_front', methods: ['GET', 'POST'])]
    public function newFront(Request $request, EntityManagerInterface $entityManager, TourismeRepository $tr, $id, Security $security): Response
    {
        $reclamation = new reclamation();
        $user = $security->getUser(); // get the connected user
        $tourisme = $tr->find($id);
        if (!$tourisme) {
            throw $this->createNotFoundException("Tourisme not found.");
        }
        $reclamation->setDate(new \DateTime());
        $reclamation->setSolved(false);
        $reclamation->setTourismeId($tourisme->getId());
        $reclamation->setUser($user);
        $form = $this->createForm(ReclamationTypeFront::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sujetFilter = $this->filterwords($reclamation->getSujet());
            $descriptionFilter = $this->filterwords($reclamation->getDescription());

            $reclamation->setSujet($sujetFilter['filtered']);
            $reclamation->setDescription($descriptionFilter['filtered']);

            if ($sujetFilter['hasBadWords'] || $descriptionFilter['hasBadWords']) {
                $this->addFlash('warning', 'Attention : Certains mots inappropriés ont été détectés et remplacés par des astérisques.');
            }

            $entityManager->persist($reclamation);
            $entityManager->flush();
            return $this->redirectToRoute('app_reclamation_merci', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reclamation/newFront.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form,
        ]);
    }


    #[Route('/new', name: 'app_reclamation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Security $security, TourismeRepository $tr): Response
    {
        $reclamation = new Reclamation();
        $user = $security->getUser(); // get the connected user
        $reclamation->setUser($user);
        $form = $this->createForm(ReclamationType::class, $reclamation,[
            'tourismes' => $tr->findAll()
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $bb = $this->filterwords($reclamation->getSujet());
            $reclamation->setSujet($bb);

            $rr = $this->filterwords($reclamation->getDescription());
            $reclamation->setDescription($rr);

            
            $entityManager->persist($reclamation);
            $entityManager->flush();

            return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reclamation/new.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reclamation_show', methods: ['GET'])]
    public function show(Reclamation $reclamation, TourismeRepository $tr): Response
    {
        // Fetch all tourismes
        $tourismes = $tr->findAll();

        // Create an array with the ids as integers
        $tourismeNames = [];
        foreach ($tourismes as $tourisme) {
            $tourismeNames[$tourisme->getId()] = $tourisme->getNom();
        }

        return $this->render('reclamation/show.html.twig', [
            'reclamation' => $reclamation,
            'tourismeNames' => $tourismeNames,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reclamation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager, TourismeRepository $tr): Response
    {
        $tourismes = $tr->findAll();

        $form = $this->createForm(ReclamationType::class, $reclamation, [
            'tourismes' => $tourismes, // Pass the tourismes to the form
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reclamation/edit.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reclamation_delete', methods: ['POST'])]
    public function delete(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reclamation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($reclamation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/front/{id}/front', name: 'app_reclamation_delete_front', methods: ['POST'])]
    public function deleteFront(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reclamation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($reclamation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reclamation_user_id', [], Response::HTTP_SEE_OTHER);
    }
}
