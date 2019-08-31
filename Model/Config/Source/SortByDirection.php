<?php
declare(strict_types=1);

namespace Piuga\News\Model\Config\Source;

use Magento\Framework\Api\SortOrder;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class SortByDirection
 * @package Piuga\News\Model\Config\Source
 */
class SortByDirection implements OptionSourceInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            ['value' => SortOrder::SORT_ASC, 'label' => __('Ascending')],
            ['value' => SortOrder::SORT_DESC, 'label' => __('Descending')]
        ];
    }
}
