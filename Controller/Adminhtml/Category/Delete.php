<?php
declare(strict_types=1);

namespace Piuga\News\Controller\Adminhtml\Category;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Piuga\News\Api\CategoryRepositoryInterface;

/**
 * Class Delete
 * @package Piuga\News\Controller\Adminhtml\Category
 */
class Delete extends Action implements HttpGetActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Piuga_News::delete_category';

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * Delete constructor.
     * @param Context $context
     * @param CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(
        Context $context,
        CategoryRepositoryInterface $categoryRepository
    ) {
        parent::__construct($context);
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Delete action
     *
     * @return ResultInterface
     */
    public function execute() : ResultInterface
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = (int)$this->getRequest()->getParam('id');
        if ($id) {
            try {
                // Delete model by ID
                $this->categoryRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('The news category was removed.'));

                // Go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());

                // Go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }
        $this->messageManager->addErrorMessage(__('The news category to be removed cannot be found.'));

        // Go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
