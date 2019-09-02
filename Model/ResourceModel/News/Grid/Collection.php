<?php
declare(strict_types=1);

namespace Piuga\News\Model\ResourceModel\News\Grid;

use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Api\Search\AggregationInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\Document;
use Piuga\News\Model\ResourceModel\News\Collection as NewsCollection;
use Psr\Log\LoggerInterface;

/**
 * Class Collection
 * @package Piuga\News\Model\ResourceModel\News\Grid
 */
class Collection extends NewsCollection implements SearchResultInterface
{
    /**
     * @var AggregationInterface
     */
    protected $aggregations;

    /**
     * Collection constructor.
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param string $mainTable
     * @param string $eventPrefix
     * @param string $eventObject
     * @param string $resourceModel
     * @param string $model
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        string $mainTable,
        string $eventPrefix,
        string $eventObject,
        string $resourceModel,
        string $model = Document::class
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, null, null);
        $this->_eventPrefix = $eventPrefix;
        $this->_eventObject = $eventObject;
        $this->_init($model, $resourceModel);
        $this->setMainTable($mainTable);
    }

    /**
     * {@inheritdoc}
     */
    public function getAggregations() : AggregationInterface
    {
        return $this->aggregations;
    }

    /**
     * {@inheritdoc}
     */
    public function setAggregations($aggregations) : Collection
    {
        $this->aggregations = $aggregations;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchCriteria()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function setSearchCriteria(SearchCriteriaInterface $searchCriteria = null) : Collection
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalCount() : int
    {
        return $this->getSize();
    }

    /**
     * {@inheritdoc}
     */
    public function setTotalCount($totalCount) : Collection
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setItems(array $items = null) : Collection
    {
        return $this;
    }

    /**
     * Override addFieldToFilter to filter by varchar data for store
     *
     * @param array|string $field
     * @param string|array|null $condition
     * @return NewsCollection
     */
    public function addFieldToFilter($field, $condition = null) : NewsCollection
    {
        if ($field == 'stores') {
            $store = $condition['eq'];

            return parent::addFieldToFilter(
                array('stores', 'stores'),
                array(
                    array('finset' => $store)
                )
            );
        } else {
            return parent::addFieldToFilter($field, $condition);
        }
    }
}
