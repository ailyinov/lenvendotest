<?php


namespace Lenvendo\Document;

use ONGR\ElasticsearchBundle\Annotation as ES;

/**
 * @ES\Index(alias="bookmarks", default=true)
 */
class BookmarkElastic
{
    /**
     * @ES\Id()
     */
    private $id;

    /**
     * @ES\Property(type="integer")
     */
    private $mysqlId;

    /**
     * @ES\Property(type="text")
     */
    private $title;

    /**
     * @ES\Property(type="text")
     */
    private $url;

    /**
     * @ES\Property(type="text")
     */
    private $description;

    /**
     * @ES\Property(type="text")
     */
    private $keywords;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getMysqlId()
    {
        return $this->mysqlId;
    }

    /**
     * @param mixed $mysqlId
     */
    public function setMysqlId($mysqlId): void
    {
        $this->mysqlId = $mysqlId;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title): void
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url): void
    {
        $this->url = $url;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * @param mixed $keywords
     */
    public function setKeywords($keywords): void
    {
        $this->keywords = $keywords;
    }
}