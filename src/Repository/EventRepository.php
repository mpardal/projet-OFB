<?php

namespace App\Repository;

use App\Entity\Admin;
use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function getLastEvent(EntityManagerInterface $entityManager): ?array
    {
        $qb = $entityManager->createQueryBuilder();

        $qb->select('e')
            ->from(Event::class, 'e')
            ->orderBy('e.id', 'DESC')
            ->setMaxResults(1);

        return $qb->getQuery()->getResult();
    }
}