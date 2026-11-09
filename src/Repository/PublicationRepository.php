<?php

namespace App\Repository;

use App\Entity\Publication;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use SortDirection;

/**
 * @extends ServiceEntityRepository<Publication>
 */
class PublicationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Publication::class);
    }

    public function findFilteredPublications(?string $search = null, ?string $sort = null)
    {
        $qb = $this->createQueryBuilder('p')
            ->join('p.product', 'prod')
            ->join('prod.movie', 'm')
            ->addSelect('prod', 'm');

        // Filtre recherche
        if ($search) {
            $qb->andWhere('m.title LIKE :search')
            ->setParameter('search', '%' . $search . '%');
        }

        // Tri
        switch ($sort) {
            case 'price_asc':
                $qb->orderBy('prod.priceProduct', SortDirection::Ascending);
                break;
            case 'price_desc':
                $qb->orderBy('prod.priceProduct', SortDirection::Descending);
                break;
            case 'title_asc':
                $qb->orderBy('m.title', SortDirection::Ascending);
                break;
            case 'title_desc':
                $qb->orderBy('m.title', SortDirection::Descending);
                break;
            default:
                $qb->orderBy('p.datePublication', SortDirection::Descending);
        }

        return $qb; // retourne le QueryBuilder, pas getQuery()
    }
}
