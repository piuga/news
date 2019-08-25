<?php
declare(strict_types=1);

namespace Piuga\News\Console\Command;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Console\Cli;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Piuga\News\Api\Data\NewsInterface;
use Piuga\News\Api\NewsRepositoryInterface;

/**
 * Class GetNewsItem
 * @package Piuga\News\Console\Command
 */
class NewsItemStatus extends Command
{
    /**
     * Names of input arguments or options
     */
    const INPUT_KEY_IDS = 'ids';
    const INPUT_KEY_STATUS = 'status';

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
     * NewsItemStatus constructor.
     * @param NewsRepositoryInterface $newsRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param FilterGroupBuilder $filterGroupBuilder
     */
    public function __construct(
        NewsRepositoryInterface $newsRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        FilterGroupBuilder $filterGroupBuilder
    ) {
        $this->newsRepository = $newsRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->filterGroupBuilder = $filterGroupBuilder;
        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('piuga:news:status')
            ->setDescription('This will update news item(s) status. You can provided a list of IDs, ' .
                'or leave empty to update for all.')
            ->addArgument(
                self::INPUT_KEY_STATUS,
                InputArgument::REQUIRED,
                'Status of news item(s) to be set [0|1]'
            )
            ->addArgument(
                self::INPUT_KEY_IDS,
                InputArgument::IS_ARRAY,
                'ID(s) of news item(s)'
            );
        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $status = (int)$input->getArgument(self::INPUT_KEY_STATUS);
        $ids = $input->getArgument(self::INPUT_KEY_IDS);

        try {
            if ($ids) {
                // Prepare filter by IDs
                $filters[] = $this->filterBuilder
                    ->setField(NewsInterface::NEWS_ID)
                    ->setConditionType('in')
                    ->setValue($ids)
                    ->create();
                $this->searchCriteriaBuilder->addFilters($filters);
            }

            $news = $this->newsRepository
                ->getList($this->searchCriteriaBuilder->create())
                ->getItems();

            $progress = new ProgressBar($output, count($news));
            $progress->setFormat('<comment>%message%</comment> %current%/%max% [%bar%] %percent:3s%% %elapsed%');

            foreach ($news as $item) {
                $progress->setMessage('Processing ID: ' . $item->getId() . ' ');
                $item->setStatus($status)->save();
                sleep(1); // Added to show progress bar update
                $progress->advance();
            }

            $output->writeln("");
            $output->writeln("<info>Status was changed!</info>");

            return Cli::RETURN_SUCCESS;
        } catch (\Exception $exception) {
            $output->writeln('<error>Unable to change status for news item(s).</error>');

            return Cli::RETURN_FAILURE;
        }
    }
}
