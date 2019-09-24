<?php
declare(strict_types=1);

namespace Piuga\News\Console\Command;

use Magento\Framework\Console\Cli;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Piuga\News\Api\CategoryRepositoryInterface;

/**
 * Class GetNewsCategory
 * @package Piuga\News\Console\Command
 */
class GetNewsCategory extends Command
{
    /**
     * Names of input arguments or options
     */
    const INPUT_KEY_ID = 'id';

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * GetNewsCategory constructor.
     * @param CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(
        CategoryRepositoryInterface $categoryRepository
    ) {
        $this->categoryRepository = $categoryRepository;
        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('piuga:news:category:get')
            ->setDescription('This will get news category data by provided ID.')
            ->addArgument(
                self::INPUT_KEY_ID,
                InputArgument::REQUIRED,
                'ID of news category'
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
            // Load news category by ID
            $newsCategory = $this->categoryRepository->getById($id);

            $output->writeln('<info>'.
                sprintf(
                    "%s (%d)\n %s",
                    $newsCategory->getTitle(),
                    $newsCategory->getId(),
                    $newsCategory->getContent()
                ) .
                '</info>');

            return Cli::RETURN_SUCCESS;
        } catch (\Exception $exception) {
            $output->writeln('<error>Unable to load news category.</error>');

            return Cli::RETURN_FAILURE;
        }
    }
}
