<?php

namespace App\Repository;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Common\Collections\Collection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Post>
 *
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }
    public function findAllWithComments(): array
    {
        return $this->createQueryBuilder('p') //alias to current entity
            ->addSelect('c') //select all the fields for the comment entity realated to this post
            ->leftJoin('p.comments', 'c') //c alias we give it to p.comments
            ->orderBy('p.created', 'DESC')
            ->getQuery()
            ->getResult();
    }
  
    /*public function findALlByAuthor(
        int| User $author
    ): array {
        return $this->findAllQuerry(
            withComments: true,
            withLikes: true,
            withAuthors: true

        )->where('p.author= :author')
            ->setParameter(
                'author',
                $author instanceof User ? $author->getId() : $author
            )
            ->getQuery()->getResult();
    }
    public function findAllQuerry(
        bool $withComments = false,
        bool $withLikes = false,
        bool $withAuthors = false,

    ): QueryBuilder {
        $query = $this->createQueryBuilder('p');
        if ($withComments) {
            $query->leftJoin('p.comments', 'c')
                ->addSelect('c');
        }
        if ($withLikes) {
            $query->leftJoin('p.likedBy', 'l')
                ->addSelect('l');
        }
        if ($withAuthors) {
            $query->leftJoin('p.author', 'a')
                ->addSelect('a');
        }
        return $query->orderBy('p.created', 'DESC');
    }
    public function findPostsFromFollows(
        Collection| array $authors
    ): array {
        return $this->findAllQuerry(
            withComments: true,
            withLikes: true,
            withAuthors: true

        )->where('p.author IN (:authors)')
            ->setParameter(
                'authors',
                $authors
            )->getQuery()->getResult();
    }*/
    //    /**
    //     * @return Post[] Returns an array of Post objects
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

    //    public function findOneBySomeField($value): ?Post
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
