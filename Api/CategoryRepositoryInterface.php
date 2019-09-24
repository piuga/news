<?php
namespace Piuga\News\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResults;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Piuga\News\Api\Data\CategoryInterface;

/**
 * Interface CategoryRepositoryInterface
 * @package Piuga\News\Api
 */
interface CategoryRepositoryInterface
{
    /**
     * Save news category
     *
     * @param CategoryInterface $news
     * @return CategoryInterface
     * @throws LocalizedException
     */
    public function save(CategoryInterface $news) : CategoryInterface;

    /**
     * Retrieve news category by ID
     *
     * @param int $id
     * @return CategoryInterface
     * @throws LocalizedException
     */
    public function getById(int $id) : CategoryInterface;

    /**
     * Retrieve news categories matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return SearchResults
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria) : SearchResults;

    /**
     * Delete news category
     *
     * @param CategoryInterface $news
     * @return bool (true on success)
     * @throws LocalizedException
     */
    public function delete(CategoryInterface $news) : bool;

    /**
     * Delete news category by ID
     *
     * @param int $id
     * @return bool (true on success)
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById(int $id) : bool;
}
