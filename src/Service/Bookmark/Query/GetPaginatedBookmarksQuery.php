<?php


namespace Lenvendo\Service\Bookmark\Query;


use Doctrine\ORM\Tools\Pagination\Paginator;
use Elasticsearch\Client as ElasticsearchClient;
use Lenvendo\Document\BookmarkElastic;
use Lenvendo\Repository\BookmarkRepository;
use ONGR\ElasticsearchBundle\Service\IndexService;
use ONGR\ElasticsearchDSL\Query\FullText\MultiMatchQuery;
use Psr\Container\ContainerInterface;

class GetPaginatedBookmarksQuery
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var BookmarkRepository
     */
    private $bookmarkRepository;

    /**
     * @var ElasticsearchClient
     */
    private $elasticsearchClient;

    /**
     * GetPaginatedBookmarksQuery constructor.
     *
     * @param ContainerInterface $container
     * @param BookmarkRepository $bookmarkRepository
     * @param ElasticsearchClient $elasticsearchClient
     */
    public function __construct(ContainerInterface $container, BookmarkRepository $bookmarkRepository, ElasticsearchClient $elasticsearchClient)
    {
        $this->container = $container;
        $this->bookmarkRepository = $bookmarkRepository;
        $this->elasticsearchClient = $elasticsearchClient;
    }

    public function run(int $count, int $offset, string $orderBy, string $sortOrder = 'asc', string $search = null): Paginator
    {
        $bookmarkIds = null;
        if (null !== $search) {
            $bookmarkIds =$this->findBySearch($search);
        }

        return $this->getPaginator($count, $offset, $orderBy, $sortOrder, $bookmarkIds);
    }

    private function findBySearch(string $query): ?array
    {
        /** @var IndexService $indexService */
        $indexService = $this->container->get(BookmarkElastic::class);

        $multiMatchQuery = new MultiMatchQuery(
            ['title', 'url', 'keywords', 'description'],
            $query,
            [
                'type' => 'phrase_prefix',
            ]
        );
        $search = $indexService->createSearch()->addQuery($multiMatchQuery);
        $result =$indexService->findDocuments($search);
        $bookmarkIds = [];
        /** @var BookmarkElastic $item */
        foreach ($result as $item) {
            $bookmarkIds[] = $item->getMySqlId();
        }

        return $bookmarkIds;
    }

    /**
     * @param int $count
     * @param int $offset
     * @param string $orderBy
     * @param string $sortOrder
     * @param array $bookmarkIds
     * @return Paginator
     */
    private function getPaginator(int $count, int $offset, string $orderBy, string $sortOrder, ?array $bookmarkIds): Paginator
    {
        return $this->bookmarkRepository->findSorted($count, $offset, $orderBy, $sortOrder, $bookmarkIds);
    }
}