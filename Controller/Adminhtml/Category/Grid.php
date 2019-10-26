<?php
declare(strict_types=1);

namespace Piuga\News\Controller\Adminhtml\Category;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\View\LayoutFactory;
use Piuga\News\Api\CategoryRepositoryInterface;
use Piuga\News\Api\Data\CategoryInterface;

/**
 * Class Grid
 * @package Piuga\News\Controller\Adminhtml\Category
 */
class Grid extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Piuga_News::edit_category';

    /**
     * @var RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var LayoutFactory
     */
    protected $layoutFactory;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var CategoryInterface
     */
    protected $category;

    /**
     * Grid constructor.
     * @param Context $context
     * @param RawFactory $resultRawFactory
     * @param LayoutFactory $layoutFactory
     * @param CategoryRepositoryInterface $categoryRepository
     * @param CategoryInterface $category
     */
    public function __construct(
        Context $context,
        RawFactory $resultRawFactory,
        LayoutFactory $layoutFactory,
        CategoryRepositoryInterface $categoryRepository,
        CategoryInterface $category
    ) {
        parent::__construct($context);
        $this->resultRawFactory = $resultRawFactory;
        $this->layoutFactory = $layoutFactory;
        $this->categoryRepository = $categoryRepository;
        $this->category = $category;
    }

    /**
     * Grid Action
     * Display list of news related to current category
     *
     * @return ResultInterface
     */
    public function execute() : ResultInterface
    {
        $category = $this->initCategory();
        if (!$category) {
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();

            return $resultRedirect->setPath('piuga_news/*/', ['_current' => true, 'id' => null]);
        }
        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultRaw = $this->resultRawFactory->create();

        return $resultRaw->setContents(
            $this->layoutFactory->create()->createBlock(
                \Piuga\News\Block\Adminhtml\Category\Tab\News::class,
                'category.news.grid'
            )->toHtml()
        );
    }

    /**
     * Initialize category model
     *
     * @return CategoryInterface|null
     */
    protected function initCategory() : ?CategoryInterface
    {
        $id = (int)$this->getRequest()->getParam('id');
        $category = $this->category;
        $categoryRepository = $this->categoryRepository;
        if ($id) {
            try {
                $category = $categoryRepository->getById($id);
            } catch (\Exception $e) {
                return null;
            }
        }

        return $category;
    }
}
