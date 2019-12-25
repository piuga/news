<?php
declare(strict_types=1);

namespace Piuga\News\Controller\Adminhtml\Category\Widget;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Escaper;
use Magento\Framework\View\LayoutFactory;
use Piuga\News\Block\Adminhtml\Category\Widget\Chooser as ChooserBlock;

/**
 * Class Chooser
 * @package Piuga\News\Controller\Adminhtml\Category\Widget
 */
class Chooser extends Action
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Piuga_News::news';

    /**
     * @var LayoutFactory
     */
    protected $layoutFactory;

    /**
     * @var RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var Escaper
     */
    protected $escaper;

    /**
     * Chooser constructor.
     * @param Action\Context $context
     * @param RawFactory $resultRawFactory
     * @param LayoutFactory $layoutFactory
     * @param Escaper $escaper
     */
    public function __construct(
        Action\Context $context,
        RawFactory $resultRawFactory,
        LayoutFactory $layoutFactory,
        Escaper $escaper
    ) {
        parent::__construct($context);
        $this->layoutFactory = $layoutFactory;
        $this->resultRawFactory = $resultRawFactory;
        $this->escaper = $escaper;
    }

    /**
     * Chooser Source action
     *
     * @return ResultInterface
     */
    public function execute() : ResultInterface
    {
        $uniqId = $this->getRequest()->getParam('uniq_id');

        /** @var \Magento\Framework\View\Layout $layout */
        $layout = $this->layoutFactory->create();
        $categoriesGrid = $layout->createBlock(
            ChooserBlock::class,
            '',
            ['data' => ['id' => $this->escaper->escapeHtml($uniqId)]]
        );
        $html = $categoriesGrid->toHtml();

        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultRaw = $this->resultRawFactory->create();

        return $resultRaw->setContents($html);
    }
}
