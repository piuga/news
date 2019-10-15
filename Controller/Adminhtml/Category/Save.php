<?php
declare(strict_types=1);

namespace Piuga\News\Controller\Adminhtml\Category;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Piuga\News\Api\Data\CategoryInterface;
use Piuga\News\Api\Data\CategoryInterfaceFactory;
use Piuga\News\Api\CategoryRepositoryInterface;
use Piuga\News\Model\Category;

/**
 * Class Save
 * @package Piuga\News\Controller\Adminhtml\Category
 */
class Save extends Action implements HttpPostActionInterface
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
     * @var CategoryInterfaceFactory
     */
    protected $categoryFactory;

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
     * @param CategoryRepositoryInterface $categoryRepository
     * @param CategoryInterfaceFactory $categoryFactory
     * @param PostDataProcessor $dataProcessor
     * @param DataPersistorInterface $dataPersistor
     */
    public function __construct(
        Context $context,
        CategoryRepositoryInterface $categoryRepository,
        CategoryInterfaceFactory $categoryFactory,
        PostDataProcessor $dataProcessor,
        DataPersistorInterface $dataPersistor
    ) {
        parent::__construct($context);
        $this->categoryRepository = $categoryRepository;
        $this->categoryFactory = $categoryFactory;
        $this->dataProcessor = $dataProcessor;
        $this->dataPersistor = $dataPersistor;
    }

    /**
     * Save category action
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

            // Check and fill URL key
            $data = $this->checkUrlKey($data);

            if ($id) {
                try {
                    /** @var CategoryInterface $model */
                    $model = $this->categoryRepository->getById($id);
                } catch (LocalizedException $e) {
                    $this->messageManager->addErrorMessage(__('This category no longer exists.'));

                    return $resultRedirect->setPath('*/*/');
                }
            } else {
                /** @var CategoryInterface $model */
                $model = $this->categoryFactory->create();
            }
            $model->setData($data);

            if (!$this->dataProcessor->validate($data)) {
                return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId(), '_current' => true]);
            }

            try {
                $this->categoryRepository->save($model);
                $this->messageManager->addSuccessMessage(__('Category was saved.'));
                $this->dataPersistor->clear('piuga_news_category');

                return $this->processReturn($model, $data, $resultRedirect);
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the news category.'));
            }
            $this->dataPersistor->set('piuga_news_category', $data);

            return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
        }

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Process and set the return
     *
     * @param CategoryInterface $model
     * @param array $data
     * @param ResultInterface $resultRedirect
     * @return ResultInterface
     * @throws LocalizedException
     */
    private function processReturn(CategoryInterface $model, array $data, ResultInterface $resultRedirect) : ResultInterface
    {
        $redirect = $data['back'] ?? 'close';

        if ($redirect ==='continue') {
            $resultRedirect->setPath('*/*/edit', ['id' => $model->getId()]);
        } else if ($redirect === 'close') {
            $resultRedirect->setPath('*/*/');
        } else if ($redirect === 'duplicate') {
            $duplicateModel = $this->categoryFactory->create(['data' => $data]);
            $duplicateModel->setId(null);
            $duplicateModel->setCreatedAt(null);
            $duplicateModel->setUrlKey($data[CategoryInterface::URL_KEY] . '-' . uniqid());
            $duplicateModel->setStatus(Category::STATUS_DISABLED);
            $this->categoryRepository->save($duplicateModel);

            $id = $duplicateModel->getId();
            $this->messageManager->addSuccessMessage(__('The news category was duplicated.'));
            $this->dataPersistor->set('piuga_news_category', $data);
            $resultRedirect->setPath('*/*/edit', ['id' => $id]);
        }

        return $resultRedirect;
    }

    /**
     * Check if URL key is empty, and if so convert title to URL key
     *
     * @param array $data
     * @return array
     */
    private function checkUrlKey(array $data) : array
    {
        if (
            isset($data[CategoryInterface::URL_KEY]) &&
            empty($data[CategoryInterface::URL_KEY]) &&
            isset($data[CategoryInterface::TITLE]) &&
            $data[CategoryInterface::TITLE]
        ) {
            $data[CategoryInterface::URL_KEY] = urlencode(strtolower(str_replace([' '], ['-'], $data[CategoryInterface::TITLE])));
        }

        return $data;
    }
}
