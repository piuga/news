<?php
declare(strict_types=1);

namespace Piuga\News\Plugin\Widget;

use Piuga\News\Block\Widget\News;
use Piuga\News\Helper\NewsItem;

/**
 * Class NewsRendering
 * @package Piuga\News\Plugin\Widget
 */
class NewsRendering
{
    /**
     * @var NewsItem
     */
    protected $newsHelper;

    /**
     * NewsRendering constructor.
     * @param NewsItem $newsHelper
     */
    public function __construct(
        NewsItem $newsHelper
    ) {
        $this->newsHelper = $newsHelper;
    }

    /**
     * Render widget block if module is enabled
     *
     * @param News $widget
     * @param callable $proceed
     * @return string
     */
    public function aroundToHtml(News $widget, callable $proceed) : string
    {
        if (!$this->newsHelper->isActive()) {
            return '';
        }

        return $proceed();
    }
}
