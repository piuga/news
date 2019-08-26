<?php
declare(strict_types=1);

namespace Piuga\News\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Piuga\News\Api\Data\NewsInterface;
use Piuga\News\Helper\NewsItem;

/**
 * Class NewsView
 * @package Piuga\News\ViewModel
 */
class NewsView implements ArgumentInterface
{
    /**
     * @var NewsItem
     */
    protected $newsHelper;

    /**
     * NewsView constructor.
     * @param NewsItem $newsHelper
     */
    public function __construct(
        NewsItem $newsHelper
    ) {
        $this->newsHelper = $newsHelper;
    }

    /**
     * Get current news item
     *
     * @return NewsInterface|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getNewsItem() : ?NewsInterface
    {
        return $this->newsHelper->getNewsItem();
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
}
