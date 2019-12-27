<?php
declare(strict_types=1);

namespace Piuga\News\Plugin\NewsInterface;

use Magento\Cms\Model\Template\FilterProvider;
use Piuga\News\Api\Data\NewsInterface;

/**
 * Class FilterContent
 * @package Piuga\News\Plugin\NewsInterface
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
     * @param NewsInterface $news
     * @param null|string $result
     * @return null|string
     * @throws \Exception
     */
    public function afterGetContent(NewsInterface $news, ?string $result) : ?string
    {
        return $result ? $this->filterProvider->getPageFilter()->filter($result) : $result;
    }

    /**
     * Filter short content - parse widgets
     *
     * @param NewsInterface $news
     * @param null|string $result
     * @return null|string
     * @throws \Exception
     */
    public function afterGetShortContent(NewsInterface $news, ?string $result) : ?string
    {
        return $result ? $this->filterProvider->getPageFilter()->filter($result) : $result;
    }
}
