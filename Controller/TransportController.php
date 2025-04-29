<?php

namespace App\Controller;

use App\Entity\Transport;
use App\Form\TransportType;
use App\Repository\TransportRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Filesystem\Filesystem;
use Knp\Component\Pager\PaginatorInterface;



#[Route('/transport')]
class TransportController extends AbstractController
{

    //Visiteur
    #[Route('/visiteur', name: 'app_transport_index_front_visiteur', methods: ['GET'])]
    public function indexFrontVisiteur(TransportRepository $tr, Request $request, PaginatorInterface $paginator): Response
    {
        $data=$tr->findAll();

        $transports=$paginator->paginate(
            $data,
            $request->query->getInt('page',1),
            8
        );

        return $this->render('transport/indexFrontVisiteur.html.twig', [
            'transports' => $transports,
        ]);
    }




    //Fin Visiteur
    #[Route('/', name: 'app_transport_index', methods: ['GET'])]
    public function index(TransportRepository $transportRepository): Response
    {
        return $this->render('transport/index.html.twig', [
            'transports' => $transportRepository->findAll(),
        ]);
    }


    #[Route('/sort-placesLibres', name: 'app_transport_index_sort_placesLibres', methods: ['GET'])]
    public function sortByPlacesLibres(TransportRepository $tr): Response
    {
        $transports = $tr->findAllSortedByPlacesLibres('ASC');

        return $this->render('transport/index.html.twig', [
            'transports' => $transports,
        ]);
    }


    #[Route('/sort-tarif', name: 'app_transport_index_sort_tarif', methods: ['GET'])]
    public function sortByTarif(TransportRepository $tr): Response
    {
        $transports = $tr->findAllSortedByTarif('ASC');

        return $this->render('transport/index.html.twig', [
            'transports' => $transports,
        ]);
    }


    #[Route('/sort-horaire', name: 'app_transport_index_sort_horaire', methods: ['GET'])]
    public function sortByHoraire(TransportRepository $tr): Response
    {
        $transports = $tr->findAllSortedByHoraire('ASC');

        return $this->render('transport/index.html.twig', [
            'transports' => $transports,
        ]);
    }


    #[Route('/sort-type', name: 'app_transport_index_sort_type', methods: ['GET'])]
    public function sortByType(TransportRepository $tr): Response
    {
        $transports = $tr->findAllSortedByType('ASC');

        return $this->render('transport/index.html.twig', [
            'transports' => $transports,
        ]);
    }


    #[Route('/stat', name: 'app_transport_index_stat', methods: ['GET'])]
    public function statTransports(TransportRepository $tr, Request $request): Response
    {
        $transports = $tr->findAll();

        return $this->render('transport/stat.html.twig', [
            'transports' => $transports,
        ]);
    }


    #[Route('/front', name: 'app_transport_index_front', methods: ['GET'])]
    public function indexFront(TransportRepository $tr, Request $request, PaginatorInterface $paginator): Response
    {
        $data=$tr->findAll();

        $transports=$paginator->paginate(
            $data,
            $request->query->getInt('page',1),
            8
        );

        return $this->render('transport/indexFront.html.twig', [
            'transports' => $transports,
        ]);
    }

    #[Route('/new', name: 'app_transport_new', methods: ['GET', 'POST'])]
    public function new(Request $request, TransportRepository $transportRepository): Response
    {
        $transport = new Transport();
        $filesystem = new Filesystem();
        $form = $this->createForm(TransportType::class, $transport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $transportRepository->save($transport, true);

            $uploadedFile = $form->get('image')->getData();
            $formData =  $uploadedFile->getPathname();
            $sourcePath = strval($formData);
            $destinationPath = 'photo'.$transport->getDepart().strval($transport->getId()).'.png';
            $transport->setImage($destinationPath);
            $filesystem->copy($sourcePath, $destinationPath);
            $transportRepository->save($transport, true);

            return $this->redirectToRoute('app_transport_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('transport/new.html.twig', [
            'transport' => $transport,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_transport_show', methods: ['GET'])]
    public function show(Transport $transport): Response
    {
        return $this->render('transport/show.html.twig', [
            'transport' => $transport,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_transport_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Transport $transport, TransportRepository $transportRepository): Response
    {
        $filesystem = new Filesystem();
        $form = $this->createForm(TransportType::class, $transport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $transportRepository->save($transport, true);

            $uploadedFile = $form->get('image')->getData();
            $formData =  $uploadedFile->getPathname();
            $sourcePath = strval($formData);
            $destinationPath = 'photo'.$transport->getDepart().strval($transport->getId()).'.png';
            $transport->setImage($destinationPath);
            $filesystem->copy($sourcePath, $destinationPath);
            $transportRepository->save($transport, true);

            return $this->redirectToRoute('app_transport_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('transport/edit.html.twig', [
            'transport' => $transport,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_transport_delete', methods: ['POST'])]
    public function delete(Request $request, Transport $transport, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$transport->getId(), $request->request->get('_token'))) {
            $entityManager->remove($transport);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_transport_index', [], Response::HTTP_SEE_OTHER);
    }
}
