<?php


namespace Lenvendo\UserInteraction\Dto;


use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Request;

class AddBookmarkDto implements Dto
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $password;

    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * BookmarkData constructor.
     *
     * @param string $url
     * @param string $password
     */
    public function __construct(string $url, string $password = null)
    {
        $this->url = $url;
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    /**
     * @param ResponseInterface $response
     */
    public function setResponse(ResponseInterface $response): void
    {
        $this->response = $response;
    }

    public static function createFromRequest(Request $request)
    {
        return new self($request->get('url'), $request->get('password', null));
    }
}