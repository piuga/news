<?php
declare(strict_types=1);

namespace Piuga\News\Block\Adminhtml\Category\Edit;

use Magento\Backend\Block\Widget\Context;
use Piuga\News\Api\CategoryRepositoryInterface;

/**
 * Class GenericButton
 * @package Piuga\News\Block\Adminhtml\Category\Edit
 */
class GenericButton
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * GenericButton constructor.
     * @param Context $context
     * @param CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(
        Context $context,
        CategoryRepositoryInterface $categoryRepository
    ) {
        $this->context = $context;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Return category ID
     *
     * @return int|null
     */
    public function getCategoryId() : ?int
    {
        try {
            return (int)$this->categoryRepository->getById(
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
