<?php
declare(strict_types=1);

namespace Piuga\News\Block\Adminhtml\System\Config;

use Magento\Backend\Block\Template\Context;
use Magento\Cms\Model\Wysiwyg\Config;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class Editor
 * @package Piuga\News\Block\Adminhtml\System\Config
 */
class Editor extends Field
{
    /**
     * @var Config
     */
    protected $wysiwygConfig;

    /**
     * Editor constructor.
     * @param Context $context
     * @param Config $wysiwygConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        Config $wysiwygConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->wysiwygConfig = $wysiwygConfig;
    }

    /**
     * Set WYSIWYG to store config field element
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $element->setWysiwyg(true);
        $element->setConfig($this->wysiwygConfig->getConfig($element));

        return parent::_getElementHtml($element);
    }
}
