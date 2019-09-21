<?php
declare(strict_types=1);

namespace Piuga\News\Controller\File;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Response\Http as HttpResponse;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\HTTP\Mime;
use Magento\Framework\Session\SessionManagerInterface;
use Piuga\News\Helper\NewsItem;
use Piuga\News\Model\FileInfo;

/**
 * Class Download
 * @package Piuga\News\Controller\File
 */
class Download extends Action
{
    /**
     * @var NewsItem
     */
    protected $newsHelper;

    /**
     * @var FileInfo
     */
    protected $fileInfo;

    /**
     * @var SessionManagerInterface
     */
    protected $session;

    /**
     * Download constructor.
     * @param Context $context
     * @param SessionManagerInterface $session
     * @param NewsItem $newsHelper
     * @param FileInfo $fileInfo
     */
    public function __construct(
        Context $context,
        SessionManagerInterface $session,
        NewsItem $newsHelper,
        FileInfo $fileInfo
    ) {
        parent::__construct($context);
        $this->newsHelper = $newsHelper;
        $this->session = $session;
        $this->fileInfo = $fileInfo;
    }

    /**
     * Download news file action
     *
     * @return ResultInterface|void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $newsItem = $this->newsHelper->getNewsItem();

        // If news cannot be seen or module is disabled, then return
        if (!$newsItem || !$this->newsHelper->isActive() || !$newsItem->getFile()) {
            return;
        }

        $file = $newsItem->getFile();
        if (!$this->fileInfo->isExist($file)) {
            return;
        }

        $contentType = $this->fileInfo->getMimeType($file);
        $stat = $this->fileInfo->getStat($file);

        /** @var HttpResponse $response */
        $response = $this->getResponse();
        $response
            ->setHttpResponseCode(200)
            ->setHeader('Pragma', 'public', true)
            ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
            ->setHeader('Content-type', $contentType, true);

        if ($stat && isset($stat['size'])) {
            $response->setHeader('Content-Length', $stat['size']);
        }

        // For security reasons we force browsers to download the file instead of opening it.
        $contentDisposition = Mime::DISPOSITION_ATTACHMENT;
        $response->setHeader('Content-Disposition', $contentDisposition  . '; filename=' . basename($file));

        //Rendering
        $response->clearBody();
        $response->sendHeaders();

        $this->output($file);
    }

    /**
     * Output file contents
     *
     * @param string $file
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    protected function output(string $file)
    {
        $handle = $this->fileInfo->readFile($file);
        $this->session->writeClose();
        while (($buffer = $handle->read(1024)) == true) {
            echo $buffer;
        }
    }
}
