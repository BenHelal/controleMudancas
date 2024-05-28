<?php

namespace App\Repository;

use App\Entity\DateTermine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DateTermine>
 *
 * @method DateTermine|null find($id, $lockMode = null, $lockVersion = null)
 * @method DateTermine|null findOneBy(array $criteria, array $orderBy = null)
 * @method DateTermine[]    findAll()
 * @method DateTermine[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DateTermineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DateTermine::class);
    }

//    /**
//     * @return DateTermine[] Returns an array of DateTermine objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DateTermine
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
