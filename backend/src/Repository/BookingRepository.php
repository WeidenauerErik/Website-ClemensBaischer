<?php

namespace App\Repository;

use App\Entity\Booking;
use App\Entity\Trailer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Booking>
 */
class BookingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
    }

    /**
     * Bookings for a trailer that overlap the given [from, to] window (inclusive on both ends).
     *
     * @return Booking[]
     */
    public function findOverlapping(
        Trailer $trailer,
        \DateTimeImmutable $from,
        \DateTimeImmutable $to,
        ?Booking $excluding = null,
    ): array {
        $qb = $this->createQueryBuilder('b')
            ->andWhere('b.trailer = :trailer')
            ->andWhere('b.startDate <= :to')
            ->andWhere('b.endDate >= :from')
            ->setParameter('trailer', $trailer)
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->orderBy('b.startDate', 'ASC');

        if (null !== $excluding) {
            $qb->andWhere('b.id != :excludingId')->setParameter('excludingId', $excluding->getId());
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Booking[]
     */
    public function findForTrailerInRange(Trailer $trailer, \DateTimeImmutable $from, \DateTimeImmutable $to): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.trailer = :trailer')
            ->andWhere('b.startDate <= :to')
            ->andWhere('b.endDate >= :from')
            ->setParameter('trailer', $trailer)
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->orderBy('b.startDate', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
