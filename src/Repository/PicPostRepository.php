<?php

namespace App\Repository;

use App\Entity\PicPost;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PicPost>
 *
 * @method PicPost|null find($id, $lockMode = null, $lockVersion = null)
 * @method PicPost|null findOneBy(array $criteria, array $orderBy = null)
 * @method PicPost[]    findAll()
 * @method PicPost[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PicPostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PicPost::class);
    }

//    /**
//     * @return PicPost[] Returns an array of PicPost objects
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

//    public function findOneBySomeField($value): ?PicPost
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
