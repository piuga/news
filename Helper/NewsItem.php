<?php
declare(strict_types=1);

namespace Piuga\News\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Stdlib\DateTime;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Piuga\News\Api\NewsRepositoryInterface;
use Piuga\News\Api\Data\NewsInterface;

/**
 * Class NewsItem
 * @package Piuga\News\Helper
 */
class NewsItem extends AbstractHelper
{
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
     * NewsItem constructor.
     * @param Context $context
     * @param NewsRepositoryInterface $newsRepository
     * @param StoreManagerInterface $storeManager
     * @param DateTime $dateTime
     */
    public function __construct(
        Context $context,
        NewsRepositoryInterface $newsRepository,
        StoreManagerInterface $storeManager,
        DateTime $dateTime
    ) {
        parent::__construct($context);
        $this->newsRepository = $newsRepository;
        $this->storeManager = $storeManager;
        $this->dateTime = $dateTime;
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
     * Get news item detail page link
     *
     * @param NewsInterface $news
     * @return string
     */
    public function getItemUrl(NewsInterface $news) : string
    {
        return $this->_getUrl('news/item/view', ['id' => $news->getId()]);
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
}
