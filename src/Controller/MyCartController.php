<?php

namespace App\Controller;

use App\Repository\PublicationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;

final class MyCartController extends AbstractController
{
    #[Route('/my/cart', name: 'app_my_cart')]
    public function index(SessionInterface $session, PublicationRepository $publicationRepository): Response
    {
        $cartIds = $session->get('cart', []);
        $cartItems = $publicationRepository->findBy(['id' => $cartIds]);

        return $this->render('my_cart/my_cart.html.twig', [
            'cartItems' => $cartItems,
        ]);
    }

    #[Route('/cart/add/{id}', name: 'app_cart_add', methods: ['POST'])]
    public function add(int $id, PublicationRepository $publicationRepository, SessionInterface $session): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $publication = $publicationRepository->find($id);
        if (!$publication) {
            return $this->json([
                'count' => count($session->get('cart', [])),
                'message' => 'Produit introuvable.',
                'type' => 'danger'
            ]);
        }

        // Récupère le panier
        $cart = $session->get('cart', []);

        // Vérifie si l’ID existe déjà
        if (!in_array($id, $cart, true)) {
            $cart[] = $id;
            $session->set('cart', $cart);

            return $this->json([
                'count' => count($cart),
                'message' => $publication->getProduct()->getMovie()->getTitle() . ' ajouté au panier !',
                'type' => 'success'
            ]);
        }

        // Si déjà présent → feedback utilisateur
        return $this->json([
            'count' => count($cart),
            'message' => 'Cet article est déjà dans votre panier.',
            'type' => 'warning'
        ]);
    }

    #[Route('/cart/remove/{id}', name: 'cart_remove', methods: ['POST'])]
    public function remove(int $id, SessionInterface $session): JsonResponse
    {
        $cart = $session->get('cart', []);

        if (($key = array_search($id, $cart)) !== false) {
            unset($cart[$key]);
            $session->set('cart', array_values($cart));
            $message = 'Le produit a été retiré du panier.';
            $type = 'success';
        } else {
            $message = 'Le produit n’était pas dans le panier.';
            $type = 'danger';
        }

        return $this->json([
            'count' => count($cart),
            'message' => $message,
            'type' => $type
        ]);
    }

    #[Route('/cart/checkout', name: 'app_cart_checkout')]
    public function checkout(SessionInterface $session): Response
    {
        // On vide le panier après la commande fictive
        $session->remove('cart');

        return $this->render('my_cart/thank_you.html.twig');
    }
}