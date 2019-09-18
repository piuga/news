<?php
declare(strict_types=1);

namespace Piuga\News\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Asset\Repository as AssetRepository;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;
use Piuga\News\Api\NewsRepositoryInterface;

/**
 * Class Image
 * @package Piuga\News\Ui\Component\Listing\Column
 */
class Image extends Column
{
    /**
     * Column constants
     */
    const NAME = 'image';
    const ALT_FIELD = 'title';

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var NewsRepositoryInterface
     */
    protected $newsRepository;

    /**
     * @var AssetRepository
     */
    protected $assetRepository;

    /**
     * Image constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param NewsRepositoryInterface $newsRepository
     * @param AssetRepository $assetRepository
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        NewsRepositoryInterface $newsRepository,
        AssetRepository $assetRepository,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->urlBuilder = $urlBuilder;
        $this->newsRepository = $newsRepository;
        $this->assetRepository = $assetRepository;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function prepareDataSource(array $dataSource) : array
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                $itemObject = $this->newsRepository->getById((int)$item['id']);
                if ($itemObject->getImage()) {
                    $imageUrl = $itemObject->getImageUrl();
                } else {
                    $imageUrl = $this->assetRepository->getUrl("Piuga_News::images/no-image.png");
                }
                $item[$fieldName . '_src'] = $imageUrl;
                $item[$fieldName . '_alt'] = $this->getAlt($item);
                $item[$fieldName . '_link'] = $this->urlBuilder->getUrl(
                    'piuga_news/news/edit',
                    ['id' => $itemObject->getId()]
                );
                $item[$fieldName . '_orig_src'] = $imageUrl;
            }
        }

        return $dataSource;
    }

    /**
     * Get Alt
     *
     * @param array $row
     * @return null|string
     */
    protected function getAlt(array $row) : ?string
    {
        $altField = $this->getData('config/altField') ?: self::ALT_FIELD;

        return $row[$altField] ?? null;
    }
}
