<?php

namespace App\Repository;

use App\Entity\DepartemantMudancass;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DepartemantMudancas>
 *
 * @method DepartemantMudancas|null find($id, $lockMode = null, $lockVersion = null)
 * @method DepartemantMudancas|null findOneBy(array $criteria, array $orderBy = null)
 * @method DepartemantMudancas[]    findAll()
 * @method DepartemantMudancas[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DepartemantMudancasRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DepartemantMudancass::class);
    }

    public function add(DepartemantMudancass $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DepartemantMudancass $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return DepartemantMudancass[] Returns an array of DepartemantMudancas objects
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

//    public function findOneBySomeField($value): ?DepartemantMudancas
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
