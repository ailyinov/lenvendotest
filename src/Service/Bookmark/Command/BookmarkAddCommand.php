<?php


namespace Lenvendo\Service\Bookmark\Command;


use Doctrine\ORM\EntityManagerInterface;
use Lenvendo\Entity\Bookmark;
use Lenvendo\UserInteraction\Dto\AddBookmarkDto;
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
     * BookmarkAdd constructor.
     *
     * @param HttpClient $httpClient
     * @param ResponseParser $responseParser
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(HttpClient $httpClient, ResponseParser $responseParser, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder)
    {
        $this->httpClient = $httpClient;
        $this->responseParser = $responseParser;
        $this->entityManager = $entityManager;
        $this->encoder = $encoder;
    }

    public function run(AddBookmarkDto $bookmarkData)
    {
        $bodyContent = $this->httpClient->request($bookmarkData->getUrl());
        $metadata = $this->responseParser->parseMeta($bodyContent);

        $bookmark = new Bookmark();
        $bookmark->setUrl($bookmarkData->getUrl());
        $bookmark->setTitle($metadata['title'] ?? '');
        $bookmark->setDescription($metadata[ResponseParser::META_DESCRIPTION] ?? '');
        $bookmark->setKeywords($metadata[ResponseParser::META_KEYWORDS] ?? '');
        $bookmark->setFavicon($metadata['favicon'] ?? '');
        if ($bookmarkData->getPassword()) {
            $bookmark->setPassword($this->encoder->encodePassword($bookmark, $bookmarkData->getPassword()));
        }

        $this->entityManager->persist($bookmark);
        $this->entityManager->flush();
    }
}