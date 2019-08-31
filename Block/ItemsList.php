<?php
declare(strict_types=1);

namespace Piuga\News\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Theme\Block\Html\Pager;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Piuga\News\Api\Data\NewsInterface;
use Piuga\News\Helper\NewsItem;
use Piuga\News\Model\ResourceModel\News\Collection;
use Piuga\News\Model\ResourceModel\News\CollectionFactory;

/**
 * Class ItemsList
 * @package Piuga\News\Block
 */
class ItemsList extends Template
{
    /**
     * @var CollectionFactory
     */
    protected $newsCollectionFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var NewsItem
     */
    protected $newsHelper;

    /**
     * ItemsList constructor.
     * @param Context $context
     * @param CollectionFactory $newsCollectionFactory
     * @param StoreManagerInterface $storeManager
     * @param NewsItem $newsHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        CollectionFactory $newsCollectionFactory,
        StoreManagerInterface $storeManager,
        NewsItem $newsHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->newsCollectionFactory = $newsCollectionFactory;
        $this->storeManager = $storeManager;
        $this->newsHelper = $newsHelper;
    }

    /**
     * Get news items collection
     *
     * @return Collection
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getNewsItems() {
        static $news = null;
        if ($news === null) {
            // Get current store ID
            $currentStoreId = $this->storeManager->getStore()->getId();
            // Prepare stores array for filter
            $stores = [Store::DEFAULT_STORE_ID, $currentStoreId];
            // Prepare collection
            $news = $this->newsCollectionFactory->create()
                ->addFieldToFilter('status', ['eq' => 1])
                ->addFieldToFilter('publish_at', ['lteq' => date('Y-m-d H:i:s')])
                ->addFieldToFilter('stores', ['in' => $stores])
                ->setOrder($this->newsHelper->getSortBy(), $this->newsHelper->getSortByDirection());
        }

        return $news;
    }

    /**
     * Add pager block and collection
     *
     * @return ItemsList|Template
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        if ($this->getNewsItems()) {
            /** @var Pager $pager */
            $pager = $this->getLayout()->createBlock(
                    Pager::class,
                    'piuga.news.list.pager'
                )
                ->setShowAmounts(true)
                ->setAvailableLimit($this->newsHelper->getAvailableLimit())
                ->setCollection($this->getNewsItems());
            $this->setChild('pager', $pager);
            $this->getNewsItems()->load();
        }

        return $this;
    }

    /**
     * Get pager block from layout
     *
     * @return string
     */
    public function getPagerHtml() : string
    {
        return $this->getChildHtml('pager');
    }

    /**
     * Get news item detail page link
     *
     * @param NewsInterface $news
     * @return string
     */
    public function getItemUrl(NewsInterface $news) : string
    {
        return $this->newsHelper->getItemUrl($news);
    }

    /**
     * Return publish date in locale format
     *
     * @param NewsInterface $news
     * @return string
     */
    public function getPublishDate(NewsInterface $news) : string
    {
        return $this->newsHelper->getPublishDate($news);
    }

    /**
     * Get list description from configurations
     *
     * @return string
     * @throws \Exception
     */
    public function getDescription() : string
    {
        return $this->newsHelper->getListDescription();
    }
}
