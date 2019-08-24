<?php
declare(strict_types=1);

namespace Piuga\News\Model\ResourceModel\News;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Piuga\News\Api\Data\NewsInterface;
use Piuga\News\Model\News;
use Piuga\News\Model\ResourceModel\News as NewsResource;

/**
 * Class Collection
 * @package Piuga\News\Model\ResourceModel\News
 */
class Collection extends AbstractCollection
{
    /**
     * ID Field name
     *
     * @var string
     */
    protected $_idFieldName = NewsInterface::NEWS_ID;

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'piuga_news_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'news_collection';

    /**
     * Define collection model and resource model
     */
    protected function _construct()
    {
        $this->_init(
            News::class,
            NewsResource::class
        );
    }
}
