<?php

namespace App\Repository;

use App\Entity\Projevisa;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Projevisa>
 *
 * @method Projevisa|null find($id, $lockMode = null, $lockVersion = null)
 * @method Projevisa|null findOneBy(array $criteria, array $orderBy = null)
 * @method Projevisa[]    findAll()
 * @method Projevisa[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjevisaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Projevisa::class);
    }

//    /**
//     * @return Projevisa[] Returns an array of Projevisa objects
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

//    public function findOneBySomeField($value): ?Projevisa
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
