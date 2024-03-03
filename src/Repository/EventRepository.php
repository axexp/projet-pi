<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Event>
 *
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function findBySearchQuery(string $searchQuery): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.type LIKE :query')
            ->setParameter('query', '%' . $searchQuery . '%')
            ->getQuery()
            ->getResult();
    }

    public function countAllParticipants(): int
    {
        return $this->createQueryBuilder('e')
            ->select('COUNT(p.id) as participantCount')
            ->leftJoin('e.participants', 'p') // Assuming you have a property named 'participants' in your Event entity
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countAllEvents(): int
    {
        return $this->createQueryBuilder('e')
            ->select('COUNT(e.id) as eventCount')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countAllComments(): int
    {
        return $this->createQueryBuilder('e')
            ->select('COUNT(c.id) as commentCount')
            ->leftJoin('e.comments', 'c')
            ->getQuery()
            ->getSingleScalarResult();
    }
  
/*
public function searchEvents($searchQuery, $limit, $offset)
{
    $qb = $this->createQueryBuilder('e')
        ->andWhere('e.name LIKE :searchQuery OR e.type LIKE :searchQuery')
        ->setParameter('searchQuery', '%' . $searchQuery . '%')
        ->orderBy('e.datedebut', 'ASC')
        ->setMaxResults($limit)
        ->setFirstResult($offset);

    // Add other criteria as needed

    return $qb->getQuery()->getResult();
}

public function countFilteredEvents($searchQuery)
{
    $qb = $this->createQueryBuilder('e')
        ->select('COUNT(e.id)')
        ->andWhere('e.name LIKE :searchQuery OR e.type LIKE :searchQuery')
        ->setParameter('searchQuery', '%' . $searchQuery . '%');

    // Add other criteria as needed

    return $qb->getQuery()->getSingleScalarResult();
}*/

//    /**
//     * @return Event[] Returns an array of Event objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Event
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

}
