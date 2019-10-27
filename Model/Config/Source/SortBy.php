<?php
declare(strict_types=1);

namespace Piuga\News\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Piuga\News\Api\Data\NewsInterface;

/**
 * Class SortBy
 * @package Piuga\News\Model\Config\Source
 */
class SortBy implements OptionSourceInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            ['value' => NewsInterface::AUTHOR, 'label' => __('Author')],
            ['value' => NewsInterface::CREATED_AT, 'label' => __('Created At')],
            ['value' => 'position', 'label' => __('Position')],
            ['value' => NewsInterface::PUBLISH_AT, 'label' => __('Publish At')],
            ['value' => NewsInterface::TITLE, 'label' => __('Title')],
            ['value' => NewsInterface::UPDATED_AT, 'label' => __('Updated At')],
            ['value' => NewsInterface::URL_KEY, 'label' => __('URL Key')]
        ];
    }
}
