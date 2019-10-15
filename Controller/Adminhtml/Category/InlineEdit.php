<?php
declare(strict_types=1);

namespace Piuga\News\Controller\Adminhtml\Category;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Piuga\News\Api\CategoryRepositoryInterface;
use Piuga\News\Api\Data\CategoryInterface;

/**
 * Class InlineEdit
 * @package Piuga\News\Controller\Adminhtml\Category
 */
class InlineEdit extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Piuga_News::save_category';

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var JsonFactory
     */
    protected $jsonFactory;

    /**
     * @var PostDataProcessor
     */
    protected $dataProcessor;

    /**
     * InlineEdit constructor.
     * @param Context $context
     * @param CategoryRepositoryInterface $categoryRepository
     * @param PostDataProcessor $dataProcessor
     * @param JsonFactory $jsonFactory
     */
    public function __construct(
        Context $context,
        CategoryRepositoryInterface $categoryRepository,
        PostDataProcessor $dataProcessor,
        JsonFactory $jsonFactory
    ) {
        parent::__construct($context);
        $this->categoryRepository = $categoryRepository;
        $this->dataProcessor = $dataProcessor;
        $this->jsonFactory = $jsonFactory;
    }

    /**
     * Save inline edit data
     *
     * @return ResponseInterface|ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        if ($this->getRequest()->getParam('isAjax')) {
            $postItems = $this->getRequest()->getParam('items', []);
            if (!count($postItems)) {
                $messages[] = __('Please correct the data sent.');
                $error = true;
            } else {
                foreach (array_keys($postItems) as $itemId) {
                    /** @var CategoryInterface $item */
                    $item = $this->categoryRepository->getById($itemId);
                    try {
                        $data = $this->filterCategories($postItems[$itemId]);
                        $this->validateCategories($data, $item, $error, $messages);
                        $item->setData(array_merge($item->getData(), $data));
                        $this->categoryRepository->save($item);
                    } catch (\Exception $e) {
                        $messages[] = $this->getErrorWithItemId(
                            $item,
                            (string)__($e->getMessage())
                        );
                        $error = true;
                    }
                }
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }

    /**
     * Filtering categories data
     *
     * @param array $data
     * @return array
     */
    protected function filterCategories(array $data = [])
    {
        return $this->dataProcessor->filter($data);
    }

    /**
     * Validate categories data
     *
     * @param array $data
     * @param CategoryInterface $item
     * @param bool $error
     * @param array $messages
     */
    protected function validateCategories(array $data, CategoryInterface $item, bool &$error, array &$messages)
    {
        if (!$this->dataProcessor->validate($data)) {
            $error = true;
            foreach ($this->messageManager->getMessages(true)->getItems() as $error) {
                $messages[] = $this->getErrorWithPageId($item, $error->getText());
            }
        }
    }

    /**
     * Add category ID to error message
     *
     * @param CategoryInterface $item
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithItemId(CategoryInterface $item, string $errorText) : string
    {
        return '[Category ID: ' . $item->getId() . '] ' . $errorText;
    }
}
