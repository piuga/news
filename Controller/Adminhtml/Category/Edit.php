<?php
declare(strict_types=1);

namespace Piuga\News\Controller\Adminhtml\Category;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;
use Piuga\News\Api\CategoryRepositoryInterface;
use Piuga\News\Api\Data\CategoryInterface;

/**
 * Class Edit
 * @package Piuga\News\Controller\Adminhtml\Category
 */
class Edit extends Action implements HttpGetActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Piuga_News::edit_category';

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var CategoryInterface
     */
    protected $category;

    /**
     * Edit constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param CategoryRepositoryInterface $categoryRepository
     * @param CategoryInterface $category
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        CategoryRepositoryInterface $categoryRepository,
        CategoryInterface $category
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->categoryRepository = $categoryRepository;
        $this->category = $category;
    }

    /**
     * Edit category page
     *
     * @return ResultInterface
     */
    public function execute() : ResultInterface
    {
        // 1. Get ID and create model
        $id = (int)$this->getRequest()->getParam('id');
        $category = $this->category;
        $categoryRepository = $this->categoryRepository;

        // 2. Initial checking and model loading
        if ($id) {
            try {
                $category = $categoryRepository->getById($id);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('This category item no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }

        // 3. Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Piuga_News::category_add')
            ->addBreadcrumb(__('News'), __('News'))
            ->addBreadcrumb(__('Manage Categories'), __('Manage Categories'));
        $resultPage->getConfig()->getTitle()->prepend(__('News'));
        $resultPage->getConfig()->getTitle()->prepend($category->getId() ? $category->getTitle() : __('New Category'));

        return $resultPage;
    }
}
