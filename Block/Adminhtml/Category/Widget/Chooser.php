<?php
declare(strict_types=1);

namespace Piuga\News\Block\Adminhtml\Category\Widget;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\View\Model\PageLayout\Config\BuilderInterface;
use Magento\Widget\Block\Adminhtml\Widget\Chooser as WidgetChooser;
use Piuga\News\Api\CategoryRepositoryInterface;
use Piuga\News\Model\ResourceModel\Category\CollectionFactory;
use Piuga\News\Model\News\Source\Status;

/**
 * Class Chooser
 * @package Piuga\News\Block\Adminhtml\Category\Widget
 */
class Chooser extends Extended
{
    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var BuilderInterface
     */
    protected $pageLayoutBuilder;

    /**
     * @var Status
     */
    protected $status;

    /**
     * Chooser constructor.
     * @param Context $context
     * @param Data $backendHelper
     * @param CategoryRepositoryInterface $categoryRepository
     * @param CollectionFactory $collectionFactory
     * @param BuilderInterface $pageLayoutBuilder
     * @param Status $status
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $backendHelper,
        CategoryRepositoryInterface $categoryRepository,
        CollectionFactory $collectionFactory,
        BuilderInterface $pageLayoutBuilder,
        Status $status,
        array $data = []
    ) {
        parent::__construct($context, $backendHelper, $data);
        $this->pageLayoutBuilder = $pageLayoutBuilder;
        $this->categoryRepository = $categoryRepository;
        $this->collectionFactory = $collectionFactory;
        $this->status = $status;
    }

    /**
     * Block construction, prepare grid params
     *
     * @return void
     */
    protected function _construct() {
        parent::_construct();
        $this->setUseAjax(true);
        $this->setDefaultFilter(['chooser_is_active' => '1']);
    }

    /**
     * Prepare chooser element HTML
     *
     * @param AbstractElement $element
     * @return AbstractElement
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function prepareElementHtml(AbstractElement $element) : AbstractElement
    {
        $uniqId = $this->mathRandom->getUniqueHash($element->getId());
        $sourceUrl = $this->getUrl('piuga_news/category_widget/chooser', ['uniq_id' => $uniqId]);

        $chooser = $this->getLayout()->createBlock(
            WidgetChooser::class
        )->setElement(
            $element
        )->setConfig(
            $this->getConfig()
        )->setFieldsetId(
            $this->getFieldsetId()
        )->setSourceUrl(
            $sourceUrl
        )->setUniqId(
            $uniqId
        );

        if ($element->getValue()) {
            $category = $this->categoryRepository->getById((int)$element->getValue());
            if ($category->getId()) {
                $chooser->setLabel($this->escapeHtml($category->getTitle()));
            }
        }

        $element->setData('after_element_html', $chooser->toHtml());

        return $element;
    }

    /**
     * Grid Row JS Callback
     *
     * @return string
     */
    public function getRowClickCallback() : string
    {
        $chooserJsObject = $this->getId();

        return '
            function (grid, event) {
                var trElement = Event.findElement(event, "tr");
                var categoryTitle = trElement.down("td").next().innerHTML;
                var categoryId = trElement.down("td").innerHTML.replace(/^\s+|\s+$/g,"");'
                . $chooserJsObject . '.setElementValue(categoryId);'
                . $chooserJsObject . '.setElementLabel(categoryTitle);'
                . $chooserJsObject . '.close();
            }
        ';
    }

    /**
     * Prepare collection
     *
     * @return Extended
     */
    protected function _prepareCollection()
    {
        $collection = $this->collectionFactory->create();
        /* @var $collection \Piuga\News\Model\ResourceModel\Category\Collection */
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare columns for grid
     *
     * @return Extended
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'chooser_id',
            [
                'header' => __('ID'),
                'index' => 'id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );

        $this->addColumn(
            'chooser_title',
            [
                'header' => __('Title'),
                'index' => 'title',
                'header_css_class' => 'col-title',
                'column_css_class' => 'col-title'
            ]
        );

        $this->addColumn(
            'chooser_identifier',
            [
                'header' => __('URL Key'),
                'index' => 'url_key',
                'header_css_class' => 'col-url',
                'column_css_class' => 'col-url'
            ]
        );

        $this->addColumn(
            'chooser_is_active',
            [
                'header' => __('Status'),
                'index' => 'status',
                'type' => 'options',
                'options' => $this->status->getOptionArray(),
                'header_css_class' => 'col-status',
                'column_css_class' => 'col-status'
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * Get grid url
     *
     * @return string
     */
    public function getGridUrl() : string
    {
        return $this->getUrl(
            'piuga_news/category_widget/chooser',
            [
                '_current' => true,
                'uniq_id' => $this->getId()
            ]
        );
    }
}
