<?php
declare(strict_types=1);

namespace Piuga\News\Block\Adminhtml\News\Edit;

use Magento\Backend\Block\Widget\Context;
use Piuga\News\Api\NewsRepositoryInterface;

/**
 * Class GenericButton
 * @package Piuga\News\Block\Adminhtml\News\Edit
 */
class GenericButton
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var NewsRepositoryInterface
     */
    protected $newsRepository;

    /**
     * GenericButton constructor.
     * @param Context $context
     * @param NewsRepositoryInterface $newsRepository
     */
    public function __construct(
        Context $context,
        NewsRepositoryInterface $newsRepository
    ) {
        $this->context = $context;
        $this->newsRepository = $newsRepository;
    }

    /**
     * Return News ID
     *
     * @return int|null
     */
    public function getNewsId() : ?int
    {
        try {
            return (int)$this->newsRepository->getById(
                (int)$this->context->getRequest()->getParam('id')
            )->getId();
        } catch (\Exception $e) {
        }

        return null;
    }

    /**
     * Generate URL by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl(string $route = '', array $params = []) : string
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
