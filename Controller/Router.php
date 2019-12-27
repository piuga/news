<?php
namespace Piuga\News\Controller;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\App\Action\Forward;
use Magento\Framework\App\Action\Redirect;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\RouterInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Url;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Piuga\News\Api\CategoryRepositoryInterface;
use Piuga\News\Api\Data\CategoryInterface;
use Piuga\News\Api\Data\NewsInterface;
use Piuga\News\Api\NewsRepositoryInterface;
use Piuga\News\Helper\NewsItem;

/**
 * Class Router
 * @package Piuga\News\Controller
 */
class Router implements RouterInterface 
{
    /**
     * Router frontend name entry point
     */
    const ROUTER_FRONT_NAME = 'news';

    /**
     * @var ActionFactory
     */
    protected $actionFactory;

    /**
     * Event manager
     *
     * @var ManagerInterface
     */
    protected $eventManager;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var NewsRepositoryInterface
     */
    protected $newsRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @var FilterGroupBuilder
     */
    protected $filterGroupBuilder;

    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * @var NewsItem
     */
    protected $newsHelper;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var SortOrder
     */
    protected $sortOrder;

    /**
     * Router constructor.
     * @param ActionFactory $actionFactory
     * @param ManagerInterface $eventManager
     * @param NewsRepositoryInterface $newsRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param FilterGroupBuilder $filterGroupBuilder
     * @param StoreManagerInterface $storeManager
     * @param ResponseInterface $response
     * @param NewsItem $newsHelper
     * @param CategoryRepositoryInterface $categoryRepository
     * @param SortOrder $sortOrder
     */
    public function __construct(
        ActionFactory $actionFactory,
        ManagerInterface $eventManager,
        NewsRepositoryInterface $newsRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        FilterGroupBuilder $filterGroupBuilder,
        StoreManagerInterface $storeManager,
        ResponseInterface $response,
        NewsItem $newsHelper,
        CategoryRepositoryInterface $categoryRepository,
        SortOrder $sortOrder
    ) {
        $this->actionFactory = $actionFactory;
        $this->eventManager = $eventManager;
        $this->newsRepository = $newsRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->filterGroupBuilder = $filterGroupBuilder;
        $this->storeManager = $storeManager;
        $this->response = $response;
        $this->newsHelper = $newsHelper;
        $this->categoryRepository = $categoryRepository;
        $this->sortOrder = $sortOrder;
    }

    /**
     * Validate and Match News Page and modify request
     *
     * @param RequestInterface $request
     * @return ActionInterface|null
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function match(RequestInterface $request) : ?ActionInterface
    {
        $identifier = trim($request->getPathInfo(), '/');
        $condition = new DataObject(['identifier' => $identifier, 'continue' => true]);
        $this->eventManager->dispatch(
            'piuga_news_controller_router_match_before',
            ['router' => $this, 'condition' => $condition]
        );

        // Check for system redirects
        $identifier = $condition->getIdentifier();
        if ($condition->getRedirectUrl()) {
            $this->response->setRedirect($condition->getRedirectUrl());
            $request->setDispatched(true);
            return $this->actionFactory->create(Redirect::class);
        }

        // Stop if another previous router already matched the current request
        if (!$condition->getContinue()) {
            return null;
        }

        // Stop if module is disabled
        if (!$this->newsHelper->isActive()) {
            return null;
        }

        $identifierParts = explode('/', $identifier);

        /** Check if URL is for a news page (list or detail) */
        $urlPrefix = $this->newsHelper->getNewsUrl();
        if(!is_array($identifierParts) || $identifierParts[0] !== $urlPrefix) {
            // Not a news detail page request, continue
            return null;
        }

        // Get current store ID and prepare stores array for filter
        $currentStoreId = $this->storeManager->getStore()->getId();
        $stores = [Store::DEFAULT_STORE_ID, $currentStoreId];

        // Check if category page
        $categoryFound = null;
        if (count($identifierParts) > 1) {
            // Get category item URL key as the 2nd identifier part
            $categoryUrlKey = $identifierParts[1];

            /** Get category item based on identifier */
            // Prepare filters
            $filters = [
                // Active status filter
                $this->filterBuilder->setConditionType('eq')->setField(CategoryInterface::STATUS)->setValue(1)->create(),
                // URL key check
                $this->filterBuilder->setConditionType('eq')->setField(CategoryInterface::URL_KEY)->setValue($categoryUrlKey)->create(),
                // Category stores contains admin (0) or current store ID
                $this->filterBuilder->setConditionType('in')->setField(CategoryInterface::STORES)->setValue($stores)->create()
            ];
            // Group filters for AND statement
            $filterGroups = [];
            foreach ($filters as $filter) {
                $filterGroups[] = $this->filterGroupBuilder->setFilters([$filter])->create();
            }
            // Apply filters
            $this->searchCriteriaBuilder->setFilterGroups($filterGroups);
            // Prepare sort order
            $orders = [
                // order by position: low number first
                $this->sortOrder->setField(CategoryInterface::POSITION)->setDirection(SortOrder::SORT_ASC)
            ];
            // Apply sort order
            $this->searchCriteriaBuilder->setSortOrders($orders);
            $categories = $this->categoryRepository->getList(
                $this->searchCriteriaBuilder->create()
            )->getItems();

            foreach ($categories as $item) {
                if ($item && $item->getId()) {
                    // Prepare request
                    $request->setModuleName('news')
                        ->setControllerName('category')
                        ->setActionName('view')
                        ->setParam('cat_id', $item->getId());
                    $request->setAlias(Url::REWRITE_REQUEST_PATH_ALIAS, $identifier);
                    $categoryFound = $item->getId();
                    break;
                }
            }
        }

        // Check if detail page
        $newsUrlKey = null;
        if ($categoryFound && isset($identifierParts[2])) {
            // Get news item URL key as the 3rd identifier part
            $newsUrlKey = $identifierParts[2];
        } elseif (!$categoryFound && isset($identifierParts[1])) {
            // Get news item URL key as the 2nd identifier part
            $newsUrlKey = $identifierParts[1];
        }

        if ($newsUrlKey) {
            // Prepare filters
            $filters = [
                // Active status filter
                $this->filterBuilder->setConditionType('eq')->setField(NewsInterface::STATUS)->setValue(1)->create(),
                // Publish date filter - publish date is older or equal to today
                $this->filterBuilder->setConditionType('lteq')->setField(NewsInterface::PUBLISH_AT)->setValue(date('Y-m-d H:i:s'))->create(),
                // News stores contains admin (0) or current store ID
                $this->filterBuilder->setConditionType('in')->setField(NewsInterface::STORES)->setValue($stores)->create(),
                // URL key filter
                $this->filterBuilder->setConditionType('eq')->setField(NewsInterface::URL_KEY)->setValue($newsUrlKey)->create()
            ];
            // Group filters for AND statement
            $filterGroups = [];
            foreach ($filters as $filter) {
                $filterGroups[] = $this->filterGroupBuilder->setFilters([$filter])->create();
            }
            // Apply filters
            $this->searchCriteriaBuilder->setFilterGroups($filterGroups);
            $news = $this->newsRepository
                ->getList($this->searchCriteriaBuilder->create())
                ->getItems();
            if (!$news || !count($news)) {
                return null;
            }

            // Get first array entry - should be only one, but it might be also multiple items
            $newsItem = array_shift($news);
            if (!$newsItem || !$newsItem->getId()) {
                return null;
            }

            // Prepare request
            $request->setModuleName(self::ROUTER_FRONT_NAME)
                ->setControllerName('item')
                ->setActionName('view')
                ->setParam('id', $newsItem->getId());
            if ($categoryFound) {
                $request->setParam('cat_id', $categoryFound);
            }
            $request->setAlias(Url::REWRITE_REQUEST_PATH_ALIAS, $identifier);

        } elseif (!$categoryFound) {
            // Forward to news list
            $request->setModuleName(self::ROUTER_FRONT_NAME)
                ->setControllerName('index')
                ->setActionName('index');
            $request->setAlias(Url::REWRITE_REQUEST_PATH_ALIAS, $identifier);
        }

        return $this->actionFactory->create(Forward::class);
    }
}
