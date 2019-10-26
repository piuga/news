<?php
declare(strict_types=1);

namespace Piuga\News\Block\Adminhtml\Category\Tab;

use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Piuga\News\Api\CategoryRepositoryInterface;
use Piuga\News\Api\Data\CategoryInterface;
use Piuga\News\Model\NewsFactory;
use Piuga\News\Model\News\Source\Status;

/**
 * Class News
 * @package Piuga\News\Block\Adminhtml\Category\Tab
 */
class News extends Extended
{
    /**
     * @var NewsFactory
     */
    protected $newsFactory;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var Status
     */
    protected $status;

    /**
     * News constructor.
     * @param Context $context
     * @param Data $backendHelper
     * @param NewsFactory $newsFactory
     * @param CategoryRepositoryInterface $categoryRepository
     * @param Status $status
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $backendHelper,
        NewsFactory $newsFactory,
        CategoryRepositoryInterface $categoryRepository,
        Status $status,
        array $data = []
    ) {
        parent::__construct($context, $backendHelper, $data);
        $this->newsFactory = $newsFactory;
        $this->categoryRepository = $categoryRepository;
        $this->status = $status;
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('piuga_news_category_news');
        $this->setDefaultSort('position');
        $this->setDefaultDir('asc');
        $this->setUseAjax(true);
    }

    /**
     * Get current category instance
     *
     * @return CategoryInterface|null
     * @throws NoSuchEntityException
     */
    public function getCategory() : ?CategoryInterface
    {
        $requestId = (int)$this->getRequest()->getParam('id');
        if ($requestId) {
            try {
                return $this->categoryRepository->getById($requestId);
            } catch (\Exception $e) {
                throw NoSuchEntityException::singleField('id', $requestId);
            }
        }

        return null;
    }

    /**
     * Add selected news to collection filter depending on option
     *
     * @param Column $column
     * @return Extended
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in category flag
        if ($column->getId() === 'in_category') {
            $newsIds = $this->getSelectedNews();
            if (empty($newsIds)) {
                $newsIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('main_table.id', ['in' => $newsIds]);
            } elseif (!empty($newsIds)) {
                $this->getCollection()->addFieldToFilter('main_table.id', ['nin' => $newsIds]);
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareCollection()
    {
        if ($this->getCategory() && $this->getCategory()->getId()) {
            $this->setDefaultFilter(['in_category' => 1]);
        }

        $collection = $this->newsFactory->create()->getCollection();
        // Add position to collection
        $collection->getSelect()->joinLeft(
            'piuga_news_categories_items',
            'news_id = main_table.id AND category_id = ' . (int)$this->getRequest()->getParam('id', 0),
            'position'
        );
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'in_category',
            [
                'type' => 'checkbox',
                'name' => 'in_category',
                'values' => $this->getSelectedNews(),
                'index' => 'id',
                'header_css_class' => 'col-select col-massaction',
                'column_css_class' => 'col-select col-massaction'
            ]
        );
        $this->addColumn(
            'id',
            [
                'header' => __('ID'),
                'sortable' => true,
                'index' => 'id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            'title',
            [
                'header' => __('Title'),
                'index' => 'title'
            ]
        );
        $this->addColumn(
            'url_key',
            [
                'header' => __('URL key'),
                'index' => 'url_key'
            ]
        );
        $this->addColumn(
            'author',
            [
                'header' => __('Author'),
                'index' => 'author'
            ]
        );
        $this->addColumn(
            'status',
            [
                'type' => 'options',
                'options' => $this->status->getOptionArray(),
                'header' => __('Status'),
                'index' => 'status'
            ]
        );
        $this->addColumn(
            'publish_at',
            [
                'header' => __('Publish At'),
                'index' => 'publish_at'
            ]
        );
        $this->addColumn(
            'position',
            [
                'header' => __('Position'),
                'type' => 'number',
                'index' => 'position',
                'editable' => true
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * Get grid URL
     *
     * @return string
     */
    public function getGridUrl() : string
    {
        return $this->getUrl('piuga_news/category/grid', ['_current' => true]);
    }

    /**
     * Get selected news
     *
     * @return array
     * @throws NoSuchEntityException
     */
    protected function getSelectedNews() : array
    {
        $news = $this->getRequest()->getPost('selected_news') ?: [];
        if (empty($news) && $this->getCategory()) {
            $news = $this->getCategory()->getNewsPosition();
            return array_keys($news);
        }

        return $news;
    }
}
