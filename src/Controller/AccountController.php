<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class AccountController extends AbstractController
{
    #[Route('/monCompte', name: 'app_account')]
    #[IsGranted('ROLE_USER')]
    public function index(UserInterface $user): Response
    {
        return $this->render('account/account.html.twig', [
            'user' => $user,
        ]);
    }
}
