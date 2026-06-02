<?php

namespace App\Repository;

use App\Entity\Reservation;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('r')
            ->leftJoin('r.seance', 's')
            ->addSelect('s')
            ->where('r.user = :userId')
            ->setParameter('userId', $user->getId(), 'uuid')
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findActiveForSeanceAndUser(string $seanceId, User $user): ?Reservation
    {
        return $this->createQueryBuilder('r')
            ->where('r.seance = :seance')
            ->andWhere('r.user = :user')
            ->andWhere('r.statut != :cancelled')
            ->setParameter('seance', $seanceId)
            ->setParameter('user', $user)
            ->setParameter('cancelled', 'annulee')
            ->getQuery()
            ->getOneOrNullResult();
    }
}
