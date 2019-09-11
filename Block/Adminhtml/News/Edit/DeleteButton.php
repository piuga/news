<?php
declare(strict_types=1);

namespace Piuga\News\Block\Adminhtml\News\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class DeleteButton
 * @package Piuga\News\Block\Adminhtml\News\Edit
 */
class DeleteButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        $data = [];
        if ($this->getNewsId()) {
            $data = [
                'label' => __('Delete News'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . __(
                        'Are you sure you want to do this?'
                    ) . '\', \'' . $this->getDeleteUrl() . '\')',
                'sort_order' => 30,
            ];
        }

        return $data;
    }

    /**
     * Get URL for delete button
     *
     * @return string
     */
    public function getDeleteUrl() : string
    {
        return $this->getUrl('*/*/delete', ['id' => $this->getNewsId()]);
    }
}
