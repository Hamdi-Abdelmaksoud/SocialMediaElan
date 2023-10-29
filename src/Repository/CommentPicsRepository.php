<?php

namespace App\Repository;

use App\Entity\CommentPics;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CommentPics>
 *
 * @method CommentPics|null find($id, $lockMode = null, $lockVersion = null)
 * @method CommentPics|null findOneBy(array $criteria, array $orderBy = null)
 * @method CommentPics[]    findAll()
 * @method CommentPics[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentPicsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CommentPics::class);
    }

//    /**
//     * @return CommentPics[] Returns an array of CommentPics objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?CommentPics
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
