<?php
namespace Piuga\News\Api\Data;

/**
 * Interface NewsInterface
 * @package Piuga\News\Api\Data
 */
interface NewsInterface
{
    /**
     * Constants for keys of data array.
     * Identical to the name of the getter/setter in snake case
     */
    const AUTHOR = 'author';
    const CONTENT = 'content';
    const CREATED_AT = 'created_at';
    const IMAGE = 'image';
    const META_DESCRIPTION = 'meta_description';
    const META_KEYWORDS = 'meta_keywords';
    const NEWS_ID = 'id';
    const PUBLISH_AT = 'publish_at';
    const SHORT_CONTENT = 'short_content';
    const TITLE = 'title';
    const UPDATED_AT = 'updated_at';
    const URL_KEY = 'url_key';
    const STATUS = 'status';
    const STORES = 'stores';

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set ID
     *
     * @param int $id
     * @return NewsInterface
     */
    public function setId($id);

    /**
     * Get Url Key
     *
     * @return string
     */
    public function getUrlKey() : string;

    /**
     * Set Url Key
     *
     * @param string $urlKey
     * @return NewsInterface
     */
    public function setUrlKey(string $urlKey) : NewsInterface;

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle() : string;

    /**
     * Set title
     *
     * @param string $title
     * @return NewsInterface
     */
    public function setTitle(string $title) : NewsInterface;

    /**
     * Get content
     *
     * @return string|null
     */
    public function getContent() : ?string;

    /**
     * Set content
     *
     * @param string $content
     * @return NewsInterface
     */
    public function setContent(string $content) : NewsInterface;

    /**
     * Get short content
     *
     * @return string|null
     */
    public function getShortContent() : ?string;

    /**
     * Set short content
     *
     * @param string $shortContent
     * @return NewsInterface
     */
    public function setShortContent(string $shortContent) : NewsInterface;

    /**
     * Get publish date
     *
     * @return string
     */
    public function getPublishAt() : string;

    /**
     * Set publish date
     *
     * @param string $publishAt
     * @return NewsInterface
     */
    public function setPublishAt(string $publishAt) : NewsInterface;

    /**
     * Get creation date
     *
     * @return string
     */
    public function getCreatedAt() : string;

    /**
     * Set creation date
     *
     * @param string $createdAt
     * @return NewsInterface
     */
    public function setCreatedAt(string $createdAt) : NewsInterface;

    /**
     * Get update date
     *
     * @return string
     */
    public function getUpdatedAt() : string;

    /**
     * Set update date
     *
     * @param string $updateAt
     * @return NewsInterface
     */
    public function setUpdatedAt(string $updateAt) : NewsInterface;

    /**
     * Get stores
     *
     * @return string
     */
    public function getStores() : string;

    /**
     * Set stores
     *
     * @param string $stores
     * @return NewsInterface
     */
    public function setStores(string $stores) : NewsInterface;

    /**
     * Get status
     *
     * @return int
     */
    public function getStatus() : int;

    /**
     * Set status
     *
     * @param int $status
     * @return NewsInterface
     */
    public function setStatus(int $status) : NewsInterface;

    /**
     * Get author
     *
     * @return string
     */
    public function getAuthor() : string;

    /**
     * Set author
     *
     * @param string $author
     * @return NewsInterface
     */
    public function setAuthor(string $author) : NewsInterface;

    /**
     * Get meta description
     *
     * @return string|null
     */
    public function getMetaDescription() : ?string;

    /**
     * Set meta description
     *
     * @param string $metaDescription
     * @return NewsInterface
     */
    public function setMetaDescription(string $metaDescription) : NewsInterface;

    /**
     * Get meta keywords
     *
     * @return string|null
     */
    public function getMetaKeywords() : ?string;

    /**
     * Set meta keywords
     *
     * @param string $metaKeywords
     * @return NewsInterface
     */
    public function setMetaKeywords(string $metaKeywords) : NewsInterface;

    /**
     * Get image
     *
     * @return string|null
     */
    public function getImage() : ?string;

    /**
     * Set image
     *
     * @param string $image
     * @return NewsInterface
     */
    public function setImage(string $image) : NewsInterface;
}
