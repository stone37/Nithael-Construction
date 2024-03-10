<?php

namespace App\Repository;

use App\Entity\Advert;
use App\Model\Admin\AdvertSearch;
use App\PropertyNameResolver\PriceNameResolver;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

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
    private ?string $categoryPropertyPrefix;
    private ?string $typePropertyPrefix;
    private ?string $cityPropertyPrefix;

    public function __construct(
        ManagerRegistry                    $registry,
        ParameterBagInterface $parameterBag,
        private readonly PriceNameResolver $priceNameResolver,
    )
    {
        parent::__construct($registry, Advert::class);

        $this->categoryPropertyPrefix = $parameterBag->get('app_category_property_prefix');
        $this->typePropertyPrefix = $parameterBag->get('app_type_property_prefix');
        $this->cityPropertyPrefix = $parameterBag->get('app_city_property_prefix');
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

    public function getAdmins(AdvertSearch $search): QueryBuilder
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.category', 'category')
            ->leftJoin('a.pictures', 'pictures')
            ->addSelect('category')
            ->addSelect('pictures')
            ->orderBy('a.position', 'asc');

        if ($search->getCategory()) {
            $qb->andWhere('category.id = :categoryId')->setParameter('categoryId', (int) $search->getCategory()->getId());
        }

        if ($search->getCity()) {
            $qb->andWhere('a.city = :city')->setParameter('city', $search->getCity());
        }

        if ($search->getType()) {
            $qb->andWhere('a.type = :type')->setParameter('type', $search->getType());
        }

        if ($search->isEnabled() === false) {
            $qb->andWhere('c.enabled = false');
        }

        if ($search->isEnabled() === true) {
            $qb->andWhere('c.enabled = true');
        }

        return $qb;
    }

    public function getEnabledNumber(): int
    {
        $qb = $this->createQueryBuilder('a')
            ->select('count(a.id)')
            ->where('a.enabled = true');

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function getDisabledNumber(): int
    {
        $qb = $this->createQueryBuilder('a')
            ->select('count(a.id)')
            ->where('a.enabled = false');

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function getCities(): array
    {
        $results = $this->createQueryBuilder('a')
            ->select('a.city')
            ->distinct()
            ->getQuery()
            ->getArrayResult();

        $data = [];

        foreach ($results as $result) {
            $data[$result['city']] = $result['city'];
        }

        return $data;
    }

    public function getAdvertLists(array $data): QueryBuilder
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.category', 'category')
            ->leftJoin('a.pictures', 'pictures')
            ->addSelect('category')
            ->addSelect('pictures')
            ->where('a.enabled = true');

        $qb = $this->addFilterData($qb, $data);

        $qb = $qb->orderBy('a.position', 'ASC');

        return $qb;
    }

    public function getEnabledBySlug(string $slug): ?Advert
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.category', 'category')
            ->leftJoin('a.pictures', 'pictures')
            ->addSelect('category')
            ->addSelect('pictures');

        $qb->andWhere('a.slug = :slug')
            ->setParameter('slug', $slug);

        return $qb->getQuery()->getOneOrNullResult();
    }


    private function addFilterData(QueryBuilder $qb, array $data): QueryBuilder
    {
        $qb = $this->hasCategoriesQueryBuilder($qb, $data);
        $qb = $this->hasTypeQueryBuilder($qb, $data);
        $qb = $this->hasCityQueryBuilder($qb, $data);

        return $this->hasPriceBetweenQueryBuilder($qb, $data);
    }

    private function hasCategoriesQueryBuilder(QueryBuilder $qb, array $data): QueryBuilder
    {
        if ($data[$this->categoryPropertyPrefix]) {
            $qb->andWhere('category.slug = :category')->setParameter('category', $data[$this->categoryPropertyPrefix]);
        }

        return $qb;
    }

    private function hasTypeQueryBuilder(QueryBuilder $qb, array $data): QueryBuilder
    {
        if ($data[$this->typePropertyPrefix]) {
            $qb->andWhere('a.type = :type')->setParameter('type', $data[$this->typePropertyPrefix]);
        }

        return $qb;
    }

    private function hasCityQueryBuilder(QueryBuilder $qb, array $data): QueryBuilder
    {
        if ($data[$this->cityPropertyPrefix]) {
            $qb->andWhere('a.city = :city')->setParameter('city', $data[$this->cityPropertyPrefix]);
        }

        return $qb;
    }

    private function hasPriceBetweenQueryBuilder(QueryBuilder $qb, array $data): QueryBuilder
    {
        $minPrice = $this->getDataByKey($data, $this->priceNameResolver->resolveMinPriceName());
        $maxPrice = $this->getDataByKey($data, $this->priceNameResolver->resolveMaxPriceName());

        if ($minPrice) {
            $qb->andWhere($qb->expr()->gte('a.price', ':minPrice'))->setParameter('minPrice', (int)$minPrice);
        }

        if ($maxPrice) {
            $qb->andWhere($qb->expr()->lte('a.price', ':maxPrice'))->setParameter('maxPrice', (int)$maxPrice);
        }

        return $qb;
    }

    private function getDataByKey(array $data, string $key): ?string
    {
        return $data[$key] ?? null;
    }

    public function findRecent(int $limit): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.enabled = true')
            ->orderBy('a.position', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
