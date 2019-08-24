<?php
declare(strict_types=1);

namespace Piuga\News\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Piuga\News\Api\Data\NewsInterface;
use Piuga\News\Model\ResourceModel\News as NewsResource;

/**
 * Class News
 * @package Piuga\News\Model
 */
class News extends AbstractModel implements NewsInterface, IdentityInterface 
{
    /**
     * News status flags
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    /**
     * News cache tag
     */
    const CACHE_TAG = 'piuga_news';
    protected $_cacheTag = self::CACHE_TAG;

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'piuga_news_item';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'news_item';

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init(NewsResource::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * {@inheritdoc}
     */
    public function getUrlKey() : string
    {
        return (string)$this->getData(self::URL_KEY);
    }

    /**
     * {@inheritdoc}
     */
    public function setUrlKey(string $urlKey) : NewsInterface
    {
        return $this->setData(self::URL_KEY, $urlKey);
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle() : string
    {
        return (string)$this->getData(self::TITLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle(string $title) : NewsInterface
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * {@inheritdoc}
     */
    public function getContent() : ?string
    {
        return $this->getData(self::CONTENT);
    }

    /**
     * {@inheritdoc}
     */
    public function setContent(string $content) : NewsInterface
    {
        return $this->setData(self::CONTENT, $content);
    }

    /**
     * {@inheritdoc}
     */
    public function getShortContent() : ?string
    {
        return $this->getData(self::SHORT_CONTENT);
    }

    /**
     * {@inheritdoc}
     */
    public function setShortContent(string $shortContent) : NewsInterface
    {
        return $this->setData(self::SHORT_CONTENT, $shortContent);
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishAt() : string
    {
        return (string)$this->getData(self::PUBLISH_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setPublishAt(string $publishAt) : NewsInterface
    {
        return $this->setData(self::PUBLISH_AT, $publishAt);
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt() : string
    {
        return (string)$this->getData(self::CREATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt(string $createdAt) : NewsInterface
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt() : string
    {
        return (string)$this->getData(self::UPDATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt(string $updateAt) : NewsInterface
    {
        return $this->setData(self::UPDATED_AT, $updateAt);
    }

    /**
     * {@inheritdoc}
     */
    public function getStores() : string
    {
        return (string)$this->getData(self::STORES);
    }

    /**
     * {@inheritdoc}
     */
    public function setStores(string $stores) : NewsInterface
    {
        return $this->setData(self::STORES, $stores);
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus() : int
    {
        return (int)$this->getData(self::STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus(int $status) : NewsInterface
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthor() : string
    {
        return (string)$this->getData(self::AUTHOR);
    }

    /**
     * {@inheritdoc}
     */
    public function setAuthor(string $author) : NewsInterface
    {
        return $this->setData(self::AUTHOR, $author);
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaDescription() : ?string
    {
        return $this->getData(self::META_DESCRIPTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setMetaDescription(string $metaDescription) : NewsInterface
    {
        return $this->setData(self::META_DESCRIPTION, $metaDescription);
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaKeywords() : ?string
    {
        return $this->getData(self::META_KEYWORDS);
    }

    /**
     * {@inheritdoc}
     */
    public function setMetaKeywords(string $metaKeywords) : NewsInterface
    {
        return $this->setData(self::META_KEYWORDS, $metaKeywords);
    }

    /**
     * {@inheritdoc}
     */
    public function getImage() : ?string
    {
        return $this->getData(self::IMAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function setImage(string $image) : NewsInterface
    {
        return $this->setData(self::IMAGE, $image);
    }
}
