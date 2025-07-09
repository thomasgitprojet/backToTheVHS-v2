<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Movies;
use App\Form\ProductType;
use App\Repository\MoviesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

final class NewProductController extends AbstractController
{
    #[Route('/new/product', name: 'app_new_product')]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product, [
            'autocomplete_url' => $this->generateUrl('movies_autocomplete'),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer le titre du film (champ unmapped)
            $movieTitle = $form->get('movieTitle')->getData();

            // Générer et setter le slug à partir du titre
            $slug = $slugger->slug($movieTitle)->lower();
            $product->setSlug($slug);

            // Gestion de l'image
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('danger', 'Erreur lors de l’upload de l’image.');
                }

                $product->setImage($newFilename);
            }

            // Validation stricte de movieId
            $movieId = $form->get('movieId')->getData();
            if (!$movieId) {
                $this->addFlash('danger', 'Veuillez sélectionner un film dans la liste proposée.');
                return $this->render('new_product/new_product.html.twig', [
                    'form' => $form->createView(),
                ]);
            }

            $movie = $entityManager->getRepository(Movies::class)->find($movieId);
            if (!$movie) {
                $this->addFlash('danger', 'Film sélectionné invalide.');
                return $this->render('new_product/new_product.html.twig', [
                    'form' => $form->createView(),
                ]);
            }

            $product->setMovie($movie);

            $entityManager->persist($product);
            $entityManager->flush();

            $this->addFlash('success', 'Annonce publiée !');
            return $this->redirectToRoute('app_account'); // ou une autre route
        }

        return $this->render('new_product/new_product.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
