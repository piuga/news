<?php
declare(strict_types=1);

namespace Piuga\News\Controller\Item;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\View\Result\PageFactory;
use Piuga\News\Helper\NewsItem;

/**
 * Class View
 * @package Piuga\News\Controller\Item
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
        $newsItem = $this->newsHelper->getNewsItem();

        // If news cannot be seen redirect to 404 page
        if (!$newsItem) {
            $resultForward = $this->resultForwardFactory->create();
            $resultForward->forward('noroute');

            return $resultForward;
        }

        $resultPage = $this->resultPageFactory->create();

        $resultPage->getConfig()->getTitle()->set($newsItem->getTitle());
        /** Set metadata */
        $resultPage->getConfig()->setMetaTitle($newsItem->getTitle());
        $resultPage->getConfig()->setDescription($newsItem->getMetaDescription());
        $resultPage->getConfig()->setKeywords($newsItem->getMetaKeywords());

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
                    'label' => __('News'),
                    'title'    => __('News'),
                    'link'  => $this->_url->getUrl('news')
                ]
            );
            $breadcrumbsBlock->addCrumb(
                'news-' . $newsItem->getId(),
                [
                    'label' => $newsItem->getTitle(),
                    'title' => $newsItem->getTitle()
                ]
            );
        }

        return $resultPage;
    }
}
