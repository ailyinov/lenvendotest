<?php

namespace Lenvendo\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Lenvendo\Entity\Bookmark;

class BookmarkRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bookmark::class);
    }

    public function findSorted(int $count, int $offset, string $orderBy, string $sortOrder = 'asc', array $bookmarkIds = null): Paginator
    {
        $query = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('b')
            ->from($this->getClassName(), 'b')
            ->orderBy("b.$orderBy", $sortOrder)
            ->setFirstResult($offset)
            ->setMaxResults($count)
        ;
        if (null !== $bookmarkIds) {
            $query->andWhere("b.id IN (:bookmarkIds)")->setParameter('bookmarkIds', $bookmarkIds);
        }

        return new Paginator($query);
    }

    public function findOneByUrl(string $url): ?Bookmark
    {
        return $this->findOneBy(['url' => $url]);
    }
}