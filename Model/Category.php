<?php
declare(strict_types=1);

namespace Piuga\News\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Piuga\News\Api\Data\CategoryInterface;
use Piuga\News\Model\ResourceModel\Category as CategoryResource;

/**
 * Class Category
 * @package Piuga\News\Model
 */
class Category extends AbstractModel implements CategoryInterface, IdentityInterface 
{
    /**
     * Category status flags
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    /**
     * News category cache tag
     */
    const CACHE_TAG = 'piuga_news_category';
    protected $_cacheTag = self::CACHE_TAG;

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'piuga_news_category';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'news_category';

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init(CategoryResource::class);
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
    public function setUrlKey(string $urlKey) : CategoryInterface
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
    public function setTitle(string $title) : CategoryInterface
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
    public function setContent(string $content) : CategoryInterface
    {
        return $this->setData(self::CONTENT, $content);
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
    public function setCreatedAt(?string $createdAt) : CategoryInterface
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
    public function setUpdatedAt(?string $updateAt) : CategoryInterface
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
    public function setStores(string $stores) : CategoryInterface
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
    public function setStatus(int $status) : CategoryInterface
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * {@inheritdoc}
     */
    public function getPosition() : int
    {
        return (int)$this->getData(self::POSITION);
    }

    /**
     * {@inheritdoc}
     */
    public function setPosition(int $position) : CategoryInterface
    {
        return $this->setData(self::POSITION, $position);
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
    public function setMetaDescription(string $metaDescription) : CategoryInterface
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
    public function setMetaKeywords(string $metaKeywords) : CategoryInterface
    {
        return $this->setData(self::META_KEYWORDS, $metaKeywords);
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave()
    {
        if ($this->hasDataChanges()) {
            $this->setUpdatedAt(null);
        }

        return parent::beforeSave();
    }
}
