<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\MoviesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

final class NewProductController extends AbstractController
{
#[Route('/new/product', name: 'app_new_product')]
public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    $product = new Product();
    $form = $this->createForm(ProductType::class, $product, [
    'autocomplete_url' => $this->generateUrl('api_movies_search'),
    ]);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $imageFile = $form->get('imageFile')->getData();

        if ($imageFile) {
            $newFilename = uniqid().'.'.$imageFile->guessExtension();

            try {
                $imageFile->move(
                    $this->getParameter('uploads_directory'),
                    $newFilename
                );
            } catch (FileException $e) {
                $this->addFlash('danger', 'Erreur lors de l’upload de l’image.');
            }

            $product->setImage($newFilename); // assuming setImage(string $name) exists
        }

    // Associer le film sélectionné par ID
    $movieId = $form->get('movieId')->getData();
    if ($movieId) {
        $movie = $entityManager->getRepository(Movies::class)->find($movieId);
        if ($movie) {
            $product->setMovie($movie);
        }
    }

        $entityManager->persist($product);
        $entityManager->flush();

        $this->addFlash('success', 'Annonce publiée !');
        return $this->redirectToRoute('mon_compte'); // ou une autre route
    }

    return $this->render('new_product/new_product.html.twig', [
        'form' => $form->createView(),
    ]);
}

    #[Route('/api/movies/search', name: 'api_movies_search')]
    public function searchMovies(Request $request, MoviesRepository $moviesRepository): JsonResponse
    {
        $term = $request->query->get('term', '');

        $movies = $moviesRepository->createQueryBuilder('m')
            ->where('LOWER(m.title) LIKE :term')
            ->setParameter('term', '%' . strtolower($term) . '%')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();

        // $results = [];

        // foreach ($movies as $movie) {
        //     $results[] = $movie->getTitle();
        // }

        return new JsonResponse(array_map(fn($movie) => [
            'id' => $movie->getId(),
            'title' => $movie->getTitle(),
        ], $movies));
    }
}
