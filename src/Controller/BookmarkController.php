<?php


namespace Lenvendo\Controller;


use Lenvendo\Controller\Utils\PaginatorWIthSorting;
use Lenvendo\Repository\BookmarkRepository;
use Lenvendo\Service\Bookmark\BookmarkAdd;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class BookmarkController extends AbstractController
{
    public function addAction(Request $request, BookmarkAdd $bookmarkAdd)
    {
        if ($request->isMethod('post')) {
            $bookmarkAdd->run($request->get('url'));
        }
        return $this->render('add.html.twig', [
            'sup' => 'sup',
        ]);
    }

    public function listAction(Request $request, BookmarkRepository $bookmarkRepository)
    {
        $paginator = new PaginatorWIthSorting($request);

        $result = $bookmarkRepository->findSorted($paginator->getItemsPerPageCount(), $paginator->getOffset(), $paginator->getSortField(), $paginator->getOrder());
        $paginator->setItemsCount($result->count());
        $paginator->setItems($result);

        return $this->render('list.html.twig', [
            'paginator' => $paginator,
        ]);
    }

    public function itemAction(Request $request, BookmarkRepository $bookmarkRepository)
    {
        $bookmarkId = $request->get('bookmark_id');
        $bookmark = $bookmarkRepository->find($bookmarkId);

        return $this->render('item.html.twig', [
            'bookmark' => $bookmark,
        ]);
    }
}