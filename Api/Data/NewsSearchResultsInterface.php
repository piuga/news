<?php
namespace Piuga\News\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface NewsSearchResultsInterface
 * @package Piuga\News\Api\Data
 */
interface NewsSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get news items list
     *
     * @return NewsInterface[]
     */
    public function getItems() : array;

    /**
     * Set news items list
     *
     * @param NewsInterface[] $items
     * @return NewsSearchResultsInterface
     */
    public function setItems(array $items) : NewsSearchResultsInterface;
}
