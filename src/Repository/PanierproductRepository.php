<?php

namespace App\Repository;

use App\Entity\Panierproduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Panierproduct>
 *
 * @method Panierproduct|null find($id, $lockMode = null, $lockVersion = null)
 * @method Panierproduct|null findOneBy(array $criteria, array $orderBy = null)
 * @method Panierproduct[]    findAll()
 * @method Panierproduct[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PanierproductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Panierproduct::class);
    }

//    /**
//     * @return Panierproduct[] Returns an array of Panierproduct objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Panierproduct
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
