<?php
declare(strict_types=1);

namespace Piuga\News\Controller\Adminhtml\News;

use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Stdlib\DateTime\Filter\Date;
use Piuga\News\Api\Data\NewsInterface;

/**
 * Class PostDataProcessor
 * @package Piuga\News\Controller\Adminhtml\News
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
        if (isset($data[NewsInterface::STORES]) && !empty($data[NewsInterface::STORES])) {
            $data[NewsInterface::STORES] = implode(',', $data[NewsInterface::STORES]);
        }
        $filterRules = [];
        // Filter dates
        foreach ([NewsInterface::PUBLISH_AT] as $dateField) {
            if (isset($data[$dateField]) && !empty($data[$dateField])) {
                $filterRules[$dateField] = $this->dateFilter;
            }
        }

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
            NewsInterface::AUTHOR => __('Author'),
            NewsInterface::CONTENT => __('Content'),
            NewsInterface::SHORT_CONTENT => __('List Content'),
            NewsInterface::STATUS => __('Status'),
            NewsInterface::STORES => __('Store View'),
            NewsInterface::TITLE => __('News Title'),
            NewsInterface::URL_KEY => __('URL Key')
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
