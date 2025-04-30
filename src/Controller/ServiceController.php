<?php

namespace App\Controller;

use App\Entity\Service;
use App\Form\ServiceType;
use App\Repository\ServiceRepository;
use App\Repository\DemandeserviceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Filesystem\Filesystem;
use Knp\Component\Pager\PaginatorInterface;



#[Route('/service')]
class ServiceController extends AbstractController
{

    //Visiteur

    #[Route('/visiteur', name: 'app_service_index_front_visiteur', methods: ['GET'])]
    public function indexFrontVisiteur(Request $request, ServiceRepository $sr, PaginatorInterface $paginator): Response
    {
        $data=$sr->findAll();

        $services=$paginator->paginate(
            $data,
            $request->query->getInt('page',1),
            8
        );

        return $this->render('service/indexFrontVisiteur.html.twig', [
            'services' => $services,
        ]);
    }


    //Fin Visiteur
    #[Route('/', name: 'app_service_index', methods: ['GET'])]
    public function index(ServiceRepository $serviceRepository): Response
    {
        return $this->render('service/index.html.twig', [
            'services' => $serviceRepository->findAll(),
        ]);
    }


    #[Route('/sort-description', name: 'app_service_index_sort_description', methods: ['GET'])]
    public function sortByDescription(ServiceRepository $sr): Response
    {
        $services = $sr->findAllSortedByDescription('ASC');

        return $this->render('service/index.html.twig', [
            'services' => $services,
        ]);
    }



    #[Route('/sort-type', name: 'app_service_index_sort_type', methods: ['GET'])]
    public function sortByType(ServiceRepository $sr): Response
    {
        $services = $sr->findAllSortedByType('ASC');

        return $this->render('service/index.html.twig', [
            'services' => $services,
        ]);
    }



    #[Route('/sort-nom', name: 'app_service_index_sort_nom', methods: ['GET'])]
    public function sortByNom(ServiceRepository $sr): Response
    {
        $services = $sr->findAllSortedByNom('ASC');

        return $this->render('service/index.html.twig', [
            'services' => $services,
        ]);
    }


    #[Route('/stat', name: 'app_service_index_stat', methods: ['GET'])]
    public function statServices(ServiceRepository $tr, DemandeServiceRepository $repo): Response
    {
        $number_demande = $repo->getDemandeServicesByServiceId();
        $services = $tr->findAll();

        return $this->render('service/stat.html.twig', [
            'services' => $services,
            'number_demande' => $number_demande,
        ]);
    }


    #[Route('/front', name: 'app_service_index_front', methods: ['GET'])]
    public function indexFront(Request $request, ServiceRepository $sr, PaginatorInterface $paginator): Response
    {
        $data=$sr->findAll();

        $services=$paginator->paginate(
            $data,
            $request->query->getInt('page',1),
            8
        );

        return $this->render('service/indexFront.html.twig', [
            'services' => $services,
        ]);
    }

    #[Route('/new', name: 'app_service_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ServiceRepository $serviceRepository): Response
    {
        $service = new Service();
        $filesystem = new Filesystem();
        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $serviceRepository->save($service, true);

            $uploadedFile = $form->get('imagePath')->getData();
            $formData =  $uploadedFile->getPathname();
            $sourcePath = strval($formData);
            $destinationPath = 'photo'.$service->getNom().strval($service->getId()).'.png';
            $service->setImagePath($destinationPath);
            $filesystem->copy($sourcePath, $destinationPath);
            $serviceRepository->save($service, true);

            return $this->redirectToRoute('app_service_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('service/new.html.twig', [
            'service' => $service,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_service_show', methods: ['GET'])]
    public function show(Service $service): Response
    {
        return $this->render('service/show.html.twig', [
            'service' => $service,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_service_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Service $service, ServiceRepository $serviceRepository): Response
    {
        $filesystem = new Filesystem();
        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $serviceRepository->save($service, true);

            $uploadedFile = $form->get('imagePath')->getData();
            $formData =  $uploadedFile->getPathname();
            $sourcePath = strval($formData);
            $destinationPath = 'photo'.$service->getNom().strval($service->getId()).'.png';
            $service->setImagePath($destinationPath);
            $filesystem->copy($sourcePath, $destinationPath);
            $serviceRepository->save($service, true);

            return $this->redirectToRoute('app_service_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('service/edit.html.twig', [
            'service' => $service,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_service_delete', methods: ['POST'])]
    public function delete(Request $request, Service $service, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$service->getId(), $request->request->get('_token'))) {
            $entityManager->remove($service);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_service_index', [], Response::HTTP_SEE_OTHER);
    }
}
