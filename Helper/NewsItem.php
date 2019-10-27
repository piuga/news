<?php
declare(strict_types=1);

namespace Piuga\News\Helper;

use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Stdlib\DateTime;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Piuga\News\Api\CategoryRepositoryInterface;
use Piuga\News\Api\NewsRepositoryInterface;
use Piuga\News\Api\Data\CategoryInterface;
use Piuga\News\Api\Data\NewsInterface;

/**
 * Class NewsItem
 * @package Piuga\News\Helper
 */
class NewsItem extends AbstractHelper
{
    /**
     * Store configuration paths
     */
    // General paths
    const CONFIG_PATH_BASE = 'piuga_news/';
    const CONFIG_PATH_BASE_GENERAL = self::CONFIG_PATH_BASE . 'general/';
    const CONFIG_PATH_ACTIVE = self::CONFIG_PATH_BASE_GENERAL . 'active';
    const CONFIG_PATH_TITLE = self::CONFIG_PATH_BASE_GENERAL . 'title';
    const CONFIG_PATH_URL_KEY = self::CONFIG_PATH_BASE_GENERAL . 'url_key';
    const CONFIG_PATH_DESCRIPTION = self::CONFIG_PATH_BASE_GENERAL . 'description';
    const CONFIG_PATH_ALLOWED_ITEMS = self::CONFIG_PATH_BASE_GENERAL . 'allowed_items';
    const CONFIG_PATH_SORT_BY = self::CONFIG_PATH_BASE_GENERAL . 'sort_by';
    const CONFIG_PATH_SORT_BY_DIRECTION = self::CONFIG_PATH_BASE_GENERAL . 'sort_by_direction';
    // SEO configuration paths
    const CONFIG_PATH_BASE_SEO = self::CONFIG_PATH_BASE . 'seo/';
    const CONFIG_PATH_META_TITLE = self::CONFIG_PATH_BASE_SEO . 'title';
    const CONFIG_PATH_META_DESCRIPTION = self::CONFIG_PATH_BASE_SEO . 'description';
    const CONFIG_PATH_META_KEYWORDS = self::CONFIG_PATH_BASE_SEO . 'keywords';

    /**
     * @var NewsRepositoryInterface
     */
    protected $newsRepository;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var FilterProvider
     */
    protected $filterProvider;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * NewsItem constructor.
     * @param Context $context
     * @param NewsRepositoryInterface $newsRepository
     * @param StoreManagerInterface $storeManager
     * @param DateTime $dateTime
     * @param ScopeConfigInterface $scopeConfig
     * @param FilterProvider $filterProvider
     * @param CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(
        Context $context,
        NewsRepositoryInterface $newsRepository,
        StoreManagerInterface $storeManager,
        DateTime $dateTime,
        ScopeConfigInterface $scopeConfig,
        FilterProvider $filterProvider,
        CategoryRepositoryInterface $categoryRepository
    ) {
        parent::__construct($context);
        $this->newsRepository = $newsRepository;
        $this->storeManager = $storeManager;
        $this->dateTime = $dateTime;
        $this->scopeConfig = $scopeConfig;
        $this->filterProvider = $filterProvider;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Get news item based on request ID
     *
     * @return NewsInterface|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getNewsItem() : ?NewsInterface
    {
        try {
            $id = (int)$this->_getRequest()->getParam('id');
            $newsItem = $this->newsRepository->getById($id);
        } catch (\Exception $e) {
            return null;
        }

        // Check if news is enabled or published
        if (!$newsItem || !$newsItem->getStatus() || (strtotime($newsItem->getPublishAt()) > time())) {
            return null;
        }

        // Check stores
        $currentStoreId = $this->storeManager->getStore()->getId();
        $stores = [Store::DEFAULT_STORE_ID, $currentStoreId];
        $newsItemStores = explode(',', $newsItem->getStores());
        if (!count(array_intersect($stores, $newsItemStores))) {
            return null;
        }

        return $newsItem;
    }

    /**
     * Get news category based on request ID or URL key
     *
     * @return CategoryInterface|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getNewsCategory() : ?CategoryInterface
    {
        try {
            $id = (int)$this->_getRequest()->getParam('cat_id');
            $newsCategory = $this->categoryRepository->getById($id);
        } catch (\Exception $e) {
            return null;
        }

        // Check if news category is enabled
        if (!$newsCategory || !$newsCategory->getStatus()) {
            return null;
        }

        // Check stores
        $currentStoreId = $this->storeManager->getStore()->getId();
        $stores = [Store::DEFAULT_STORE_ID, $currentStoreId];
        $newsCategoryStores = explode(',', $newsCategory->getStores());
        if (!count(array_intersect($stores, $newsCategoryStores))) {
            return null;
        }

        return $newsCategory;
    }

    /**
     * Get news item detail page link
     *
     * @param NewsInterface $news
     * @param CategoryInterface|null $category
     * @return string
     */
    public function getItemUrl(NewsInterface $news, CategoryInterface $category = null) : string
    {
        if ($category) {
            return $this->_getUrl($this->getNewsUrl() . '/' . $category->getUrlKey() . '/' . $news->getUrlKey());
        }

        return $this->_getUrl($this->getNewsUrl() . '/' . $news->getUrlKey());
    }

    /**
     * Get news category page link
     *
     * @param CategoryInterface $category
     * @return string
     */
    public function getCategoryUrl(CategoryInterface $category) : string
    {
        return $this->_getUrl($this->getNewsUrl() . '/' . $category->getUrlKey());
    }

    /**
     * Return publish date in locale format
     *
     * @param NewsInterface $news
     * @return string
     */
    public function getPublishDate(NewsInterface $news) : string
    {
        if ($news->getPublishAt()) {
            return $this->dateTime->formatDate($news->getPublishAt(), false);
        }

        return '';
    }

    /**
     * Check if module is activated
     *
     * @return bool
     */
    public function isActive() : bool
    {
        return (bool)$this->scopeConfig->getValue(
            self::CONFIG_PATH_ACTIVE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getListTitle() : string
    {
        return (string)$this->scopeConfig->getValue(
            self::CONFIG_PATH_TITLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get list base URL
     *
     * @return string
     */
    public function getNewsUrl() : string
    {
        return (string)$this->scopeConfig->getValue(
            self::CONFIG_PATH_URL_KEY,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get list description
     *
     * @return string
     * @throws \Exception
     */
    public function getListDescription() : string
    {
        return $this->filterProvider
            ->getBlockFilter()
            ->filter(
                (string)$this->scopeConfig->getValue(
                    self::CONFIG_PATH_DESCRIPTION,
                    ScopeInterface::SCOPE_STORE
                )
            );
    }

    /**
     * Get available items per page
     *
     * @return array
     */
    public function getAvailableLimit() : array
    {
        // Default items per page options
        $availableLimit = [5 => 5, 10 => 10, 15 => 15];

        $allowedItems = explode(',', (string)$this->scopeConfig->getValue(
            self::CONFIG_PATH_ALLOWED_ITEMS,
            ScopeInterface::SCOPE_STORE
        ));

        if (count($allowedItems)) {
            $availableLimit = [];
            foreach ($allowedItems as $item) {
                $availableLimit[(int)$item] = (int)$item;
            }
        }

        return $availableLimit;
    }

    /**
     * Get list sort by attribute
     *
     * @return string
     */
    public function getSortBy() : string
    {
        return (string)$this->scopeConfig->getValue(
            self::CONFIG_PATH_SORT_BY,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get list sort by direction
     *
     * @return string
     */
    public function getSortByDirection() : string
    {
        return (string)$this->scopeConfig->getValue(
            self::CONFIG_PATH_SORT_BY_DIRECTION,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get meta title
     *
     * @return string
     */
    public function getMetaTitle() : string
    {
        return (string)$this->scopeConfig->getValue(
            self::CONFIG_PATH_META_TITLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get meta description
     *
     * @return string
     */
    public function getMetaDescription() : string
    {
        return (string)$this->scopeConfig->getValue(
            self::CONFIG_PATH_META_DESCRIPTION,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get meta keywords
     *
     * @return string
     */
    public function getMetaKeywords() : string
    {
        return (string)$this->scopeConfig->getValue(
            self::CONFIG_PATH_META_KEYWORDS,
            ScopeInterface::SCOPE_STORE
        );
    }
}
