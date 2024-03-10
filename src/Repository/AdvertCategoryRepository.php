<?php

namespace App\Repository;

use App\Entity\AdvertCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AdvertCategory>
 *
 * @method AdvertCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method AdvertCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method AdvertCategory[]    findAll()
 * @method AdvertCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdvertCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdvertCategory::class);
    }

    public function save(AdvertCategory $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(AdvertCategory $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getEnabledBySlug(string $slug): ?AdvertCategory
    {
        return $this->createQueryBuilder('c')
            ->where('c.enabled = true')
            ->andWhere('c.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
