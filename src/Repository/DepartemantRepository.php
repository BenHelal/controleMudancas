<?php

namespace App\Repository;

use App\Entity\Departemant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Departemant>
 *
 * @method Departemant|null find($id, $lockMode = null, $lockVersion = null)
 * @method Departemant|null findOneBy(array $criteria, array $orderBy = null)
 * @method Departemant[]    findAll()
 * @method Departemant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DepartemantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Departemant::class);
    }

    public function add(Departemant $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Departemant $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function search(string $name): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.name like :val')
            ->setParameter('val', "%$name%")
            ->getQuery()
            ->getResult()
        ;
    }

//    /**
//     * @return Departemant[] Returns an array of Departemant objects
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

//    public function findOneBySomeField($value): ?Departemant
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
