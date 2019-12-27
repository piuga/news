<?php
declare(strict_types=1);

namespace Piuga\News\Plugin\View\Page\Config;

use Magento\Framework\View\Page\Config;
use Piuga\News\Helper\NewsItem;

/**
 * Class CategoryMetaTitle
 * @package Piuga\News\Plugin\View\Page\Config
 */
class CategoryMetaTitle
{
    /**
     * @var NewsItem
     */
    protected $newsHelper;

    /**
     * CategoryMetaTitle constructor.
     * @param NewsItem $newsHelper
     */
    public function __construct(
        NewsItem $newsHelper
    ) {
        $this->newsHelper = $newsHelper;
    }

    /**
     * Update news item page title - add category title
     *
     * @param Config $subject
     * @param string $metaTitle
     * @return array
     */
    public function beforeSetMetaTitle(Config $subject, string $metaTitle) : array
    {
        try {
            if (
                ($category = $this->newsHelper->getNewsCategory()) &&
                ($newsItem = $this->newsHelper->getNewsItem())
            ) {
                $metaTitle .= ' - ' . $category->getTitle();
            }
        } catch (\Exception $e) {
        }

        return [$metaTitle];
    }
}
