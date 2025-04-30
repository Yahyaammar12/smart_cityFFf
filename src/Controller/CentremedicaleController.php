<?php

namespace App\Controller;

use App\Entity\Centremedicale;
use App\Form\CentremedicaleType;
use App\Repository\CentremedicaleRepository;
use App\Repository\RendezvouRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;



#[Route('/centremedicale')]
class CentremedicaleController extends AbstractController
{

    //Visiteur

    #[Route('/visiteur', name: 'app_centremedicale_index_front_visiteur', methods: ['GET'])]
    public function indexFrontVisiteur(CentremedicaleRepository $cr, PaginatorInterface $paginator, Request $request): Response
    {
        $data=$cr->findAll();

        $centremedicales=$paginator->paginate(
            $data,
            $request->query->getInt('page',1),
            8
        );

        return $this->render('centremedicale/indexFrontVisiteur.html.twig', [
            'centremedicales' => $centremedicales,
        ]);
    }


    //Fin Visiteur



    #[Route('/', name: 'app_centremedicale_index', methods: ['GET'])]
    public function index(CentremedicaleRepository $cr): Response
    {   
        $centremedicales=$cr->findAll();

        return $this->render('centremedicale/index.html.twig', [
            'centremedicales' => $centremedicales,
        ]);
    }


    #[Route('/sort-nom', name: 'app_centremedicale_index_sort_nom', methods: ['GET'])]
    public function sortByNom(CentremedicaleRepository $tr): Response
    {
        $centremedicales = $tr->findAllSortedByNom('ASC');

        return $this->render('centremedicale/index.html.twig', [
            'centremedicales' => $centremedicales,
        ]);
    }


    #[Route('/sort-type', name: 'app_centremedicale_index_sort_type', methods: ['GET'])]
    public function sortByType(CentremedicaleRepository $tr): Response
    {
        $centremedicales = $tr->findAllSortedByType('ASC');

        return $this->render('centremedicale/index.html.twig', [
            'centremedicales' => $centremedicales,
        ]);
    }


    #[Route('/sort-localisation', name: 'app_centremedicale_index_sort_localisation', methods: ['GET'])]
    public function sortByLocalisation(CentremedicaleRepository $tr): Response
    {
        $centremedicales = $tr->findAllSortedByLocalisation('ASC');

        return $this->render('centremedicale/index.html.twig', [
            'centremedicales' => $centremedicales,
        ]);
    }


    #[Route('/sort-dispo', name: 'app_centremedicale_index_sort_dispo', methods: ['GET'])]
    public function sortByDisponibilite(CentremedicaleRepository $tr): Response
    {
        $centremedicales = $tr->findAllSortedByDisponibilite('ASC');

        return $this->render('centremedicale/index.html.twig', [
            'centremedicales' => $centremedicales,
        ]);
    }


    #[Route('/stat', name: 'app_centremedicale_index_stat', methods: ['GET'])]
    public function statTransports(CentremedicaleRepository $cr, RendezvouRepository $rr): Response
    {
        $number_rdv = $rr->getRdvsByCentreId();
        $centremedicales = $cr->findAll();

        return $this->render('centremedicale/stat.html.twig', [
            'centremedicales' => $centremedicales,
            'number_rdv' => $number_rdv
        ]);
    }



    #[Route('/front', name: 'app_centremedicale_index_front', methods: ['GET'])]
    public function indexFront(CentremedicaleRepository $cr, PaginatorInterface $paginator, Request $request): Response
    {
        $data=$cr->findAll();

        $centremedicales=$paginator->paginate(
            $data,
            $request->query->getInt('page',1),
            8
        );

        return $this->render('centremedicale/indexFront.html.twig', [
            'centremedicales' => $centremedicales,
        ]);
    }

    #[Route('/{id}/editDispo', name: 'app_centremedicale_edit_dispo', methods: ['GET', 'POST'])]
    public function editDispo(Request $request, $id, centremedicaleRepository $cr): Response
    {
        $centremedicale = new Centremedicale();
        $centremedicale = $cr->find($id);
        if($centremedicale->isDisponibilite()==true){
            $centremedicale->setDisponibilite(false);
        }else{
            $centremedicale->setDisponibilite(true);
        }
        $cr->save($centremedicale,true);

        return $this->redirectToRoute('app_centremedicale_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/new', name: 'app_centremedicale_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $centremedicale = new Centremedicale();
        $form = $this->createForm(CentremedicaleType::class, $centremedicale);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($centremedicale);
            $entityManager->flush();

            return $this->redirectToRoute('app_centremedicale_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('centremedicale/new.html.twig', [
            'centremedicale' => $centremedicale,
            'form' => $form,
        ]);
    }

    

    #[Route('/{id}', name: 'app_centremedicale_show', methods: ['GET'])]
    public function show(Centremedicale $centremedicale): Response
    {
        return $this->render('centremedicale/show.html.twig', [
            'centremedicale' => $centremedicale,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_centremedicale_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Centremedicale $centremedicale, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CentremedicaleType::class, $centremedicale);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_centremedicale_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('centremedicale/edit.html.twig', [
            'centremedicale' => $centremedicale,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_centremedicale_delete', methods: ['POST'])]
    public function delete(Request $request, Centremedicale $centremedicale, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$centremedicale->getId(), $request->request->get('_token'))) {
            $entityManager->remove($centremedicale);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_centremedicale_index', [], Response::HTTP_SEE_OTHER);
    }
}
