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
use App\Security\Voter\ProductVoter;

final class ProductManageController extends AbstractController
{
    #[Route('/new/product', name: 'app_new_product')]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger,
        ImageManager $imageManager
    ): Response {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product, [
            'image_required' => true,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // 1. Vérifier le film AVANT de toucher au disque
            $movieId = $form->get('movieId')->getData();
            $movie = $movieId ? $entityManager->getRepository(Movies::class)->find($movieId) : null;

            if (!$movie) {
                $this->addFlash('danger', 'Veuillez sélectionner un film dans la liste proposée.');

                return $this->render('new_product/new_product.html.twig', [
                    'form' => $form->createView(),
                ]);
            }

            $product->setMovie($movie);
            $product->setSlug($slugger->slug($form->get('movieTitle')->getData())->lower());

            // 2. Enregistrer l'image sur le disque (si une image a été envoyée)
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                try {
                    $product->setImage($imageManager->uploadImage($imageFile));
                } catch (FileException $e) {
                    $this->addFlash('danger', "Erreur lors de l'upload de l'image.");

                    return $this->render('new_product/new_product.html.twig', [
                        'form' => $form->createView(),
                    ]);
                }
            }

            // 3. Préparer la publication liée
            $publication = new Publication();
            $publication->setProduct($product);
            $publication->setAccount($this->getUser());
            $publication->setDatePublication(new \DateTime());

            // 4. Tout enregistrer en base en une seule fois
            try {
                $entityManager->persist($product);
                $entityManager->persist($publication);
                $entityManager->flush();
            } catch (\Throwable $e) {
                // La base n'a rien enregistré : on supprime l'image devenue orpheline
                if ($product->getImage()) {
                    $imageManager->deleteImage($product->getImage());
                }

                throw $e;
            }

            $this->addFlash('success', 'Annonce publiée !');

            return $this->redirectToRoute('app_account');
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

        // Seul le propriétaire peut supprimer le produit (voir ProductVoter)
        $this->denyAccessUnlessGranted(ProductVoter::DELETE, $product, 'Vous ne pouvez pas supprimer ce produit.');

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
        // Seul le propriétaire peut modifier le produit (voir ProductVoter)
        $this->denyAccessUnlessGranted(ProductVoter::EDIT, $product, 'Vous ne pouvez pas modifier ce produit.');

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
