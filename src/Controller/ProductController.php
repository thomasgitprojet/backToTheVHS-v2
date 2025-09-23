<?php

namespace App\Controller;

use App\Repository\PublicationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProductController extends AbstractController
{
    #[Route('/publication/{id}', name: 'app_product')]
    public function show(int $id, PublicationRepository $publicationRepository): Response
    {
        $publication = $publicationRepository->find($id);

        if (!$publication) {
            throw $this->createNotFoundException("Publication non trouvée");
        }

        return $this->render('product/product.html.twig', [
            'publication' => $publication,
        ]);
        }
}
