<?php
declare(strict_types=1);

namespace Piuga\News\Plugin\CategoryInterface;

use Magento\Cms\Model\Template\FilterProvider;
use Piuga\News\Api\Data\CategoryInterface;

/**
 * Class FilterContent
 * @package Piuga\News\Plugin\CategoryInterface
 */
class FilterContent
{
    /**
     * @var FilterProvider
     */
    protected $filterProvider;

    /**
     * FilterContent constructor.
     * @param FilterProvider $filterProvider
     */
    public function __construct(
        FilterProvider $filterProvider
    ) {
        $this->filterProvider = $filterProvider;
    }

    /**
     * Filter content - parse widgets
     *
     * @param CategoryInterface $category
     * @param string $result
     * @return string
     * @throws \Exception
     */
    public function afterGetContent(CategoryInterface $category, string $result) : string
    {
        return $this->filterProvider->getPageFilter()->filter($result);
    }
}
