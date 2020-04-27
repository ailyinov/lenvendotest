<?php


namespace Lenvendo\UserInteraction\RequestData;


class BookmarkData
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
}