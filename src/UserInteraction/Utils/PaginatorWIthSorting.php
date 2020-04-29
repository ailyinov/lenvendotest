<?php


namespace Lenvendo\UserInteraction\Utils;


use Symfony\Component\HttpFoundation\Request;

class PaginatorWIthSorting
{
    private const ITEMS_PER_PAGE = 3;

    /**
     * @var int
     */
    private $page;

    /**
     * @var int
     */
    private $itemsCount;

    /**
     * @var string
     */
    private $sortField = 'name';

    /**
     * @var string
     */
    private $order ='asc';

    /**
     * @var string|null
     */
    private $search;

    /**
     * @var \IteratorAggregate
     */
    private $items;

    /**
     * PaginatorWIthSorting constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $page = $request->get('page', 1);
        $sort = $request->get('sort', 'dateCreated');
        $order = $request->get('order', 'asc');
        $search = $request->get('search', null);

        $this->setPage(max(1, $page));
        $this->setSortField($sort);
        $this->setOrder($order);
        $this->setSearch($search);
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @param int $page
     */
    public function setPage(int $page): void
    {
        $this->page = $page;
    }

    /**
     * @return string
     */
    public function getSortField(): string
    {
        return $this->sortField;
    }

    /**
     * @param string $sortField
     */
    public function setSortField(string $sortField): void
    {
        if (in_array($sortField, ['url', 'title', 'dateCreated'])) {
            $this->sortField = $sortField;
        }
    }

    /**
     * @return string
     */
    public function getOrder(): string
    {
        return $this->order;
    }

    /**
     * @param string $order
     */
    public function setOrder(string $order): void
    {
        $this->order = $order;
    }

    public function toggleOrder(): string
    {
        return $this->getOrder() == 'asc' ? 'desc' : 'asc';
    }

    public function getOffset(): int
    {
        return ($this->getPage() - 1) * self::ITEMS_PER_PAGE;
    }

    public function getItemsPerPageCount(): int
    {
        return self::ITEMS_PER_PAGE;
    }

    public function getItemsCount(): int
    {
        return $this->itemsCount;
    }

    public function setItemsCount(int $itemsCount): void
    {
        $this->itemsCount = $itemsCount;
    }

    public function pagesCount()
    {
        $count = ceil($this->getItemsCount()/self::ITEMS_PER_PAGE);

        return $count < 1 ? 1 : $count;
    }

    public function getQuery(int $page = null, string $sort = null, string $order = null): string
    {
        return http_build_query($this->getQueryParams($page, $sort, $order));
    }

    public function getQueryParams(int $page = null, string $sort = null, string $order = null): array
    {
        return [
            'page' => $page ?? $this->getPage(),
            'sort' => $sort ?? $this->getSortField(),
            'order' => $order ?? $this->getOrder(),
        ];
    }

    /**
     * @return \IteratorAggregate
     */
    public function getItems(): \IteratorAggregate
    {
        return $this->items;
    }

    /**
     * @param \IteratorAggregate $items
     */
    public function setItems(\IteratorAggregate $items): void
    {
        $this->items = $items;
    }

    /**
     * @return string
     */
    public function getSearch(): ?string
    {
        return $this->search;
    }

    /**
     * @param string|null $search
     */
    public function setSearch(?string $search): void
    {
        $this->search = $search;
    }
}