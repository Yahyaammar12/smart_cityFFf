<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\UserTypeFront;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;




#[Route('/user')]
class UserController extends AbstractController
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }


    #[Route('/sort-nom', name: 'app_user_index_sort_nom', methods: ['GET'])]
    public function sortByNom(UserRepository $ur, PaginatorInterface $paginator, Request $request): Response
    {
        $data = $ur->findAllSortedByNom('ASC');

        $users=$paginator->paginate(
            $data,
            $request->query->getInt('page',1),
            5
        );

        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/sort-date', name: 'app_user_index_sort_date', methods: ['GET'])]
    public function sortByDate(UserRepository $ur, PaginatorInterface $paginator, Request $request): Response
    {
        $data = $ur->findAllSortedByCreatedAt('ASC');

        $users=$paginator->paginate(
            $data,
            $request->query->getInt('page',1),
            5
        );

        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/sort-role', name: 'app_user_index_sort_role', methods: ['GET'])]
    public function sortByRole(UserRepository $ur, PaginatorInterface $paginator, Request $request): Response
    {
        $data = $ur->findAllSortedByRole('ASC');

        $users=$paginator->paginate(
            $data,
            $request->query->getInt('page',1),
            5
        );

        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/sort-adresse', name: 'app_user_index_sort_adresse', methods: ['GET'])]
    public function sortByAdresse(UserRepository $ur, PaginatorInterface $paginator, Request $request): Response
    {
        $data = $ur->findAllSortedByAdresse('ASC');

        $users=$paginator->paginate(
            $data,
            $request->query->getInt('page',1),
            5
        );
        

        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }


    #[Route('/stat', name: 'app_user_index_stat', methods: ['GET'])]
    public function statUsers(UserRepository $ur, Request $request): Response
    {
        $data_membership = $ur->getMembershipDurationInMonths();
        
        // Prepare the data for the JavaScript variables
        $membershipData = [];
        foreach ($data_membership as $membership) {
            $user = $ur->find($membership['user_id']);
            $membershipData[] = [
                'user_name' => $user->getNom(),  // Assuming the User entity has a getName() method
                'membership_count' => $membership['membership_duration_in_months']
            ];
        }

        $users=$ur->findAll();

        return $this->render('user/stat.html.twig', [
            'users' => $users,
            'membershipData' => $membershipData
        ]);
    }



    #[Route('/liste', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $ur, Request $request, PaginatorInterface $paginator): Response
    {
        $data_membership = $ur->getMembershipDurationInMonths();

        $data= $ur->findAll();
        
        // Prepare the data for the JavaScript variables
        $membershipData = [];
        foreach ($data_membership as $membership) {
            $user = $ur->find($membership['user_id']);
            $membershipData[] = [
                'user_name' => $user->getNom(),  // Assuming the User entity has a getName() method
                'membership_count' => $membership['membership_duration_in_months']
            ];
        }

        $users=$paginator->paginate(
            $data,
            $request->query->getInt('page',1),
            5
        );

        return $this->render('user/index.html.twig', [
            'users' => $users,
            'membershipData' => $membershipData
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Cryptage du mot de passe
            $plainPassword = $user->getMotDePasse();
            $encodedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
            $user->setMotDePasse($encodedPassword);

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }


    #[Route('/{id}/ban', name: 'app_user_ban', methods: ['GET', 'POST'])]
    public function banUser(Request $request, $id, UserRepository $ur): Response
    {
        $user = new User();
        $user = $ur->find($id);
        if($user->isActif()==true){
            $user->setActif(false);
        }else{
            $user->setActif(true);
        }
        $ur->save($user,true);

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Si le mot de passe a été modifié, on le crypte à nouveau
            if ($user->getMotDePasse()) {
                $plainPassword = $user->getMotDePasse();
                $encodedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
                $user->setMotDePasse($encodedPassword);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }


    #[Route('/{id}/edit/front', name: 'app_user_edit_front', methods: ['GET', 'POST'])]
    public function editFront(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserTypeFront::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Si le mot de passe a été modifié, on le crypte à nouveau
            if ($user->getMotDePasse()) {
                $plainPassword = $user->getMotDePasse();
                $encodedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
                $user->setMotDePasse($encodedPassword);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_front', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/editFront.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
    

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
