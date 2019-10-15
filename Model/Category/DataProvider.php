<?php
declare(strict_types=1);

namespace Piuga\News\Model\Category;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Magento\Ui\DataProvider\ModifierPoolDataProvider;
use Piuga\News\Api\CategoryRepositoryInterface;
use Piuga\News\Api\Data\CategoryInterface;
use Piuga\News\Api\Data\CategoryInterfaceFactory;
use Piuga\News\Model\ResourceModel\Category\Collection;
use Piuga\News\Model\ResourceModel\Category\CollectionFactory;

/**
 * Class DataProvider
 * @package Piuga\News\Model\Category
 */
class DataProvider extends ModifierPoolDataProvider
{
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var CategoryInterface
     */
    protected $categoryFactory;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var array
     */
    protected $loadedData = [];

    /**
     * DataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param CategoryRepositoryInterface $categoryRepository
     * @param CategoryInterfaceFactory $categoryFactory
     * @param DataPersistorInterface $dataPersistor
     * @param RequestInterface $request
     * @param array $meta
     * @param array $data
     * @param PoolInterface|null $pool
     */
    public function __construct(
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        CollectionFactory $collectionFactory,
        CategoryRepositoryInterface $categoryRepository,
        CategoryInterfaceFactory $categoryFactory,
        DataPersistorInterface $dataPersistor,
        RequestInterface $request,
        array $meta = [],
        array $data = [],
        PoolInterface $pool = null
    ) {
        $this->collection = $collectionFactory->create();
        $this->categoryRepository = $categoryRepository;
        $this->categoryFactory = $categoryFactory;
        $this->dataPersistor = $dataPersistor;
        $this->request = $request;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data, $pool);
    }

    /**
     * Get data
     *
     * @return array
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getData() : array
    {
        if (!empty($this->loadedData)) {
            return $this->loadedData;
        }

        /** @var CategoryInterface $item */
        $item = $this->getCurrentItem();
        if ($item) {
            $this->loadedData[$item->getId()] = $item->getData();
        }

        /** Check and assign data from data persistor */
        $data = $this->dataPersistor->get('piuga_news_category');
        if (!empty($data)) {
            $category = $this->categoryFactory->create();
            $category->setData($data);
            $this->loadedData[$category->getId()] = $category->getData();
            $this->dataPersistor->clear('piuga_news_category');
        }

        return $this->loadedData;
    }

    /**
     * Get current news category
     *
     * @return CategoryInterface|null
     * @throws NoSuchEntityException
     */
    public function getCurrentItem() : ?CategoryInterface
    {
        $requestId = (int)$this->request->getParam($this->requestFieldName);

        if ($requestId) {
            try {
                return $this->categoryRepository->getById($requestId);
            } catch (\Exception $e) {
                throw NoSuchEntityException::singleField('id', $requestId);
            }
        }

        return null;
    }
}
