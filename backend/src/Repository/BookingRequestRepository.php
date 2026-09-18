<?php

namespace App\Repository;

use App\Entity\BookingRequest;
use App\Enum\BookingRequestStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BookingRequest>
 */
class BookingRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BookingRequest::class);
    }

    /** @return BookingRequest[] */
    public function findByStatus(?BookingRequestStatus $status): array
    {
        $qb = $this->createQueryBuilder('r')->orderBy('r.createdAt', 'DESC');

        if (null !== $status) {
            $qb->andWhere('r.status = :status')->setParameter('status', $status);
        }

        return $qb->getQuery()->getResult();
    }
}
