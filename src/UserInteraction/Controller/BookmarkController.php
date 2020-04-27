<?php


namespace Lenvendo\UserInteraction\Controller;


use Lenvendo\UserInteraction\RequestData\BookmarkData;
use Lenvendo\UserInteraction\Utils\PaginatorWIthSorting;
use Lenvendo\Repository\BookmarkRepository;
use Lenvendo\Service\Bookmark\BookmarkAdd;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BookmarkController extends AbstractController
{
    public function addAction(Request $request, BookmarkAdd $bookmarkAdd, ValidatorInterface $validator, BookmarkData $bookmarkData = null)
    {
        if ($request->isMethod('post')) {
            $this->validateCsrfToken('add-bookmark', $request->request->get('token'));
            $errors =$validator->validate($bookmarkData);
            if (count($errors) == 0) {
                $bookmarkAdd->run($bookmarkData);
            }
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

    /**
     * @param string $id
     * @param string $submittedToken
     */
    private function validateCsrfToken(string $id, string $submittedToken): void
    {
        if (!$this->isCsrfTokenValid($id, $submittedToken)) {
            $this->redirect($this->generateUrl('bookmarks'));
        }
    }
}