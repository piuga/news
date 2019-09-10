<?php
declare(strict_types=1);

namespace Piuga\News\Controller\Adminhtml\News;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Piuga\News\Api\NewsRepositoryInterface;

/**
 * Class Delete
 * @package Piuga\News\Controller\Adminhtml\News
 */
class Delete extends Action implements HttpGetActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Piuga_News::delete_item';

    /**
     * @var NewsRepositoryInterface
     */
    protected $newsRepository;

    /**
     * Delete constructor.
     * @param Context $context
     * @param NewsRepositoryInterface $newsRepository
     */
    public function __construct(
        Context $context,
        NewsRepositoryInterface $newsRepository
    ) {
        parent::__construct($context);
        $this->newsRepository = $newsRepository;
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
                $this->newsRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('The news item was removed.'));

                // Go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());

                // Go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }
        $this->messageManager->addErrorMessage(__('The news item to be removed cannot be found.'));

        // Go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
