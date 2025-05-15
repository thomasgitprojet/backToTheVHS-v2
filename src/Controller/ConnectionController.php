<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ConnectionController extends AbstractController
{
    #[Route('/connexion', name: 'app_connection')]
    public function index(): Response
    {
        return $this->render('connection/connection.html.twig', [
            'controller_name' => 'ConnectionController',
        ]);
    }
}
