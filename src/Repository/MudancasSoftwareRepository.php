<?php

namespace App\Repository;

use App\Entity\MudancasSoftware;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MudancasSoftware>
 *
 * @method MudancasSoftware|null find($id, $lockMode = null, $lockVersion = null)
 * @method MudancasSoftware|null findOneBy(array $criteria, array $orderBy = null)
 * @method MudancasSoftware[]    findAll()
 * @method MudancasSoftware[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MudancasSoftwareRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MudancasSoftware::class);
    }

    public function save(MudancasSoftware $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(MudancasSoftware $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return MudancasSoftware[] Returns an array of MudancasSoftware objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?MudancasSoftware
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
