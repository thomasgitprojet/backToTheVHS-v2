<?php

namespace App\Controller;

use App\Repository\PublicationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

final class MyCartController extends AbstractController
{
    // Route pour afficher la page du panier
    #[Route('/my/cart', name: 'app_my_cart')]
    public function index(SessionInterface $session, PublicationRepository $publicationRepository): Response
    {
        // Récupérer les IDs des produits dans le panier
        $cartIds = $session->get('cart', []);

        // Récupérer les publications correspondantes
        $cartItems = $publicationRepository->findBy(['id' => $cartIds]);

        return $this->render('my_cart/my_cart.html.twig', [
            'cartItems' => $cartItems,
        ]);
    }

    #[Route('/cart/add/{id}', name: 'app_cart_add')]
    public function add(
        int $id,
        PublicationRepository $publicationRepository,
        SessionInterface $session
    ): JsonResponse {
        // Vérifie que l'utilisateur est connecté
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // Récupère le produit
        $publication = $publicationRepository->find($id);
        if (!$publication) {
            return $this->json([
                'count' => $session->get('cart', []) ? count($session->get('cart')) : 0,
                'message' => 'Produit introuvable.',
                'type' => 'danger'
            ]);
        }

        // Récupère le panier ou crée un tableau vide
        $cart = $session->get('cart', []);
        $cart[] = $id;
        $session->set('cart', $cart);

        // Renvoie JSON pour AJAX
        return $this->json([
            'count' => count($cart),
            'message' => $publication->getProduct()->getMovie()->getTitle() . ' ajouté au panier !',
            'type' => 'success'
        ]);
    }
}