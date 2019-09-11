<?php
declare(strict_types=1);

namespace Piuga\News\Controller\Adminhtml\News;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Piuga\News\Api\Data\NewsInterface;
use Piuga\News\Api\Data\NewsInterfaceFactory;
use Piuga\News\Api\NewsRepositoryInterface;
use Piuga\News\Model\News;

/**
 * Class Save
 * @package Piuga\News\Controller\Adminhtml\News
 */
class Save extends Action implements HttpPostActionInterface
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
     * @var NewsInterfaceFactory
     */
    protected $newsFactory;

    /**
     * @var PostDataProcessor
     */
    protected $dataProcessor;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * Save constructor.
     * @param Context $context
     * @param NewsRepositoryInterface $newsRepository
     * @param NewsInterfaceFactory $newsFactory
     * @param PostDataProcessor $dataProcessor
     * @param DataPersistorInterface $dataPersistor
     */
    public function __construct(
        Context $context,
        NewsRepositoryInterface $newsRepository,
        NewsInterfaceFactory $newsFactory,
        PostDataProcessor $dataProcessor,
        DataPersistorInterface $dataPersistor
    ) {
        parent::__construct($context);
        $this->newsRepository = $newsRepository;
        $this->newsFactory = $newsFactory;
        $this->dataProcessor = $dataProcessor;
        $this->dataPersistor = $dataPersistor;
    }

    /**
     * Save news action
     *
     * @return ResultInterface
     */
    public function execute() : ResultInterface
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();

        if ($data) {
            $data = $this->dataProcessor->filter($data);
            $id = (int)$this->getRequest()->getParam('id');
            // Unset ID if null or 0
            if (isset($data['id']) && !$data['id']) {
                unset($data['id']);
            }

            if ($id) {
                try {
                    /** @var NewsInterface $model */
                    $model = $this->newsRepository->getById($id);
                } catch (LocalizedException $e) {
                    $this->messageManager->addErrorMessage(__('This news item no longer exists.'));

                    return $resultRedirect->setPath('*/*/');
                }
            } else {
                /** @var NewsInterface $model */
                $model = $this->newsFactory->create();
            }
            $model->setData($data);

            if (!$this->dataProcessor->validate($data)) {
                return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId(), '_current' => true]);
            }

            try {
                $this->newsRepository->save($model);
                $this->messageManager->addSuccessMessage(__('News item was saved.'));
                $this->dataPersistor->clear('piuga_news');

                return $this->processReturn($model, $data, $resultRedirect);
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the news item.'));
            }
            $this->dataPersistor->set('piuga_news', $data);

            return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
        }

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Process and set the return
     *
     * @param NewsInterface $model
     * @param array $data
     * @param ResultInterface $resultRedirect
     * @return ResultInterface
     * @throws LocalizedException
     */
    private function processReturn(NewsInterface $model, array $data, ResultInterface $resultRedirect) : ResultInterface
    {
        $redirect = $data['back'] ?? 'close';

        if ($redirect ==='continue') {
            $resultRedirect->setPath('*/*/edit', ['id' => $model->getId()]);
        } else if ($redirect === 'close') {
            $resultRedirect->setPath('*/*/');
        } else if ($redirect === 'duplicate') {
            $duplicateModel = $this->newsFactory->create(['data' => $data]);
            $duplicateModel->setId(null);
            $duplicateModel->setCreatedAt(null);
            $duplicateModel->setUrlKey($data[NewsInterface::URL_KEY] . '-' . uniqid());
            $duplicateModel->setStatus(News::STATUS_DISABLED);
            $this->newsRepository->save($duplicateModel);

            $id = $duplicateModel->getId();
            $this->messageManager->addSuccessMessage(__('The news item was duplicated.'));
            $this->dataPersistor->set('piuga_news', $data);
            $resultRedirect->setPath('*/*/edit', ['id' => $id]);
        }

        return $resultRedirect;
    }
}
