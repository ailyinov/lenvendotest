<?php


namespace Lenvendo\UserInteraction\Dto;


use Symfony\Component\HttpFoundation\Request;

class RemoveBookmarkDto implements Dto
{
    /**
     * @var string
     */
    private $password;

    /**
     * @var int
     */
    private $id;

    /**
     * BookmarkData constructor.
     *
     * @param int $id
     * @param string $password
     */
    public function __construct(int $id, string $password = null)
    {
        $this->password = $password;
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public static function createFromRequest(Request $request)
    {
        return new self($request->get('bookmark_id'), $request->get('password'));
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
}