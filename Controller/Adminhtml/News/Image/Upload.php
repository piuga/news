<?php
declare(strict_types=1);

namespace Piuga\News\Controller\Adminhtml\News\Image;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Piuga\News\Api\Data\NewsInterface;
use Piuga\News\Model\FileUploader;

/**
 * Class Upload
 * @package Piuga\News\Controller\Adminhtml\News\Image
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
    protected $imageUploader;

    /**
     * Upload constructor.
     * @param Context $context
     * @param FileUploader $imageUploader
     */
    public function __construct(
        Context $context,
        FileUploader $imageUploader
    ) {
        parent::__construct($context);
        $this->imageUploader = $imageUploader;
    }

    /**
     * Upload image controller action
     *
     * @return ResultInterface
     */
    public function execute() : ResultInterface
    {
        $imageId = $this->_request->getParam('param_name', NewsInterface::IMAGE);

        try {
            $result = $this->imageUploader->saveFileToTmpDir($imageId);
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }

        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
