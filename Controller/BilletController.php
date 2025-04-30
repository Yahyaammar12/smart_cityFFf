<?php

namespace App\Controller;

use App\Entity\Billet;
use App\Entity\Transport;
use App\Form\BilletType;
use App\Form\BilletTypeFront;
use App\Repository\BilletRepository;
use App\Repository\TransportRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Dompdf\Dompdf;
use Dompdf\Options;
use Knp\Component\Pager\PaginatorInterface;




#[Route('/billet')]
class BilletController extends AbstractController
{
    #[Route('/', name: 'app_billet_index', methods: ['GET'])]
    public function index(BilletRepository $br): Response
    {
        $billets=$br->findAll();

        return $this->render('billet/index.html.twig', [
            'billets' => $billets,
        ]);
    }



    #[Route('/sort-statutPaiement', name: 'app_billet_index_sort_statutPaiement', methods: ['GET'])]
    public function sortByStatutPaiement(BilletRepository $tr): Response
    {
        $billets = $tr->findAllSortedByStatutPaiement('ASC');

        return $this->render('billet/index.html.twig', [
            'billets' => $billets,
        ]);
    }


    #[Route('/sort-statut', name: 'app_billet_index_sort_statut', methods: ['GET'])]
    public function sortByStatut(BilletRepository $tr): Response
    {
        $billets = $tr->findAllSortedByStatut('ASC');

        return $this->render('billet/index.html.twig', [
            'billets' => $billets,
        ]);
    }


    #[Route('/sort-prix', name: 'app_billet_index_sort_prix', methods: ['GET'])]
    public function sortByPrix(BilletRepository $tr): Response
    {
        $billets = $tr->findAllSortedByPrix('ASC');

        return $this->render('billet/index.html.twig', [
            'billets' => $billets,
        ]);
    }


    #[Route('/sort-date', name: 'app_billet_index_sort_date', methods: ['GET'])]
    public function sortByDate(BilletRepository $tr): Response
    {
        $billets = $tr->findAllSortedByDate('ASC');

        return $this->render('billet/index.html.twig', [
            'billets' => $billets,
        ]);
    }


    #[Route('/mes_billets', name: 'app_billet_user_id', methods: ['GET'])]
    public function indexAllBillets(BilletRepository $br, Security $security): Response
    {
        $user = $security->getUser(); // get the connected user
        $data = $br->findBilletsByUserId($user->getId());

        return $this->render('billet/mesBillets.html.twig', [
            'billets' => $data,
        ]);
    }


    #[Route('/{id}/pdf', name: 'app_billet_pdf', methods: ['GET'])]     
    public function AfficheBilletPDF(BilletRepository $repo, $id)
    {
        $pdfoptions = new Options();
        $pdfoptions->set('defaultFont', 'Arial');
        $pdfoptions->setIsRemoteEnabled(true);
        

        $dompdf = new Dompdf($pdfoptions);

        $billets = $repo->find($id);

        // Check if the billet exists
        if (!$billets) {
            throw $this->createNotFoundException('Your billet does not exist');
        }

        $html = $this->renderView('billet/pdfExport.html.twig', [
            'billet' => $billets
        ]);

        $html = '<div>' . $html . '</div>';

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A6', 'landscape');
        $dompdf->render();

        $pdfOutput = $dompdf->output();

        return new Response($pdfOutput, Response::HTTP_OK, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="billetPDF.pdf"'
        ]);
    }


    #[Route('/{id}/annuler', name: 'app_billet_annuler', methods: ['GET', 'POST'])]
    public function annulerBillet(Request $request, $id, BilletRepository $br): Response
    {
        $billet = new Billet();
        $billet = $br->find($id);
        $billet->setStatus('CANCELLED');
        $br->save($billet,true);

        return $this->redirectToRoute('app_billet_user_id', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/new', name: 'app_billet_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        $billet = new Billet();
        $user = $security->getUser(); // get the connected user
        $billet->setUser($user);

        $form = $this->createForm(BilletType::class, $billet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $transport = $billet->getTransport();
            $billet->setPrix($transport->getTarif());

            if ($transport && $transport->getPlaces_libres() > 0) {
                // Décrémentation du nombre de places
                $transport->setPlaces_libres($transport->getPlaces_libres() - 1);

                $entityManager->persist($billet);
                $entityManager->flush();

                return $this->redirectToRoute('app_billet_index', [], Response::HTTP_SEE_OTHER);
            } else {
                // Flash message si plus de places
                $this->addFlash('danger', 'Aucune place disponible pour ce transport.');
            }
        }

        return $this->renderForm('billet/new.html.twig', [
            'billet' => $billet,
            'form' => $form,
        ]);
    }

    #[Route('/merci', name: 'app_billet_merci', methods: ['GET'])]
    public function merci(BilletRepository $billetRepository): Response
    {
        return $this->render('billet/merci.html.twig', [
            'billets' => $billetRepository->findAll(),
        ]);
    }

    #[Route('/front/newFront/{id}', name: 'app_billet_new_front', methods: ['GET', 'POST'])]
    public function newFront(Request $request, EntityManagerInterface $entityManager, TransportRepository $tr, $id, Security $security): Response
    {
        $billet = new Billet();
        $transport = new Transport();
        $user = $security->getUser(); // get the connected user

        $transport = $tr->find($id);
        $billet->setTransport($transport);
        $billet->setStatus("CONFIRMED");
        $billet->setPrix($transport->getTarif());
        $billet->setUser($user);

        $form = $this->createForm(BilletTypeFront::class, $billet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($transport && $transport->getPlaces_libres() > 0) {
                // Décrémentation du nombre de places
                $transport->setPlaces_libres($transport->getPlaces_libres() - 1);

                $entityManager->persist($billet);
                $entityManager->flush();

                return $this->redirectToRoute('app_billet_index', [], Response::HTTP_SEE_OTHER);
            } else {
                // Flash message si plus de places
                $this->addFlash('danger', 'Aucune place disponible pour ce transport.');
            }
        }
        

        return $this->renderForm('billet/newFront.html.twig', [
            'billet' => $billet,
            'form' => $form,
        ]);
    }


    #[Route('/{id}', name: 'app_billet_show', methods: ['GET'])]
    public function show(Billet $billet): Response
    {
        return $this->render('billet/show.html.twig', [
            'billet' => $billet,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_billet_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Billet $billet, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BilletType::class, $billet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_billet_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('billet/edit.html.twig', [
            'billet' => $billet,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_billet_delete', methods: ['POST'])]
    public function delete(Request $request, Billet $billet, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$billet->getId(), $request->request->get('_token'))) {
            $entityManager->remove($billet);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_billet_index', [], Response::HTTP_SEE_OTHER);
    }
}
