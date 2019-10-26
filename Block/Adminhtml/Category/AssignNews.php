<?php
declare(strict_types=1);

namespace Piuga\News\Block\Adminhtml\Category;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\BlockInterface;
use Piuga\News\Api\CategoryRepositoryInterface;
use Piuga\News\Api\Data\CategoryInterface;
use Piuga\News\Block\Adminhtml\Category\Tab\News;

/**
 * Class AssignNews
 * @package Piuga\News\Block\Adminhtml\Category
 */
class AssignNews extends Template
{
    /**
     * Block template
     *
     * @var string
     */
    protected $_template = 'Piuga_News::category/edit/assign_news.phtml';

    /**
     * @var News
     */
    protected $blockGrid;

    /**
     * @var Json
     */
    protected $jsonEncoder;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * AssignNews constructor.
     * @param Context $context
     * @param Json $jsonEncoder
     * @param CategoryRepositoryInterface $categoryRepository
     * @param RequestInterface $request
     * @param array $data
     */
    public function __construct(
        Context $context,
        Json $jsonEncoder,
        CategoryRepositoryInterface $categoryRepository,
        RequestInterface $request,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->categoryRepository = $categoryRepository;
        $this->request = $request;
        $this->jsonEncoder = $jsonEncoder;
    }

    /**
     * Retrieve instance of grid block
     *
     * @return BlockInterface
     * @throws LocalizedException
     */
    public function getBlockGrid() : BlockInterface
    {
        if (null === $this->blockGrid) {
            $this->blockGrid = $this->getLayout()->createBlock(
                News::class,
                'category.news.grid'
            );
        }

        return $this->blockGrid;
    }

    /**
     * Return HTML of grid block
     *
     * @return string
     * @throws LocalizedException
     */
    public function getGridHtml() : string
    {
        return $this->getBlockGrid()->toHtml();
    }

    /**
     * Get news data
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getNewsJson() : string
    {
        $news = $this->getCategory() ? $this->getCategory()->getNewsPosition() : null;
        if (!empty($news)) {
            return $this->jsonEncoder->serialize($news);
        }

        return '{}';
    }

    /**
     * Get current category instance
     *
     * @return CategoryInterface|null
     * @throws NoSuchEntityException
     */
    public function getCategory() : ?CategoryInterface
    {
        $requestId = (int)$this->request->getParam('id');
        if ($requestId) {
            try {
                return $this->categoryRepository->getById($requestId);
            } catch (\Exception $e) {
                throw NoSuchEntityException::singleField('id', $requestId);
            }
        }

        return null;
    }
}
