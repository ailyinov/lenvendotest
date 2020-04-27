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

    public function findSorted(int $count, int $offset, string $orderBy, string $sortOrder = 'asc'): Paginator
    {
        $dql = "SELECT b FROM {$this->getClassName()} b ORDER BY b.$orderBy $sortOrder";
        $query = $this->getEntityManager()->createQuery($dql)
            ->setFirstResult($offset)
            ->setMaxResults($count);

        return new Paginator($query);
    }

    public function findOneByUrl(string $url): ?Bookmark
    {
        return $this->findOneBy(['url' => $url]);
    }
}