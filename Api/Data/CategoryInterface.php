<?php
namespace Piuga\News\Api\Data;

/**
 * Interface CategoryInterface
 * @package Piuga\News\Api\Data
 */
interface CategoryInterface
{
    /**
     * Constants for keys of data array.
     * Identical to the name of the getter/setter in snake case
     */
    const CATEGORY_ID = 'id';
    const CONTENT = 'content';
    const CREATED_AT = 'created_at';
    const META_DESCRIPTION = 'meta_description';
    const META_KEYWORDS = 'meta_keywords';
    const NEWS_POSITION = 'news_position';
    const POSITION = 'position';
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
     * @return CategoryInterface
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
     * @return CategoryInterface
     */
    public function setUrlKey(string $urlKey) : CategoryInterface;

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
     * @return CategoryInterface
     */
    public function setTitle(string $title) : CategoryInterface;

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
     * @return CategoryInterface
     */
    public function setContent(string $content) : CategoryInterface;

    /**
     * Get creation date
     *
     * @return string
     */
    public function getCreatedAt() : string;

    /**
     * Set creation date
     *
     * @param string|null $createdAt
     * @return CategoryInterface
     */
    public function setCreatedAt(?string $createdAt) : CategoryInterface;

    /**
     * Get update date
     *
     * @return string
     */
    public function getUpdatedAt() : string;

    /**
     * Set update date
     *
     * @param string|null $updateAt
     * @return CategoryInterface
     */
    public function setUpdatedAt(?string $updateAt) : CategoryInterface;

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
     * @return CategoryInterface
     */
    public function setStores(string $stores) : CategoryInterface;

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
     * @return CategoryInterface
     */
    public function setStatus(int $status) : CategoryInterface;

    /**
     * Get position
     *
     * @return int
     */
    public function getPosition() : int;

    /**
     * Set position
     *
     * @param int $position
     * @return CategoryInterface
     */
    public function setPosition(int $position) : CategoryInterface;

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
     * @return CategoryInterface
     */
    public function setMetaDescription(string $metaDescription) : CategoryInterface;

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
     * @return CategoryInterface
     */
    public function setMetaKeywords(string $metaKeywords) : CategoryInterface;

    /**
     * Retrieve array of news IDs for category
     * The array returned has the following format:
     * array($newsId => $position)
     *
     * @return array
     */
    public function getNewsPosition() : array;
}
