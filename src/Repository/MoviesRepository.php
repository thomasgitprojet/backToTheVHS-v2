<?php

namespace App\Repository;

use App\Entity\Movies;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Movies>
 */
class MoviesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Movies::class);
    }

    /** @return list<Movies> */
    public function findByTitleLike(string $term): array
    {
    return $this->createQueryBuilder('m')
        ->where('LOWER(m.title) LIKE :term')
        ->setParameter('term', '%' . strtolower($term) . '%')
        ->setMaxResults(10)
        ->getQuery()
        ->getResult();
    }
}
