<?php
declare(strict_types=1);

namespace Piuga\News\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;

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
     * Index constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Renders news list page
     *
     * @return ResultInterface
     */
    public function execute() : ResultInterface
    {
        $resultPage = $this->resultPageFactory->create();

        $resultPage->getConfig()->getTitle()->set(__('News'));
        /** Set metadata */
        $resultPage->getConfig()->setMetaTitle(__('News'));
        $resultPage->getConfig()->setDescription(__('Fresh dummy news'));
        $resultPage->getConfig()->setKeywords(__('news, fresh news, informations'));

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
                    'label'    => __('News'),
                    'title'    => __('News')
                ]
            );
        }

        return $resultPage;
    }
}
