<?php


namespace Lenvendo\Service\Bookmark\Command;


use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use Lenvendo\Entity\Bookmark;
use Lenvendo\UserInteraction\Dto\RemoveBookmarkDto;

class BookmarkRemoveCommand
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * BookmarkAdd constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param RemoveBookmarkDto $removeBookmarkDto
     * @throws ORMException
     */
    public function __invoke(RemoveBookmarkDto $removeBookmarkDto)
    {
        $bookmark = $this->entityManager->getReference(Bookmark::class, $removeBookmarkDto->getId());
        $this->entityManager->remove($bookmark);
        $this->entityManager->flush();
    }
}