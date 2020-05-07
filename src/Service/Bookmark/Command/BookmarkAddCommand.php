<?php


namespace Lenvendo\Service\Bookmark\Command;


use Doctrine\ORM\EntityManagerInterface;
use Lenvendo\Entity\Bookmark;
use Lenvendo\Service\Bookmark\ResponseParser;
use Lenvendo\UserInteraction\Dto\AddBookmarkDto;
use Psr\Container\ContainerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class BookmarkAddCommand
{
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
     * @param ResponseParser $responseParser
     * @param EntityManagerInterface $entityManager
     * @param ContainerInterface $container
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(ResponseParser $responseParser, EntityManagerInterface $entityManager, ContainerInterface $container, UserPasswordEncoderInterface $encoder)
    {
        $this->responseParser = $responseParser;
        $this->entityManager = $entityManager;
        $this->encoder = $encoder;
        $this->container = $container;
    }

    public function run(AddBookmarkDto $addBookmarkDto): Bookmark
    {
        $urlDomain = parse_url($addBookmarkDto->getUrl(), PHP_URL_HOST);
        $metadata = $this->responseParser->parseMeta((string) $addBookmarkDto->getResponse()->getBody(), $urlDomain);

        return $this->persistToMysql($addBookmarkDto, $metadata);
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