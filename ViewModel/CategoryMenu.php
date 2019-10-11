<?php
declare(strict_types=1);

namespace Piuga\News\ViewModel;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\StoreManagerInterface;
use Piuga\News\Api\CategoryRepositoryInterface;
use Piuga\News\Api\Data\CategoryInterface;
use Piuga\News\Controller\Router;
use Piuga\News\Helper\NewsItem;

/**
 * Class CategoryMenu
 * @package Piuga\News\ViewModel
 */
class CategoryMenu implements ArgumentInterface
{
    /**
     * @var NewsItem
     */
    protected $newsHelper;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @var FilterGroupBuilder
     */
    protected $filterGroupBuilder;

    /**
     * @var SortOrder
     */
    protected $sortOrder;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * CategoryMenu constructor.
     * @param NewsItem $newsHelper
     * @param CategoryRepositoryInterface $categoryRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param FilterGroupBuilder $filterGroupBuilder
     * @param SortOrder $sortOrder
     * @param StoreManagerInterface $storeManager
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        NewsItem $newsHelper,
        CategoryRepositoryInterface $categoryRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        FilterGroupBuilder $filterGroupBuilder,
        SortOrder $sortOrder,
        StoreManagerInterface $storeManager,
        UrlInterface $urlBuilder
    ) {
        $this->newsHelper = $newsHelper;
        $this->categoryRepository = $categoryRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->filterGroupBuilder = $filterGroupBuilder;
        $this->sortOrder = $sortOrder;
        $this->storeManager = $storeManager;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Get all active categories
     *
     * @return array|null
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getActiveCategories() : ?array
    {
        // Get current store ID
        $currentStoreId = $this->storeManager->getStore()->getId();
        // Prepare stores array for filter
        $stores = [\Magento\Store\Model\Store::DEFAULT_STORE_ID, $currentStoreId];

        // Prepare filters
        $filters = [
            // Active status filter
            $this->filterBuilder->setConditionType('eq')->setField(CategoryInterface::STATUS)->setValue(1)->create(),
            // Category stores contains admin (0) or current store ID
            $this->filterBuilder->setConditionType('in')->setField(CategoryInterface::STORES)->setValue($stores)->create()
        ];
        // Group filters for AND statement
        $filterGroups = [];
        foreach ($filters as $filter) {
            $filterGroups[] = $this->filterGroupBuilder->setFilters([$filter])->create();
        }
        // Apply filters
        $this->searchCriteriaBuilder->setFilterGroups($filterGroups);
        // Prepare sort order
        $orders = [
            // Order by position: low number first
            $this->sortOrder->setField(CategoryInterface::POSITION)->setDirection(SortOrder::SORT_ASC)
        ];
        // Apply sort order
        $this->searchCriteriaBuilder->setSortOrders($orders);
        $categories = $this->categoryRepository
            ->getList($this->searchCriteriaBuilder->create())
            ->getItems();

        return $categories;
    }

    /**
     * Get category URL
     *
     * @param CategoryInterface $category
     * @return string
     */
    public function getCategoryLink(CategoryInterface $category) : string
    {
        $urlPrefix = trim($this->newsHelper->getNewsUrl(), '/');

        return $this->urlBuilder->getUrl($urlPrefix . '/' . $category->getUrlKey());
    }
}
