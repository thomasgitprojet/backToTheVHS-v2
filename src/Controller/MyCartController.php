<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MyCartController extends AbstractController
{
    #[Route('/my/cart', name: 'app_my_cart')]
    public function index(): Response
    {
        return $this->render('my_cart/my_cart.html.twig', [
            'controller_name' => 'MyCartController',
        ]);
    }
}
