<?php
declare(strict_types=1);

namespace Piuga\News\Controller\Adminhtml\Category;

use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Stdlib\DateTime\Filter\Date;
use Piuga\News\Api\Data\CategoryInterface;

/**
 * Class PostDataProcessor
 * @package Piuga\News\Controller\Adminhtml\Category
 */
class PostDataProcessor
{
    /**
     * @var Date
     */
    protected $dateFilter;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * PostDataProcessor constructor.
     * @param Date $dateFilter
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        Date $dateFilter,
        ManagerInterface $messageManager
    ) {
        $this->dateFilter = $dateFilter;
        $this->messageManager = $messageManager;
    }

    /**
     * Filtering posted data. Converting localized data if needed
     *
     * @param array $data
     * @return array
     */
    public function filter(array $data) : array
    {
        // Convert stores array to string
        if (isset($data[CategoryInterface::STORES]) && !empty($data[CategoryInterface::STORES])) {
            $data[CategoryInterface::STORES] = implode(',', $data[CategoryInterface::STORES]);
        }
        $filterRules = [];

        return (new \Zend_Filter_Input($filterRules, [], $data))->getUnescaped();
    }

    /**
     * Check if required fields are not empty
     *
     * @param array $data
     * @return bool
     */
    public function validate(array $data) : bool
    {
        $requiredFields = [
            CategoryInterface::POSITION => __('Position'),
            CategoryInterface::STATUS => __('Status'),
            CategoryInterface::STORES => __('Store View'),
            CategoryInterface::TITLE => __('News Title'),
            CategoryInterface::URL_KEY => __('URL Key')
        ];
        $valid = true;
        foreach ($data as $field => $value) {
            if (in_array($field, array_keys($requiredFields)) && $value == '') {
                $valid = false;
                $this->messageManager->addErrorMessage(
                    __('To apply changes you should fill in required "%1" field(s)', $requiredFields[$field])
                );
            }
        }

        return $valid;
    }
}
