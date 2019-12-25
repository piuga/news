<?php
declare(strict_types=1);

namespace Piuga\News\Block\Widget;

use Magento\Framework\Api\SortOrder;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Widget\Block\BlockInterface;
use Piuga\News\Api\CategoryRepositoryInterface;
use Piuga\News\Api\Data\CategoryInterface;
use Piuga\News\Model\News as NewsModel;
use Piuga\News\Model\ResourceModel\News\Collection;
use Piuga\News\Model\ResourceModel\News\CollectionFactory;
use Piuga\News\Helper\NewsItem;

/**
 * Class News
 * @package Piuga\News\Block\Widget
 */
class News extends Template implements BlockInterface
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var NewsItem
     */
    protected $newsHelper;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var Category
     */
    protected $category = null;

    /**
     * News constructor.
     * @param Context $context
     * @param CollectionFactory $collectionFactory
     * @param CategoryRepositoryInterface $categoryRepository
     * @param StoreManagerInterface $storeManager
     * @param NewsItem $newsHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        CollectionFactory $collectionFactory,
        CategoryRepositoryInterface $categoryRepository,
        StoreManagerInterface $storeManager,
        NewsItem $newsHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->collectionFactory = $collectionFactory;
        $this->categoryRepository = $categoryRepository;
        $this->storeManager = $storeManager;
        $this->newsHelper = $newsHelper;
    }

    /**
     * {@inheritdoc}
     */
    protected function _beforeToHtml()
    {
        $this->setNewsCollection($this->getNewsCollection());

        return parent::_beforeToHtml();
    }

    /**
     * News collection initialize process
     *
     * @return Collection
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getNewsCollection() : Collection
    {
        // Get current store ID
        $currentStoreId = $this->storeManager->getStore()->getId();
        //Prepare stores array for filter
        $stores = [Store::DEFAULT_STORE_ID, $currentStoreId];

        // Prepare collection
        $collection = $this->collectionFactory->create()
            ->addFieldToFilter('status', ['eq' => NewsModel::STATUS_ENABLED])
            ->addFieldToFilter('publish_at', ['lteq' => date('Y-m-d H:i:s')])
            ->addFieldToFilter('stores', ['in' => $stores]);

        // Add category filter
        if ($this->getCategoryId()) {
            // Add position and categories to collection
            $collection->getSelect()->joinLeft(
                'piuga_news_categories_items',
                'news_id = main_table.id',
                ['category_id', 'position']
            );
            $collection->addFieldToFilter('category_id', ['eq' => (int)$this->getCategoryId()]);
        }
        $collection->setOrder('publish_at', SortOrder::SORT_DESC);

        // Add limit
        $collection->setPageSize($this->getLimit())->setCurPage(1);

        return $collection;
    }

    /**
     * Prepare title attribute using passed title as parameter
     *
     * @return string
     */
    public function getTitle() : string
    {
        return (string)$this->getData('title');
    }

    /**
     * Check if image should be displayed or not
     *
     * @return bool
     */
    public function canShowImage() : bool
    {
        return (bool)$this->getData('show_image');
    }

    /**
     * Get news list limit
     *
     * @return int
     */
    protected function getLimit() : int
    {
        return (int)$this->getData('limit');
    }

    /**
     * Get list category filter or false
     *
     * @return int
     */
    protected function getCategoryId() : int
    {
        return (int)$this->getData('category_id');
    }

    /**
     * Get current category
     *
     * @return CategoryInterface|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCurrentCategory() : ?CategoryInterface
    {
        if ($this->category) {
            return $this->category;
        } elseif ($categoryId = $this->getCategoryId()) {
            $category = $this->categoryRepository->getById($categoryId);
            if ($category->getId()) {
                $this->category = $category;
            }
        }

        return $this->category;
    }

    /**
     * Get news item URL
     *
     * @param NewsModel $news
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getItemUrl(NewsModel $news) : string
    {
        return $this->newsHelper->getItemUrl($news, $this->getCurrentCategory());
    }

    /**
     * Return publish date in locale format
     *
     * @param NewsModel $news
     * @return string
     */
    public function getPublishDate(NewsModel $news) : string
    {
        return $this->newsHelper->getPublishDate($news);
    }
}
