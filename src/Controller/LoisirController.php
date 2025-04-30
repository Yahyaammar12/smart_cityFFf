<?php

namespace App\Controller;

use App\Entity\Loisir;
use App\Form\LoisirType;
use App\Repository\LoisirRepository;
use App\Repository\EvenementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;
use Knp\Component\Pager\PaginatorInterface;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\ValidationException;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\Builder\QrCodeBuilder;
use Endroid\QrCode\Builder\BuilderInterface;
use Endroid\QrCode\RoundBlockSizeMode;



#[Route('/loisir')]
class LoisirController extends AbstractController
{


    //Visiteur
    #[Route('/visiteur', name: 'app_loisir_index_front_visiteur', methods: ['GET'])]
    public function indexFrontVisiteur(LoisirRepository $lr, PaginatorInterface $paginator, Request $request): Response
    {
        $data=$lr->findAll();

        $loisirs=$paginator->paginate(
            $data,
            $request->query->getInt('page',1),
            8
        );

        return $this->render('loisir/indexFrontVisiteur.html.twig', [
            'loisirs' => $loisirs,
        ]);
    }

    //Fin Visiteur



    #[Route('/', name: 'app_loisir_index', methods: ['GET'])]
    public function index(LoisirRepository $loisirRepository): Response
    {
        return $this->render('loisir/index.html.twig', [
            'loisirs' => $loisirRepository->findAll(),
        ]);
    }


    #[Route('/sort-nom', name: 'app_loisir_index_sort_nom', methods: ['GET'])]
    public function sortByNom(loisirRepository $tr): Response
    {
        $loisirs = $tr->findAllSortedByNom('ASC');

        return $this->render('loisir/index.html.twig', [
            'loisirs' => $loisirs,
        ]);
    }


    #[Route('/sort-localisation', name: 'app_loisir_index_sort_localisation', methods: ['GET'])]
    public function sortByLocalisation(loisirRepository $tr): Response
    {
        $loisirs = $tr->findAllSortedByLocalisation('ASC');

        return $this->render('loisir/index.html.twig', [
            'loisirs' => $loisirs,
        ]);
    }


    #[Route('/sort-type', name: 'app_loisir_index_sort_type', methods: ['GET'])]
    public function sortByType(loisirRepository $tr): Response
    {
        $loisirs = $tr->findAllSortedByType('ASC');

        return $this->render('loisir/index.html.twig', [
            'loisirs' => $loisirs,
        ]);
    }


    #[Route('/stat', name: 'app_loisir_index_stat', methods: ['GET'])]
    public function statloisirs(LoisirRepository $lr, EvenementRepository $er): Response
    {
        $number_events = $er->getEvenementsByLoisirId();

        return $this->render('loisir/stat.html.twig', [
            'loisirs' => $lr->findAll(),
            'number_events' => $number_events,
        ]);
    }


    #[Route('/front', name: 'app_loisir_index_front', methods: ['GET'])]
    public function indexFront(LoisirRepository $lr, PaginatorInterface $paginator, Request $request): Response
    {
        $data=$lr->findAll();

        $loisirs=$paginator->paginate(
            $data,
            $request->query->getInt('page',1),
            8
        );

        return $this->render('loisir/indexFront.html.twig', [
            'loisirs' => $loisirs,
        ]);
    }

    #[Route('/QrCode/generate/{id}', name: 'app_qr_code')]
    public function qrGenerator(ManagerRegistry $doctrine, $id, LoisirRepository $repo)
    {
        $loisir = $repo->find($id);
        // Ensure values are not null, and sanitize them
        $nom = htmlspecialchars($loisir->getNom() ?: 'No name available', ENT_QUOTES, 'UTF-8');
        $type = htmlspecialchars($loisir->getType() ?: 'No type available', ENT_QUOTES, 'UTF-8');
        $localisation = htmlspecialchars($loisir->getLocalisation() ?: 'No localisation available', ENT_QUOTES, 'UTF-8');

        // Create the data string for the QR code
        $data = "Loisir Details:\n";
        $data .= "Nom: {$nom}\n";
        $data .= "Type: {$type}\n";
        $data .= "Localisation: {$localisation}\n";
        $data .= "Enjoy your leisure time!";
        
        // Create a new QR code instance with the desired parameters
        $qrCode = new QrCode(
            $data,
            new Encoding('UTF-8'),  // Encoding
            ErrorCorrectionLevel::High,  // Error correction level
            300,  // Size
            10,  // Margin
            RoundBlockSizeMode::Margin,  // Round block size mode
            new Color(0, 0, 0),  // Foreground color (black)
            new Color(255, 255, 255)  // Background color (white)
        );

        // Create a writer to generate the PNG image
        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        // Return the PNG image as the response
        return new Response(
            $result->getString(),
            Response::HTTP_OK,
            ['content-type' => 'image/png']
        );
    }

    

    #[Route('/new', name: 'app_loisir_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        $loisir = new Loisir();
        $user = $security->getUser(); // get the connected user
        $loisir->setUser($user);

        $form = $this->createForm(LoisirType::class, $loisir);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($loisir);
            $entityManager->flush();

            return $this->redirectToRoute('app_loisir_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('loisir/new.html.twig', [
            'loisir' => $loisir,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_loisir_show', methods: ['GET'])]
    public function show(Loisir $loisir): Response
    {
        return $this->render('loisir/show.html.twig', [
            'loisir' => $loisir,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_loisir_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Loisir $loisir, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LoisirType::class, $loisir);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_loisir_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('loisir/edit.html.twig', [
            'loisir' => $loisir,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_loisir_delete', methods: ['POST'])]
    public function delete(Request $request, Loisir $loisir, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$loisir->getId(), $request->request->get('_token'))) {
            $entityManager->remove($loisir);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_loisir_index', [], Response::HTTP_SEE_OTHER);
    }
}
