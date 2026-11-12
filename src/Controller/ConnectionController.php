<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class ConnectionController extends AbstractController
{
    #[Route('/connection', name: 'app_connection')]
    public function index(AuthenticationUtils $authenticationUtils): Response // $authenticationUtils est une instance de la classe AuthenticationUtils, fournie par Symfony. getLastAuthenticationError() est une méthode de cette classe.
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('connection/connection.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }
}