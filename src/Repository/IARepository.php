<?php

namespace App\Repository;

use App\Entity\IA;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<IA>
 *
 * @method IA|null find($id, $lockMode = null, $lockVersion = null)
 * @method IA|null findOneBy(array $criteria, array $orderBy = null)
 * @method IA[]    findAll()
 * @method IA[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IARepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IA::class);
    }

    /**
     * Check if a relation between IA, IA_Person, and Person exists based on names.
     *
     * @param string $name
     * @return bool
     */
    public function relationExists(string $name): bool
    {
        $result = $this->createQueryBuilder('a')
            ->join('a.iaPersons', 'b')
            ->join('b.person', 'p')
            ->andWhere('p.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getResult();
    
        return !empty($result);
    }
//    /**
//     * @return IA[] Returns an array of IA objects
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

//    public function findOneBySomeField($value): ?IA
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
