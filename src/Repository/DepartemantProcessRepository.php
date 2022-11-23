<?php

namespace App\Repository;

use App\Entity\DepartemantProcess;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DepartemantProcess>
 *
 * @method DepartemantProcess|null find($id, $lockMode = null, $lockVersion = null)
 * @method DepartemantProcess|null findOneBy(array $criteria, array $orderBy = null)
 * @method DepartemantProcess[]    findAll()
 * @method DepartemantProcess[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DepartemantProcessRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DepartemantProcess::class);
    }

    public function add(DepartemantProcess $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DepartemantProcess $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return DepartemantProcess[] Returns an array of DepartemantProcess objects
     */
    public function findByExampleField($value): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.mudancas_id = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

//    public function findOneBySomeField($value): ?DepartemantProcess
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
