<?php
namespace Piuga\News\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface CategorySearchResultsInterface
 * @package Piuga\News\Api\Data
 */
interface CategorySearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get news category list
     *
     * @return CategoryInterface[]
     */
    public function getItems() : array;

    /**
     * Set news category list
     *
     * @param CategoryInterface[] $items
     * @return CategorySearchResultsInterface
     */
    public function setItems(array $items) : CategorySearchResultsInterface;
}
