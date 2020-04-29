<?php


namespace Lenvendo\UserInteraction\Controller;


use Lenvendo\Repository\BookmarkRepository;
use Lenvendo\Service\Bookmark\Command\BookmarkAddCommand;
use Lenvendo\Service\Bookmark\Command\BookmarkRemoveCommand;
use Lenvendo\Service\Bookmark\Query\GetPaginatedBookmarksQuery;
use Lenvendo\UserInteraction\Dto\AddBookmarkDto;
use Lenvendo\UserInteraction\Dto\RemoveBookmarkDto;
use Lenvendo\UserInteraction\Utils\PaginatorWIthSorting;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BookmarkController extends AbstractController
{
    public function addAction(Request $request, BookmarkAddCommand $bookmarkAdd, ValidatorInterface $validator, AddBookmarkDto $bookmarkData = null)
    {
        $errors = [];
        if ($request->isMethod('post')) {
            $this->validateCsrfToken('add-bookmark', $request->request->get('token'));
            $errors =$validator->validate($bookmarkData);
            if (count($errors) == 0) {
                $bookmark = $bookmarkAdd->run($bookmarkData);

                return $this->redirect($this->generateUrl('bookmark_item', ['bookmark_id' => $bookmark->getId()]));
            }
        }

        return $this->render('add.html.twig', [
            'errors' => $errors,
        ]);
    }

    public function removeAction(Request $request, ValidatorInterface $validator, BookmarkRemoveCommand $remove, RemoveBookmarkDto $removeBookmarkDto = null)
    {
        if ($request->isMethod('post')) {
            $this->validateCsrfToken('remove-bookmark', $request->request->get('token'));
            $errors =$validator->validate($removeBookmarkDto);
            if (count($errors) == 0) {
                $remove($removeBookmarkDto);
            }
        }

        return $this->render('add.html.twig', [
            'sup' => 'sup',
        ]);
    }

    public function listAction(Request $request, GetPaginatedBookmarksQuery $getPaginatedBookmarksQuery)
    {
        $paginator = new PaginatorWIthSorting($request);

        $result = $getPaginatedBookmarksQuery->run($paginator->getItemsPerPageCount(), $paginator->getOffset(), $paginator->getSortField(), $paginator->getOrder(), $paginator->getSearch());
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
     * @return RedirectResponse
     */
    private function validateCsrfToken(string $id, string $submittedToken): ?RedirectResponse
    {
        if (!$this->isCsrfTokenValid($id, $submittedToken)) {
            return $this->redirect($this->generateUrl('bookmarks'));
        }

        return null;
    }
}