<?php
declare(strict_types=1);

namespace Piuga\News\Console\Command;

use Magento\Framework\Console\Cli;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Piuga\News\Api\CategoryRepositoryInterface;
use Piuga\News\Api\Data\CategoryInterface;
use Piuga\News\Api\Data\CategoryInterfaceFactory;

/**
 * Class AddNewsCategory
 * @package Piuga\News\Console\Command
 */
class AddNewsCategory extends Command
{
    /**
     * Names of input arguments or options
     */
    const INPUT_KEY_CONTENT = 'content';
    const INPUT_KEY_META_DESCRIPTION = 'meta_description';
    const INPUT_KEY_META_KEYWORDS = 'meta_keywords';
    const INPUT_KEY_POSITION = 'position';
    const INPUT_KEY_TITLE = 'title';
    const INPUT_KEY_URL_KEY = 'url_key';
    const INPUT_KEY_STATUS = 'status';

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var CategoryInterface
     */
    protected $categoryInterfaceFactory;

    /**
     * AddNewsCategory constructor.
     * @param CategoryRepositoryInterface $categoryRepository
     * @param CategoryInterfaceFactory $categoryInterfaceFactory
     */
    public function __construct(
        CategoryRepositoryInterface $categoryRepository,
        CategoryInterfaceFactory $categoryInterfaceFactory
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->categoryInterfaceFactory = $categoryInterfaceFactory;
        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('piuga:news:category:add')
            ->setDescription('This will create a news category.')
            ->addOption(
                self::INPUT_KEY_CONTENT,
                null,
                InputOption::VALUE_OPTIONAL,
                'News category content. Use \' or " to encapsulate content.'
            )->addOption(
                self::INPUT_KEY_META_DESCRIPTION,
                null,
                InputOption::VALUE_OPTIONAL,
                'News category meta description. Use \' or " to encapsulate content.'
            )->addOption(
                self::INPUT_KEY_META_KEYWORDS,
                null,
                InputOption::VALUE_OPTIONAL,
                'News category meta keywords. Use \' or " to encapsulate content.'
            )->addOption(
                self::INPUT_KEY_POSITION,
                null,
                InputOption::VALUE_OPTIONAL,
                'News category position.'
            )->addOption(
                self::INPUT_KEY_TITLE,
                null,
                InputOption::VALUE_REQUIRED,
                'News category title. Use \' or " to encapsulate content.'
            )->addOption(
                self::INPUT_KEY_URL_KEY,
                null,
                InputOption::VALUE_REQUIRED,
                'News category URL key'
            )->addOption(
                self::INPUT_KEY_STATUS,
                null,
                InputOption::VALUE_REQUIRED,
                'News category status [0|1]'
            );
        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Prepare a news category object
        $category = $this->categoryInterfaceFactory->create();

        if ($input->hasOption(self::INPUT_KEY_CONTENT) && ($input->getOption(self::INPUT_KEY_CONTENT) !== null)) {
            $category->setContent((string)$input->getOption(self::INPUT_KEY_CONTENT));
        }

        if ($input->hasOption(self::INPUT_KEY_META_DESCRIPTION) && ($input->getOption(self::INPUT_KEY_META_DESCRIPTION) !== null)) {
            $category->setMetaDescription((string)$input->getOption(self::INPUT_KEY_META_DESCRIPTION));
        }

        if ($input->hasOption(self::INPUT_KEY_META_KEYWORDS) && ($input->getOption(self::INPUT_KEY_META_KEYWORDS) !== null)) {
            $category->setMetaKeywords((string)$input->getOption(self::INPUT_KEY_META_KEYWORDS));
        }

        if ($input->hasOption(self::INPUT_KEY_POSITION) && ($input->getOption(self::INPUT_KEY_POSITION) !== null)) {
            $category->setPosition((int)$input->getOption(self::INPUT_KEY_POSITION));
        }

        if ($input->hasOption(self::INPUT_KEY_TITLE) && ($input->getOption(self::INPUT_KEY_TITLE) !== null)) {
            $category->setTitle((string)$input->getOption(self::INPUT_KEY_TITLE));
        } else {
            $output->writeln('<error>Title should be provided.</error>');
            return Cli::RETURN_FAILURE;
        }

        if ($input->hasOption(self::INPUT_KEY_URL_KEY) && ($input->getOption(self::INPUT_KEY_URL_KEY) !== null)) {
            $category->setUrlKey((string)$input->getOption(self::INPUT_KEY_URL_KEY));
        } else {
            $output->writeln('<error>URL key should be provided.</error>');
            return Cli::RETURN_FAILURE;
        }

        if ($input->hasOption(self::INPUT_KEY_STATUS) && ($input->getOption(self::INPUT_KEY_STATUS) !== null)) {
            $category->setStatus((int)$input->getOption(self::INPUT_KEY_STATUS));
        } else {
            $output->writeln('<error>Status should be provided.</error>');
            return Cli::RETURN_FAILURE;
        }

        try {
            // Save news category
            $categoryRepository = $this->categoryRepository->save($category);
            $output->writeln('<info>News category was saved (ID: ' . $categoryRepository->getId() . ')</info>');

            return Cli::RETURN_SUCCESS;
        } catch (\Exception $exception) {
            $output->writeln('<error>Unable to save news category.</error>');

            return Cli::RETURN_FAILURE;
        }
    }
}
