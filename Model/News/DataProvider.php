<?php
declare(strict_types=1);

namespace Piuga\News\Model\News;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Magento\Ui\DataProvider\ModifierPoolDataProvider;
use Piuga\News\Api\Data\NewsInterface;
use Piuga\News\Api\Data\NewsInterfaceFactory;
use Piuga\News\Api\NewsRepositoryInterface;
use Piuga\News\Model\FileInfo;
use Piuga\News\Model\ResourceModel\News\Collection;
use Piuga\News\Model\ResourceModel\News\CollectionFactory;

/**
 * Class DataProvider
 * @package Piuga\News\Model\News
 */
class DataProvider extends ModifierPoolDataProvider
{
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var NewsRepositoryInterface
     */
    protected $newsRepository;

    /**
     * @var NewsInterface
     */
    protected $newsFactory;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var FileInfo
     */
    private $fileInfo;

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
     * @param NewsRepositoryInterface $newsRepository
     * @param NewsInterfaceFactory $newsFactory
     * @param DataPersistorInterface $dataPersistor
     * @param RequestInterface $request
     * @param FileInfo $fileInfo
     * @param array $meta
     * @param array $data
     * @param PoolInterface|null $pool
     */
    public function __construct(
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        CollectionFactory $collectionFactory,
        NewsRepositoryInterface $newsRepository,
        NewsInterfaceFactory $newsFactory,
        DataPersistorInterface $dataPersistor,
        RequestInterface $request,
        FileInfo $fileInfo,
        array $meta = [],
        array $data = [],
        PoolInterface $pool = null
    ) {
        $this->collection = $collectionFactory->create();
        $this->newsRepository = $newsRepository;
        $this->newsFactory = $newsFactory;
        $this->dataPersistor = $dataPersistor;
        $this->request = $request;
        $this->fileInfo = $fileInfo;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data, $pool);
    }

    /**
     * Get data
     *
     * @return array
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getData() : array
    {
        if (!empty($this->loadedData)) {
            return $this->loadedData;
        }

        /** @var NewsInterface $item */
        $item = $this->getCurrentItem();
        if ($item) {
            $newsData = $item->getData();
            $newsData = $this->convertValues($item, $newsData);
            $this->loadedData[$item->getId()] = $newsData;
        }

        /** Check and assign data from data persistor */
        $data = $this->dataPersistor->get('piuga_news');
        if (!empty($data)) {
            $news = $this->newsFactory->create();
            $news->setData($data);
            $this->loadedData[$news->getId()] = $news->getData();
            $this->dataPersistor->clear('piuga_news');
        }

        return $this->loadedData;
    }

    /**
     * Get current news item
     *
     * @return NewsInterface|null
     * @throws NoSuchEntityException
     */
    public function getCurrentItem() : ?NewsInterface
    {
        $requestId = (int)$this->request->getParam($this->requestFieldName);

        if ($requestId) {
            try {
                return $this->newsRepository->getById($requestId);
            } catch (\Exception $e) {
                throw NoSuchEntityException::singleField('id', $requestId);
            }
        }

        return null;
    }

    /**
     * Convert file data for form render
     *
     * @param NewsInterface $item
     * @param array $data
     * @return array
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function convertValues(NewsInterface $item, array $data) : array
    {
        $attributes = [
            NewsInterface::IMAGE => $item->getImageUrl(NewsInterface::IMAGE),
            NewsInterface::FILE => $item->getFileUrl(NewsInterface::FILE)
        ];

        foreach ($attributes as $attributeCode => $value) {
            if (isset($data[$attributeCode])) {
                unset($data[$attributeCode]);

                $fileName = $item->getData($attributeCode);

                if ($this->fileInfo->isExist($fileName)) {
                    $stat = $this->fileInfo->getStat($fileName);
                    $mime = $this->fileInfo->getMimeType($fileName);

                    $data[$attributeCode][0]['name'] = basename($fileName);

                    if ($this->fileInfo->isBeginsWithMediaDirectoryPath($fileName)) {
                        $data[$attributeCode][0]['url'] = $fileName;
                    } else {
                        $data[$attributeCode][0]['url'] = $value;
                    }

                    $data[$attributeCode][0]['size'] = isset($stat) ? $stat['size'] : 0;
                    $data[$attributeCode][0]['type'] = $mime;
                }
            }
        }

        return $data;
    }
}
