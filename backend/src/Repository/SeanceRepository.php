<?php

namespace App\Repository;

use App\Entity\Seance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SeanceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Seance::class);
    }

    public function findUpcoming(bool $disponibleOnly = true): array
    {
        $qb = $this->createQueryBuilder('s')
            ->where('s.dateHeure > :now')
            ->setParameter('now', new \DateTime())
            ->orderBy('s.dateHeure', 'ASC');

        if ($disponibleOnly) {
            $qb->andWhere('s.disponible = true');
        }

        return $qb->getQuery()->getResult();
    }
}
