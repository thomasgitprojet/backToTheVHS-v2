<?php

namespace App\Controller;

use App\Repository\PublicationRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProductsController extends AbstractController
{
    #[Route('/products', name: 'app_products')]
    public function index(
        PublicationRepository $publicationRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        // Récupération des filtres avec fallback
        $search = $request->query->get('search', '');
        $sort   = $request->query->get('filter_sort', 'default'); // valeur par défaut

        $queryBuilder = $publicationRepository->findFilteredPublications($search, $sort);

        $publications = $paginator->paginate(
            $queryBuilder, 
            $request->query->getInt('page', 1), 
            12,
            [
                'sortableFields' => [], // <-- empêche KnpPaginator de trier lui-même
            ]
        );

        return $this->render('products/products.html.twig', [
            'publications' => $publications,
            'search' => $search,
            'sort' => $sort,
        ]);
    }
}
