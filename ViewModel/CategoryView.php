<?php
declare(strict_types=1);

namespace Piuga\News\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Piuga\News\Api\Data\CategoryInterface;
use Piuga\News\Helper\NewsItem;

/**
 * Class CategoryView
 * @package Piuga\News\ViewModel
 */
class CategoryView implements ArgumentInterface
{
    /**
     * @var NewsItem
     */
    protected $newsHelper;

    /**
     * CategoryView constructor.
     * @param NewsItem $newsHelper
     */
    public function __construct(
        NewsItem $newsHelper
    ) {
        $this->newsHelper = $newsHelper;
    }

    /**
     * Get current news category
     *
     * @return CategoryInterface|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getNewsCategory() : ?CategoryInterface
    {
        return $this->newsHelper->getNewsCategory();
    }
}
