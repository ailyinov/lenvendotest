<?php


namespace Lenvendo\Service\Bookmark;


use Doctrine\ORM\EntityManagerInterface;
use Lenvendo\Entity\Bookmark;
use Lenvendo\UserInteraction\RequestData\BookmarkData;

class BookmarkAdd
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
     * BookmarkAdd constructor.
     *
     * @param HttpClient $httpClient
     * @param ResponseParser $responseParser
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(HttpClient $httpClient, ResponseParser $responseParser, EntityManagerInterface $entityManager)
    {
        $this->httpClient = $httpClient;
        $this->responseParser = $responseParser;
        $this->entityManager = $entityManager;
    }

    public function run(BookmarkData $bookmarkData)
    {
        $bodyContent = $this->httpClient->request($bookmarkData->getUrl());
        $metadata = $this->responseParser->parseMeta($bodyContent);

        $bookmark = new Bookmark();
        $bookmark->setUrl($bookmarkData->getUrl());
        $bookmark->setTitle($metadata['title'] ?? '');
        $bookmark->setDescription($metadata[ResponseParser::META_DESCRIPTION] ?? '');
        $bookmark->setKeywords($metadata[ResponseParser::META_KEYWORDS] ?? '');
        $bookmark->setFavicon($metadata['favicon'] ?? '');

        $this->entityManager->persist($bookmark);
        $this->entityManager->flush();
    }
}