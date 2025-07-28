<?php

namespace App\Controller;

use App\Repository\PublicationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(PublicationRepository $publicationRepository): Response
    {
        // Récupère les 8 dernières publications par date de publication décroissante
        $latestPublications = $publicationRepository->findBy([], ['datePublication' => 'DESC'], 8);

        return $this->render('home/index.html.twig', [
            'publications' => $latestPublications,
        ]);
    }
}
