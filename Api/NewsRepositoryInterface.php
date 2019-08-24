<?php
namespace Piuga\News\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResults;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Piuga\News\Api\Data\NewsInterface;

/**
 * Interface NewsRepositoryInterface
 * @package Piuga\News\Api
 */
interface NewsRepositoryInterface
{
    /**
     * Save news item
     *
     * @param NewsInterface $news
     * @return NewsInterface
     * @throws LocalizedException
     */
    public function save(NewsInterface $news) : NewsInterface;

    /**
     * Retrieve news item by ID
     *
     * @param int $id
     * @return NewsInterface
     * @throws LocalizedException
     */
    public function getById(int $id) : NewsInterface;

    /**
     * Retrieve news items matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return SearchResults
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria) : SearchResults;

    /**
     * Delete news item
     *
     * @param NewsInterface $news
     * @return bool (true on success)
     * @throws LocalizedException
     */
    public function delete(NewsInterface $news) : bool;

    /**
     * Delete news item by ID
     *
     * @param int $id
     * @return bool (true on success)
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById(int $id) : bool;
}
