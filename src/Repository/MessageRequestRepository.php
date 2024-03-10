<?php

namespace App\Repository;

use App\Entity\MessageRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MessageRequest>
 *
 * @method MessageRequest|null find($id, $lockMode = null, $lockVersion = null)
 * @method MessageRequest|null findOneBy(array $criteria, array $orderBy = null)
 * @method MessageRequest[]    findAll()
 * @method MessageRequest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MessageRequest::class);
    }

    public function add(MessageRequest $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(MessageRequest $entity, bool $flush = false): void
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

    public function findLastRequestForIp(string $ip): ?MessageRequest
    {
        return $this->createQueryBuilder('mr')
            ->where('mr.ip = :ip')
            ->setParameter('ip', $ip)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
