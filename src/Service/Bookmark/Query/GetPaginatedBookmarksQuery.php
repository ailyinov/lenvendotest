<?php


namespace Lenvendo\Service\Bookmark\Query;


use Doctrine\ORM\Tools\Pagination\Paginator;
use Elasticsearch\ClientBuilder;
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

    public function __construct(ContainerInterface $container, BookmarkRepository $bookmarkRepository)
    {
        $this->container = $container;
        $this->bookmarkRepository = $bookmarkRepository;
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
            $query
        );
        $search = $indexService->createSearch()->addQuery($multiMatchQuery);
        $client =ClientBuilder::create()->setHosts(['localhost:9209'])->build();
        $searchParams = [
            'index' => 'bookmarks',
            'body' => $search->toArray(),
        ];
        $result = $client->search($searchParams);
        $bookmarkIds = [];
        foreach ($result['hits']['hits'] as $item) {
            $bookmarkIds[] = $item['_source']['my_sql_id'];
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