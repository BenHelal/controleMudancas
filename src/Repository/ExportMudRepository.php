<?php

namespace App\Repository;

use App\Entity\ExportMud;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ExportMud>
 *
 * @method ExportMud|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExportMud|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExportMud[]    findAll()
 * @method ExportMud[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExportMudRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExportMud::class);
    }

//    /**
//     * @return ExportMud[] Returns an array of ExportMud objects
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

//    public function findOneBySomeField($value): ?ExportMud
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
