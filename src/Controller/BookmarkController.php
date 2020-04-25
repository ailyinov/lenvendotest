<?php


namespace Lenvendo\Controller;


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
        return $this->render('base.html.twig', [
            'sup' => 'sup',
        ]);
    }

    public function listAction(BookmarkRepository $bookmarkRepository)
    {
        $bookmarkRepository->findAll();
        return $this->render('list.html.twig', [
            'bookmarks' => $bookmarkRepository->findAll(),
        ]);
    }
}