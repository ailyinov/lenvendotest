<?php


namespace Lenvendo\Service\Bookmark\Command;


use Doctrine\ORM\EntityManagerInterface;
use Lenvendo\Document\BookmarkElastic;
use Lenvendo\Entity\Bookmark;
use Lenvendo\Service\Bookmark\HttpClient;
use Lenvendo\Service\Bookmark\ResponseParser;
use Lenvendo\UserInteraction\Dto\AddBookmarkDto;
use ONGR\ElasticsearchBundle\Service\IndexService;
use Psr\Container\ContainerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class BookmarkAddCommand
{
    /**
     * @var HttpClient
     */
    private $httpClient;

    /**
     * @var ResponseParser
     */
    private $responseParser;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * BookmarkAdd constructor.
     *
     * @param HttpClient $httpClient
     * @param ResponseParser $responseParser
     * @param EntityManagerInterface $entityManager
     * @param ContainerInterface $container
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(HttpClient $httpClient, ResponseParser $responseParser, EntityManagerInterface $entityManager, ContainerInterface $container, UserPasswordEncoderInterface $encoder)
    {
        $this->httpClient = $httpClient;
        $this->responseParser = $responseParser;
        $this->entityManager = $entityManager;
        $this->encoder = $encoder;
        $this->container = $container;
    }

    public function run(AddBookmarkDto $bookmarkData)
    {
        $bodyContent = $this->httpClient->request($bookmarkData->getUrl());
        $metadata = $this->responseParser->parseMeta($bodyContent);

        $bookmark = $this->persistToMysql($bookmarkData, $metadata);
        $this->persistToElastic($bookmark);
    }

    private function persistToElastic(Bookmark $bookmark): void
    {
        $bi = new BookmarkElastic();
        $bi->setKeywords($bookmark->getKeywords());
        $bi->setMySqlId($bookmark->getId());
        $bi->setDescription($bookmark->getDescription());
        $bi->setTitle($bookmark->getTitle());
        $bi->setUrl($bookmark->getUrl());

        /** @var IndexService $indexService */
        $indexService = $this->container->get(BookmarkElastic::class);

        $indexService->persist($bi);
        $indexService->flush();
    }

    /**
     * @param AddBookmarkDto $bookmarkData
     * @param array $metadata
     * @return Bookmark
     */
    private function persistToMysql(AddBookmarkDto $bookmarkData, array $metadata): Bookmark
    {
        $bookmark = new Bookmark();
        $bookmark->setUrl($bookmarkData->getUrl());
        $bookmark->setTitle($metadata['title'] ?? '');
        $bookmark->setDescription($metadata[ResponseParser::META_DESCRIPTION] ?? '');
        $bookmark->setKeywords($metadata[ResponseParser::META_KEYWORDS] ?? '');
        $bookmark->setFavicon($metadata['favicon'] ?? '');
        if ($bookmarkData->getPassword()) {
            $bookmark->setPassword($this->encoder->encodePassword($bookmark, $bookmarkData->getPassword()));
        }
        $bookmark->setDateCreated(new \DateTime());

        $this->entityManager->persist($bookmark);
        $this->entityManager->flush();

        return $bookmark;
    }
}