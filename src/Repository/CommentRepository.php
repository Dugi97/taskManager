<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    /**
     * @param $postId
     * @param $limit
     * @param $offset
     * @return mixed
     */
    public function returnComments($postId, $offset)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.post = :post_id')
            ->setParameter('post_id', $postId)
            ->setMaxResults(5)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult()
        ;
    }

    public function returnReplays($commentId, $offset)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.parent = :parent_id')
            ->setParameter('parent_id', $commentId)
            ->setMaxResults(5)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult()
            ;
    }
}
