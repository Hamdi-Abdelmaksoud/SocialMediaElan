<?php

namespace App\Repository;

use App\Entity\PostPics;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PostPics>
 *
 * @method PostPics|null find($id, $lockMode = null, $lockVersion = null)
 * @method PostPics|null findOneBy(array $criteria, array $orderBy = null)
 * @method PostPics[]    findAll()
 * @method PostPics[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostPicsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PostPics::class);
    }

//    /**
//     * @return PostPics[] Returns an array of PostPics objects
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

//    public function findOneBySomeField($value): ?PostPics
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
