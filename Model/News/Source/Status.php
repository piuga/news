<?php
declare(strict_types=1);

namespace Piuga\News\Model\News\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Piuga\News\Model\News;

/**
 * Class Status
 * @package Piuga\News\Model\News\Source
 */
class Status implements OptionSourceInterface
{
    /**
     * Get status options
     *
     * @return array
     */
    public function toOptionArray() : array
    {
        $options = [
            [
                'value' => News::STATUS_ENABLED,
                'label' => __('Enabled')
            ],
            [
                'value' => News::STATUS_DISABLED,
                'label' => __('Disabled')
            ]
        ];

        return $options;
    }
}
