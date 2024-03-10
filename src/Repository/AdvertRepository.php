<?php

namespace App\Repository;

use App\Entity\Advert;
use App\Model\Admin\AdvertSearch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Advert>
 *
 * @method Advert|null find($id, $lockMode = null, $lockVersion = null)
 * @method Advert|null findOneBy(array $criteria, array $orderBy = null)
 * @method Advert[]    findAll()
 * @method Advert[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdvertRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Advert::class);
    }

    public function add(Advert $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Advert $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    public function getAdmin(AdvertSearch $search)
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.category', 'category')
            ->leftJoin('a.subCategory', 'subCategory')
            ->leftJoin('a.reads', 'reads')
            ->leftJoin('a.pictures', 'pictures')
            ->leftJoin('a.messages', 'messages')
            ->addSelect('category')
            ->addSelect('subCategory')
            ->addSelect('reads')
            ->addSelect('pictures')
            ->addSelect('messages')
            ->orderBy('a.position', 'asc');

        if ($search->getCity()) {
            $qb->andWhere('a.city = :city')->setParameter('city', $search->getCity());
        }

        if ($search->getCategory())
            $qb->andWhere('category.id = :category_id')->setParameter('category_id', (int)$search->getCategory());

        if ($search->getSubCategory())
            $qb->andWhere('subCategory.id = :sub_category_id')->setParameter('sub_category_id', (int)$search->getSubCategory());

        return $qb;
    }
}
