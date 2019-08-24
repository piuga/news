<?php
declare(strict_types=1);

namespace Piuga\News\Console\Command;

use Magento\Framework\Console\Cli;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Piuga\News\Api\NewsRepositoryInterface;

/**
 * Class GetNewsItem
 * @package Piuga\News\Console\Command
 */
class GetNewsItem extends Command
{
    /**
     * Names of input arguments or options
     */
    const INPUT_KEY_ID = 'id';

    /**
     * @var NewsRepositoryInterface
     */
    protected $newsRepository;

    /**
     * GetNewsItem constructor.
     * @param NewsRepositoryInterface $newsRepository
     */
    public function __construct(
        NewsRepositoryInterface $newsRepository
    ) {
        $this->newsRepository = $newsRepository;
        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('piuga:news:get')
            ->setDescription('This will get news item data by provided ID.')
            ->addArgument(
                self::INPUT_KEY_ID,
                InputArgument::REQUIRED,
                'ID of news item'
            );
        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $id = (int)$input->getArgument(self::INPUT_KEY_ID);

        try {
            // Load news item by ID
            $newsItem = $this->newsRepository->getById($id);

            $output->writeln('<info>'.
                sprintf(
                    "%s (%d)\n %s\n\n %s (%s)",
                    $newsItem->getTitle(),
                    $newsItem->getId(),
                    $newsItem->getShortContent(),
                    $newsItem->getAuthor(),
                    $newsItem->getPublishAt()
                ) .
                '</info>');

            return Cli::RETURN_SUCCESS;
        } catch (\Exception $exception) {
            $output->writeln('<error>Unable to load news item.</error>');

            return Cli::RETURN_FAILURE;
        }
    }
}
