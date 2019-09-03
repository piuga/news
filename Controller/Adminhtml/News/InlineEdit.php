<?php
declare(strict_types=1);

namespace Piuga\News\Controller\Adminhtml\News;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Piuga\News\Api\Data\NewsInterface;
use Piuga\News\Api\NewsRepositoryInterface;

/**
 * Class InlineEdit
 * @package Piuga\News\Controller\Adminhtml\News
 */
class InlineEdit extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Piuga_News::save_item';

    /**
     * @var NewsRepositoryInterface
     */
    protected $newsRepository;

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
     * @param NewsRepositoryInterface $newsRepository
     * @param PostDataProcessor $dataProcessor
     * @param JsonFactory $jsonFactory
     */
    public function __construct(
        Context $context,
        NewsRepositoryInterface $newsRepository,
        PostDataProcessor $dataProcessor,
        JsonFactory $jsonFactory
    ) {
        parent::__construct($context);
        $this->newsRepository = $newsRepository;
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
                    /** @var NewsInterface $item */
                    $item = $this->newsRepository->getById($itemId);
                    try {
                        $data = $this->filterNews($postItems[$itemId]);
                        $this->validateNews($data, $item, $error, $messages);
                        $item->setData(array_merge($item->getData(), $data));
                        $this->newsRepository->save($item);
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
     * Filtering news data
     *
     * @param array $data
     * @return array
     */
    protected function filterNews(array $data = [])
    {
        return $this->dataProcessor->filter($data);
    }

    /**
     * Validate news data
     *
     * @param array $data
     * @param NewsInterface $item
     * @param bool $error
     * @param array $messages
     */
    protected function validateNews(array $data, NewsInterface $item, bool &$error, array &$messages)
    {
        if (!$this->dataProcessor->validate($data)) {
            $error = true;
            foreach ($this->messageManager->getMessages(true)->getItems() as $error) {
                $messages[] = $this->getErrorWithPageId($item, $error->getText());
            }
        }
    }

    /**
     * Add news ID to error message
     *
     * @param NewsInterface $item
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithItemId(NewsInterface $item, string $errorText) : string
    {
        return '[News ID: ' . $item->getId() . '] ' . $errorText;
    }
}
