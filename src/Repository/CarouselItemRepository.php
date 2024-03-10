<?php

namespace App\Repository;

use App\Entity\CarouselItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CarouselItem>
 *
 * @method CarouselItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method CarouselItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method CarouselItem[]    findAll()
 * @method CarouselItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CarouselItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CarouselItem::class);
    }

    public function save(CarouselItem $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CarouselItem $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function queryAll(): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.enabled = true')
            ->orderBy('c.position', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
