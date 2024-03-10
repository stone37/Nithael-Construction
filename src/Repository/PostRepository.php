<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

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

    public function save(Post $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Post $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findRecent(int $limit): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.online = true')
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function queryAll(?Category $category = null): Query
    {
        $qb = $this->createQueryBuilder('p')
            ->where('p.online = true')
            ->orderBy('p.createdAt', 'DESC');

        if ($category) {
            $qb = $qb
                ->andWhere('p.category = :category')
                ->setParameter('category', $category);
        }

        return $qb->getQuery();
    }

    public function getNumber(): int
    {
        return (int) $this->createQueryBuilder('p')
            ->select('count(p.id)')
            ->where('p.online = true')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
