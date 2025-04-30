<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\EvenementRepository;
use Symfony\Component\HttpFoundation\JsonResponse;



final class ApiEvenementController extends AbstractController
{
    #[Route('/api/evenements', name: 'api_evenements')]
    public function index(EvenementRepository $repo): JsonResponse
    {
        $evenements = $repo->findAll();

        $data = array_map(function ($event) {
            return [
                'title' => $event->getNom(),
                'start' => $event->getDate()->format('Y-m-d'),
            ];
        }, $evenements);

        return new JsonResponse($data);
    }
}
