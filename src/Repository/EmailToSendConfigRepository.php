<?php

namespace App\Repository;

use App\Entity\EmailToSendConfig;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EmailToSendConfig>
 *
 * @method EmailToSendConfig|null find($id, $lockMode = null, $lockVersion = null)
 * @method EmailToSendConfig|null findOneBy(array $criteria, array $orderBy = null)
 * @method EmailToSendConfig[]    findAll()
 * @method EmailToSendConfig[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmailToSendConfigRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EmailToSendConfig::class);
    }

//    /**
//     * @return EmailToSendConfig[] Returns an array of EmailToSendConfig objects
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

//    public function findOneBySomeField($value): ?EmailToSendConfig
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
