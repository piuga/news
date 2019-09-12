<?php
declare(strict_types=1);

namespace Piuga\News\Controller\Adminhtml\News;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;
use Piuga\News\Api\Data\NewsInterface;
use Piuga\News\Api\NewsRepositoryInterface;

/**
 * Class Edit
 * @package Piuga\News\Controller\Adminhtml\News
 */
class Edit extends Action implements HttpGetActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Piuga_News::edit_item';

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var NewsRepositoryInterface
     */
    protected $newsRepository;

    /**
     * @var NewsInterface
     */
    protected $news;

    /**
     * Edit constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param NewsRepositoryInterface $newsRepository
     * @param NewsInterface $news
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        NewsRepositoryInterface $newsRepository,
        NewsInterface $news
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->newsRepository = $newsRepository;
        $this->news = $news;
    }

    /**
     * Edit news page
     *
     * @return ResultInterface
     */
    public function execute() : ResultInterface
    {
        // 1. Get ID and create model
        $id = (int)$this->getRequest()->getParam('id');
        $news = $this->news;
        $newsRepository = $this->newsRepository;

        // 2. Initial checking and model loading
        if ($id) {
            try {
                $news = $newsRepository->getById($id);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('This news item no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }

        // 3. Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Piuga_News::news_add')
            ->addBreadcrumb(__('News'), __('News'))
            ->addBreadcrumb(__('Manage News'), __('Manage News'));
        $resultPage->getConfig()->getTitle()->prepend(__('News'));
        $resultPage->getConfig()->getTitle()->prepend($news->getId() ? $news->getTitle() : __('New Item'));

        return $resultPage;
    }
}
