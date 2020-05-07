<?php


namespace Lenvendo\Service\Bookmark\Command;


use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use Lenvendo\Document\BookmarkElastic;
use Lenvendo\Entity\Bookmark;
use Lenvendo\UserInteraction\Dto\RemoveBookmarkDto;
use ONGR\ElasticsearchBundle\Service\IndexService;
use Psr\Container\ContainerInterface;

class BookmarkRemoveCommand
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * BookmarkAdd constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param ContainerInterface $container
     */
    public function __construct(EntityManagerInterface $entityManager, ContainerInterface $container)
    {
        $this->entityManager = $entityManager;
        $this->container = $container;
    }

    /**
     * @param RemoveBookmarkDto $removeBookmarkDto
     * @throws ORMException
     */
    public function __invoke(RemoveBookmarkDto $removeBookmarkDto)
    {
        $bookmark = $this->entityManager->getReference(Bookmark::class, $removeBookmarkDto->getId());

        /** @var IndexService $indexService */
        $indexService = $this->container->get(BookmarkElastic::class);

        $this->entityManager->remove($bookmark);
        $indexService->remove($removeBookmarkDto->getId());

        $this->entityManager->flush();
        $indexService->flush();
    }
}