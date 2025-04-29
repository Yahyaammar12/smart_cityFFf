<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\WeatherService;
use Symfony\Component\HttpFoundation\JsonResponse;



class WeatherController extends AbstractController{
    #[Route('/weather', name: 'app_weather')]
    public function index(WeatherService $weatherService): JsonResponse
    {
        $temp = $weatherService->getTemperature(); // Optionnellement : getTemperature('Paris')

        return $this->json(['temperature' => $temp]);
    }
}
