<?php

namespace App\Repository;

use App\Entity\IALog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<IALog>
 *
 * @method IALog|null find($id, $lockMode = null, $lockVersion = null)
 * @method IALog|null findOneBy(array $criteria, array $orderBy = null)
 * @method IALog[]    findAll()
 * @method IALog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IALogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IALog::class);
    }

//    /**
//     * @return IALog[] Returns an array of IALog objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?IALog
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
