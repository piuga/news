<?php
namespace Piuga\News\Controller;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
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
use Piuga\News\Api\NewsRepositoryInterface;

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
     * Router constructor.
     * @param ActionFactory $actionFactory
     * @param ManagerInterface $eventManager
     * @param NewsRepositoryInterface $newsRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param FilterGroupBuilder $filterGroupBuilder
     * @param StoreManagerInterface $storeManager
     * @param ResponseInterface $response
     */
    public function __construct(
        ActionFactory $actionFactory,
        ManagerInterface $eventManager,
        NewsRepositoryInterface $newsRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        FilterGroupBuilder $filterGroupBuilder,
        StoreManagerInterface $storeManager,
        ResponseInterface $response
    ) {
        $this->actionFactory = $actionFactory;
        $this->eventManager = $eventManager;
        $this->newsRepository = $newsRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->filterGroupBuilder = $filterGroupBuilder;
        $this->storeManager = $storeManager;
        $this->response = $response;
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

        $identifierParts = explode('/', $identifier);

        // Check if 'news' is the first identifier part and there is a 2nd part for news item URL key
        if (
            !is_array($identifierParts) ||
            count($identifierParts) < 2 ||
            $identifierParts[0] !== self::ROUTER_FRONT_NAME
        ) {
            // Not a news detail page request, continue
            return null;
        }

        // Get news item URL key as the 2nd identifier part
        $newsUrlKey = $identifierParts[1];
        // Get current store ID and prepare stores array for filter
        $currentStoreId = $this->storeManager->getStore()->getId();
        $stores = [Store::DEFAULT_STORE_ID, $currentStoreId];

        // Prepare filters
        $filters = [
            // Active status filter
            $this->filterBuilder->setConditionType('eq')->setField('status')->setValue(1)->create(),
            // Publish date filter - publish date is older or equal to today
            $this->filterBuilder->setConditionType('lteq')->setField('publish_at')->setValue(date('Y-m-d H:i:s'))->create(),
            // News stores contains admin (0) or current store ID
            $this->filterBuilder->setConditionType('in')->setField('stores')->setValue($stores)->create(),
            // URL key filter
            $this->filterBuilder->setConditionType('eq')->setField('url_key')->setValue($newsUrlKey)->create()
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
        $request->setAlias(Url::REWRITE_REQUEST_PATH_ALIAS, $identifier);

        return $this->actionFactory->create(Forward::class);
    }
}
