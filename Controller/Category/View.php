<?php
declare(strict_types=1);

namespace Piuga\News\Controller\Category;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\View\Result\PageFactory;
use Piuga\News\Helper\NewsItem;

/**
 * Class View
 * @package Piuga\News\Controller\Category
 */
class View extends Action
{
    /**
     * @var NewsItem
     */
    protected $newsHelper;

    /**
     * @var ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * View constructor.
     * @param Context $context
     * @param NewsItem $newsHelper
     * @param ForwardFactory $resultForwardFactory
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        NewsItem $newsHelper,
        ForwardFactory $resultForwardFactory,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultForwardFactory = $resultForwardFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->newsHelper = $newsHelper;
    }

    /**
     * Renders news detail page

     * @return ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute() : ResultInterface
    {
        $newsCategory = $this->newsHelper->getNewsCategory();

        // If news category cannot be seen or module is disabled, then redirect to 404 page
        if (!$newsCategory || !$this->newsHelper->isActive()) {
            $resultForward = $this->resultForwardFactory->create();
            $resultForward->forward('noroute');

            return $resultForward;
        }

        $resultPage = $this->resultPageFactory->create();

        $resultPage->getConfig()->getTitle()->set($newsCategory->getTitle());
        /** Set metadata */
        $resultPage->getConfig()->setMetaTitle($newsCategory->getTitle());
        $resultPage->getConfig()->setDescription($newsCategory->getMetaDescription());
        $resultPage->getConfig()->setKeywords($newsCategory->getMetaKeywords());

        /** @var \Magento\Theme\Block\Html\Breadcrumbs $breadcrumbsBlock */
        $breadcrumbsBlock = $resultPage->getLayout()->getBlock('breadcrumbs');
        if ($breadcrumbsBlock) {
            $breadcrumbsBlock->addCrumb(
                'home',
                [
                    'label' => __('Home'),
                    'title'    => __('Home'),
                    'link'  => $this->_url->getUrl('')
                ]
            );
            $breadcrumbsBlock->addCrumb(
                'news',
                [
                    'label' => $this->newsHelper->getListTitle(),
                    'title'    => $this->newsHelper->getListTitle(),
                    'link'  => $this->_url->getUrl($this->newsHelper->getNewsUrl())
                ]
            );
            $breadcrumbsBlock->addCrumb(
                'news-category-' . $newsCategory->getId(),
                [
                    'label' => $newsCategory->getTitle(),
                    'title' => $newsCategory->getTitle()
                ]
            );
        }

        return $resultPage;
    }
}
