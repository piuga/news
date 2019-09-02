<?php
declare(strict_types=1);

namespace Piuga\News\Ui\Component\Listing\Column;

use Magento\Store\Ui\Component\Listing\Column\Store as StoreColumn;

/**
 * Class Store
 * @package Piuga\News\Ui\Component\Listing\Column
 */
class Store extends StoreColumn
{
    /**
     * @var string
     */
    protected $storesKey = 'stores';

    /**
     * Get stores data
     *
     * @param array $item
     * @return string
     */
    protected function prepareItem(array $item)
    {
        $content = '';
        if (!empty($item[$this->storesKey])) {
            $origStores = explode(',', $item[$this->storesKey]);
        }
        if (empty($origStores)) {
            return __('All Store Views');
        }
        if (!is_array($origStores)) {
            $origStores = [$origStores];
        }
        if (in_array(0, $origStores) && count($origStores) == 1) {
            return __('All Store Views');
        }

        $data = $this->systemStore->getStoresStructure(false, $origStores);

        foreach ($data as $website) {
            $content .= $website['label'] . "<br/>";
            foreach ($website['children'] as $group) {
                $content .= str_repeat('&nbsp;', 3) . $this->escaper->escapeHtml($group['label']) . "<br/>";
                foreach ($group['children'] as $store) {
                    $content .= str_repeat('&nbsp;', 6) . $this->escaper->escapeHtml($store['label']) . "<br/>";
                }
            }
        }

        return $content;
    }
}
