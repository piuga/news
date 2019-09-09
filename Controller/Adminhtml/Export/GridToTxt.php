<?php
declare(strict_types=1);

namespace Piuga\News\Controller\Adminhtml\Export;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseInterface;
use Piuga\News\Model\Export\ConvertToTxt;

/**
 * Class GridToTxt
 * @package Piuga\News\Controller\Adminhtml\Export
 */
class GridToTxt extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Piuga_News::news';

    /**
     * @var ConvertToTxt
     */
    protected $converter;

    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * GridToTxt constructor.
     * @param Context $context
     * @param ConvertToTxt $converter
     * @param FileFactory $fileFactory
     */
    public function __construct(
        Context $context,
        ConvertToTxt $converter,
        FileFactory $fileFactory
    ) {
        parent::__construct($context);
        $this->converter = $converter;
        $this->fileFactory = $fileFactory;
    }

    /**
     * Export data provider to TXT
     *
     * @return ResponseInterface
     * @throws \Exception
     */
    public function execute() : ResponseInterface
    {
        return $this->fileFactory->create('export.txt', $this->converter->getTxtFile(), 'var');
    }
}
