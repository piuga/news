<?php
declare(strict_types=1);

namespace Piuga\News\Controller\Adminhtml\News\File;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Piuga\News\Api\Data\NewsInterface;
use Piuga\News\Model\FileUploader;

/**
 * Class Upload
 * @package Piuga\News\Controller\Adminhtml\News\File
 */
class Upload extends Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Piuga_News::edit_item';

    /**
     * @var FileUploader
     */
    protected $fileUploader;

    /**
     * Upload constructor.
     * @param Context $context
     * @param FileUploader $fileUploader
     */
    public function __construct(
        Context $context,
        FileUploader $fileUploader
    ) {
        parent::__construct($context);
        $this->fileUploader = $fileUploader;
    }

    /**
     * Upload file controller action
     *
     * @return ResultInterface
     */
    public function execute() : ResultInterface
    {
        $fileId = $this->_request->getParam('param_name', NewsInterface::FILE);

        try {
            $result = $this->fileUploader->saveFileToTmpDir($fileId);
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }

        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
