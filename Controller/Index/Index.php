<?php
declare(strict_types=1);

namespace Piuga\News\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\View\Result\PageFactory;
use Piuga\News\Helper\NewsItem;

/**
 * Class Index
 * @package Piuga\News\Controller\Index
 */
class Index extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var NewsItem
     */
    protected $newsHelper;

    /**
     * Index constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param ForwardFactory $resultForwardFactory
     * @param NewsItem $newsHelper
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        ForwardFactory $resultForwardFactory,
        NewsItem $newsHelper
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->newsHelper = $newsHelper;
    }

    /**
     * Renders news list page
     *
     * @return ResultInterface
     */
    public function execute() : ResultInterface
    {
        // If module is disabled, then redirect to 404 page
        if (!$this->newsHelper->isActive()) {
            $resultForward = $this->resultForwardFactory->create();
            $resultForward->forward('noroute');

            return $resultForward;
        }

        $resultPage = $this->resultPageFactory->create();

        $resultPage->getConfig()->getTitle()->set($this->newsHelper->getListTitle());
        /** Set metadata */
        $resultPage->getConfig()->setMetaTitle($this->newsHelper->getMetaTitle());
        $resultPage->getConfig()->setDescription($this->newsHelper->getMetaDescription());
        $resultPage->getConfig()->setKeywords($this->newsHelper->getMetaKeywords());

        /** @var \Magento\Theme\Block\Html\Breadcrumbs $breadcrumbsBlock */
        $breadcrumbsBlock = $resultPage->getLayout()->getBlock('breadcrumbs');
        if ($breadcrumbsBlock) {
            $breadcrumbsBlock->addCrumb(
                'home',
                [
                    'label'    => __('Home'),
                    'title'    => __('Home'),
                    'link'     => $this->_url->getUrl('')
                ]
            );
            $breadcrumbsBlock->addCrumb(
                'news',
                [
                    'label'    => $this->newsHelper->getListTitle(),
                    'title'    => $this->newsHelper->getListTitle()
                ]
            );
        }

        return $resultPage;
    }
}
