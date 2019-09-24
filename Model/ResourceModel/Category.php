<?php
declare(strict_types=1);

namespace Piuga\News\Model\ResourceModel;

use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime;
use Piuga\News\Api\Data\CategoryInterface;

/**
 * Class Category
 * @package Piuga\News\Model\ResourceModel
 */
class Category extends AbstractDb
{
    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * Category constructor.
     * @param Context $context
     * @param DateTime $dateTime
     * @param EntityManager $entityManager
     * @param null $connectionName
     */
    public function __construct(
        Context $context,
        DateTime $dateTime,
        EntityManager $entityManager,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->dateTime = $dateTime;
        $this->entityManager = $entityManager;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('piuga_news_categories', CategoryInterface::CATEGORY_ID);
    }

    /**
     * Process data before saving
     *
     * @param AbstractModel $object
     * @return News
     * @throws LocalizedException
     */
    protected function _beforeSave(AbstractModel $object)
    {
        /*
         * For 2 attributes which represent timestamp data in DB we should make converting such as:
         * If they are empty we need to convert them into DB type NULL
         * so in DB they will be empty and not some default value
         */
        foreach ([CategoryInterface::CREATED_AT, CategoryInterface::UPDATED_AT] as $field) {
            $value = !$object->getData($field) ? null : $this->dateTime->formatDate($object->getData($field));
            $object->setData($field, $value);
        }

        if (!$this->isValidNewsUrl($object)) {
            throw new LocalizedException(
                __('The news category URL key contains capital letters or disallowed symbols.')
            );
        }

        if ($this->isNumericNewsUrl($object)) {
            throw new LocalizedException(
                __('The news category URL key cannot be made of only numbers.')
            );
        }

        return parent::_beforeSave($object);
    }

    /**
     * Check whether url_key is numeric
     *
     * @param AbstractModel $object
     * @return bool
     */
    protected function isNumericNewsUrl(AbstractModel $object) : bool
    {
        return (bool)preg_match('/^[0-9]+$/', $object->getData(CategoryInterface::URL_KEY));
    }

    /**
     * Check whether url_key is valid
     *
     * @param AbstractModel $object
     * @return bool
     */
    protected function isValidNewsUrl(AbstractModel $object) : bool
    {
        return (bool)preg_match('/^[a-z0-9][a-z0-9_\/-]+(\.[a-z0-9_-]+)?$/', $object->getData(CategoryInterface::URL_KEY));
    }
}
