<?php

namespace App\Controller;

use App\Entity\Publication;
use App\Entity\Product;
use App\Entity\Movies;
use App\Form\ProductType;
use App\Repository\MoviesRepository;
use App\Service\ImageManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

final class ProductManageController extends AbstractController
{
    #[Route('/new/product', name: 'app_new_product')]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);

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

            // Création de la publication liée
            $publication = new Publication();
            $publication->setProduct($product);
            $publication->setAccount($this->getUser()); 
            $publication->setDatePublication(new \DateTime());

            $entityManager->persist($publication);
            $entityManager->flush();

            $this->addFlash('success', 'Annonce publiée !');
            return $this->redirectToRoute('app_account'); // ou une autre route
        }

        return $this->render('new_product/new_product.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/account/product/delete/{id}', name: 'app_delete_product', methods: ['POST'])]
    public function deleteProduct(
        Product $product,
        EntityManagerInterface $entityManager,
        ImageManager $imageManager,
        Request $request
    ): Response {
        // Protection CSRF (recommandée)
        if (!$this->isCsrfTokenValid('delete' . $product->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        // Vérifier que l'utilisateur est propriétaire du produit AVANT toute suppression
        if ($product->getPublication()?->getAccount() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas supprimer ce produit.');
        }

        // Supprimer l’image si elle existe
        if ($product->getImage()) {
            $imageManager->deleteImage($product->getImage());
        }

        // Supprimer la publication associée
        $publication = $product->getPublication();
        if ($publication) {
            $entityManager->remove($publication);
        }

        $entityManager->remove($product);
        $entityManager->flush();

        $this->addFlash('success', 'Produit supprimé avec succès.');

        return $this->redirectToRoute('app_account');
    }
    #[Route('/account/product/edit/{id}', name: 'app_edit_product')]
    public function editProduct(
        Product $product,
        Request $request,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger,
        ImageManager $imageManager
    ): Response {
        // Vérification : l'utilisateur est-il bien le propriétaire ?
        if ($product->getPublication()?->getAccount() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas modifier ce produit.');
        }

        // Pré-remplir les champs unmapped
       $form = $this->createForm(ProductType::class, $product);

        // Préremplir les champs unmapped après la création du formulaire
        $form->get('movieTitle')->setData($product->getMovie()?->getTitle());
        $form->get('movieId')->setData($product->getMovie()?->getId());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $movieTitle = $form->get('movieTitle')->getData();
            $movieId = $form->get('movieId')->getData();

            if (!$movieId) {
                $this->addFlash('danger', 'Veuillez sélectionner un film valide.');
                return $this->render('new_product/new_product.html.twig', [
                    'form' => $form->createView(),
                    'editMode' => true,
                ]);
            }

            // Met à jour le film lié
            $movie = $entityManager->getRepository(Movies::class)->find($movieId);
            if (!$movie) {
                $this->addFlash('danger', 'Film sélectionné invalide.');
                return $this->render('new_product/new_product.html.twig', [
                    'form' => $form->createView(),
                    'editMode' => true,
                ]);
            }

            $product->setMovie($movie);
            $product->setSlug($slugger->slug($movieTitle)->lower());

            // Gestion image : remplacer si une nouvelle est uploadée
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                // Supprime l’ancienne
                if ($product->getImage()) {
                    $imageManager->deleteImage($product->getImage());
                }

                $newFilename = uniqid() . '.' . $imageFile->guessExtension();
                try {
                    $imageFile->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                    $product->setImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('danger', 'Erreur lors de l’upload de la nouvelle image.');
                }
            }

            $entityManager->flush();
            $this->addFlash('success', 'Produit modifié avec succès.');

            return $this->redirectToRoute('app_account');
        }

        return $this->render('new_product/new_product.html.twig', [
            'form' => $form->createView(),
            'editMode' => true,
        ]);
    }
}
