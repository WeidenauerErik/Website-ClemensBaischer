<?php

namespace App\Repository;

use App\Entity\Trailer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Trailer>
 */
class TrailerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trailer::class);
    }

    public function findOneActiveBySlug(string $slug): ?Trailer
    {
        return $this->findOneBy(['slug' => $slug, 'active' => true]);
    }

    /** @return Trailer[] */
    public function findAllActive(): array
    {
        return $this->findBy(['active' => true], ['name' => 'ASC']);
    }
}
