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
use Piuga\News\Api\Data\NewsInterface;
use Piuga\News\Api\Data\NewsInterfaceFactory;
use Piuga\News\Api\Data\NewsSearchResultsInterfaceFactory;
use Piuga\News\Api\NewsRepositoryInterface;
use Piuga\News\Model\ResourceModel\News as NewsResource;
use Piuga\News\Model\ResourceModel\News\CollectionFactory as NewsCollectionFactory;

/**
 * Class NewsRepository
 * @package Delifrance\PsApiConnection\Model
 */
class NewsRepository implements NewsRepositoryInterface
{
    /**
     * @var NewsResource
     */
    protected $resource;

    /**
     * @var NewsInterface
     */
    protected $newsInterfaceFactory;

    /**
     * @var NewsFactory
     */
    protected $newsFactory;

    /**
     * @var NewsCollectionFactory
     */
    protected $newsCollectionFactory;

    /**
     * @var NewsSearchResultsInterfaceFactory
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
     * NewsRepository constructor.
     * @param NewsResource $resource
     * @param NewsFactory $newsFactory
     * @param NewsInterfaceFactory $newsInterfaceFactory
     * @param NewsCollectionFactory $newsCollectionFactory
     * @param NewsSearchResultsInterfaceFactory $newsSearchResultsInterfaceFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        NewsResource $resource,
        NewsFactory $newsFactory,
        NewsInterfaceFactory $newsInterfaceFactory,
        NewsCollectionFactory $newsCollectionFactory,
        NewsSearchResultsInterfaceFactory $newsSearchResultsInterfaceFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->newsFactory = $newsFactory;
        $this->newsInterfaceFactory = $newsInterfaceFactory;
        $this->newsCollectionFactory = $newsCollectionFactory;
        $this->searchResultsFactory = $newsSearchResultsInterfaceFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function save(NewsInterface $item) : NewsInterface
    {
        if (!$item->getStores()) {
            $item->setStores((string)Store::DEFAULT_STORE_ID);
        }

        try {
            $this->resource->save($item);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the news item: %1',
                $exception->getMessage()
            ));
        }

        return $item;
    }

    /**
     * {@inheritdoc}
     */
    public function getById(int $id) : NewsInterface
    {
        $item = $this->newsFactory->create();
        $this->resource->load($item, $id);
        if (!$item->getId()) {
            throw new NoSuchEntityException(__('News item with ID "%1" does not exist.', $id));
        }

        return $item;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $criteria) : SearchResults
    {
        $collection = $this->newsCollectionFactory->create();
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
    public function delete(NewsInterface $item) : bool
    {
        try {
            $this->resource->delete($item);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the news item: %1',
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
