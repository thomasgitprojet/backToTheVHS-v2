<?php

namespace App\Controller;

use App\Repository\MoviesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MovieController extends AbstractController
{
    #[Route('/movies/autocomplete', name: 'movies_autocomplete')]
    public function autocomplete(Request $request, MoviesRepository $moviesRepository): JsonResponse
    {
        $term = $request->query->get('term');

        $movies = $moviesRepository->findByTitleLike($term);

        return new JsonResponse(array_map(fn($movie) => [
            'id' => $movie->getId(),
            'title' => $movie->getTitle(),
        ], $movies));
    }
}
