<?php
declare(strict_types=1);

namespace Piuga\News\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchResults;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\Store;
use Piuga\News\Api\Data\CategoryInterface;
use Piuga\News\Api\Data\CategoryInterfaceFactory;
use Piuga\News\Api\Data\CategorySearchResultsInterfaceFactory;
use Piuga\News\Api\CategoryRepositoryInterface;
use Piuga\News\Model\ResourceModel\Category as CategoryResource;
use Piuga\News\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;

/**
 * Class CategoryRepository
 * @package Piuga\News\Model
 */
class CategoryRepository implements CategoryRepositoryInterface
{
    /**
     * @var CategoryResource
     */
    protected $resource;

    /**
     * @var CategoryInterface
     */
    protected $categoryInterfaceFactory;

    /**
     * @var CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var CategoryCollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var CategorySearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * CategoryRepository constructor.
     * @param CategoryResource $resource
     * @param CategoryFactory $categoryFactory
     * @param CategoryInterfaceFactory $categoryInterfaceFactory
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param CategorySearchResultsInterfaceFactory $newsSearchResultsInterfaceFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        CategoryResource $resource,
        CategoryFactory $categoryFactory,
        CategoryInterfaceFactory $categoryInterfaceFactory,
        CategoryCollectionFactory $categoryCollectionFactory,
        CategorySearchResultsInterfaceFactory $newsSearchResultsInterfaceFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->categoryFactory = $categoryFactory;
        $this->categoryInterfaceFactory = $categoryInterfaceFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->searchResultsFactory = $newsSearchResultsInterfaceFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function save(CategoryInterface $item) : CategoryInterface
    {
        if (!$item->getStores()) {
            $item->setStores((string)Store::DEFAULT_STORE_ID);
        }

        try {
            $this->resource->save($item);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the news category: %1',
                $exception->getMessage()
            ));
        }

        return $item;
    }

    /**
     * {@inheritdoc}
     */
    public function getById(int $id) : CategoryInterface
    {
        $item = $this->categoryFactory->create();
        $this->resource->load($item, $id);
        if (!$item->getId()) {
            throw new NoSuchEntityException(__('News category with ID "%1" does not exist.', $id));
        }

        return $item;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $criteria) : SearchResults
    {
        $collection = $this->categoryCollectionFactory->create();
        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(CategoryInterface $item) : bool
    {
        try {
            $this->resource->delete($item);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the news category: %1',
                $exception->getMessage()
            ));
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById(int $id) : bool
    {
        return $this->delete($this->getById($id));
    }
}
