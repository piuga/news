<?php
declare(strict_types=1);

namespace Piuga\News\Model\ResourceModel\Category;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Piuga\News\Api\Data\CategoryInterface;
use Piuga\News\Model\Category;
use Piuga\News\Model\ResourceModel\Category as CategoryResource;

/**
 * Class Collection
 * @package Piuga\News\Model\ResourceModel\Category
 */
class Collection extends AbstractCollection
{
    /**
     * ID Field name
     *
     * @var string
     */
    protected $_idFieldName = CategoryInterface::CATEGORY_ID;

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'piuga_news_category_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'news_category_collection';

    /**
     * Define collection model and resource model
     */
    protected function _construct()
    {
        $this->_init(
            Category::class,
            CategoryResource::class
        );
    }
}
