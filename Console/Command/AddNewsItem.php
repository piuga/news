<?php
declare(strict_types=1);

namespace Piuga\News\Console\Command;

use Magento\Framework\Console\Cli;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Piuga\News\Api\Data\NewsInterface;
use Piuga\News\Api\Data\NewsInterfaceFactory;
use Piuga\News\Api\NewsRepositoryInterface;

/**
 * Class AddNewsItem
 * @package Piuga\News\Console\Command
 */
class AddNewsItem extends Command
{
    /**
     * Names of input arguments or options
     */
    const INPUT_KEY_AUTHOR = 'author';
    const INPUT_KEY_CONTENT = 'content';
    const INPUT_KEY_META_DESCRIPTION = 'meta_description';
    const INPUT_KEY_META_KEYWORDS = 'meta_keywords';
    const INPUT_KEY_SHORT_CONTENT = 'short_content';
    const INPUT_KEY_TITLE = 'title';
    const INPUT_KEY_URL_KEY = 'url_key';
    const INPUT_KEY_STATUS = 'status';

    /**
     * @var NewsRepositoryInterface
     */
    protected $newsRepository;

    /**
     * @var NewsInterface
     */
    protected $newsInterfaceFactory;

    /**
     * AddNewsItem constructor.
     * @param NewsRepositoryInterface $newsRepository
     * @param NewsInterfaceFactory $newsInterfaceFactory
     */
    public function __construct(
        NewsRepositoryInterface $newsRepository,
        NewsInterfaceFactory $newsInterfaceFactory
    ) {
        $this->newsRepository = $newsRepository;
        $this->newsInterfaceFactory = $newsInterfaceFactory;
        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('piuga:news:add')
            ->setDescription('This will create a news item.')
            ->addOption(
                self::INPUT_KEY_AUTHOR,
                null,
                InputOption::VALUE_REQUIRED,
                'News item author. Use \' or " to encapsulate content.'
            )->addOption(
                self::INPUT_KEY_CONTENT,
                null,
                InputOption::VALUE_OPTIONAL,
                'News item content. Use \' or " to encapsulate content.'
            )->addOption(
                self::INPUT_KEY_META_DESCRIPTION,
                null,
                InputOption::VALUE_OPTIONAL,
                'News item meta description. Use \' or " to encapsulate content.'
            )->addOption(
                self::INPUT_KEY_META_KEYWORDS,
                null,
                InputOption::VALUE_OPTIONAL,
                'News item meta keywords. Use \' or " to encapsulate content.'
            )->addOption(
                self::INPUT_KEY_SHORT_CONTENT,
                null,
                InputOption::VALUE_OPTIONAL,
                'News item short content. Use \' or " to encapsulate content.'
            )->addOption(
                self::INPUT_KEY_TITLE,
                null,
                InputOption::VALUE_REQUIRED,
                'News item title. Use \' or " to encapsulate content.'
            )->addOption(
                self::INPUT_KEY_URL_KEY,
                null,
                InputOption::VALUE_REQUIRED,
                'News item URL key'
            )->addOption(
                self::INPUT_KEY_STATUS,
                null,
                InputOption::VALUE_REQUIRED,
                'News item status [0|1]'
            );
        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Prepare a news object
        $news = $this->newsInterfaceFactory->create();

        if ($input->hasOption(self::INPUT_KEY_AUTHOR) && ($input->getOption(self::INPUT_KEY_AUTHOR) !== null)) {
            $news->setAuthor((string)$input->getOption(self::INPUT_KEY_AUTHOR));
        } else {
            $output->writeln('<error>Author should be provided.</error>');
            return Cli::RETURN_FAILURE;
        }

        if ($input->hasOption(self::INPUT_KEY_CONTENT) && ($input->getOption(self::INPUT_KEY_CONTENT) !== null)) {
            $news->setContent((string)$input->getOption(self::INPUT_KEY_CONTENT));
        }

        if ($input->hasOption(self::INPUT_KEY_META_DESCRIPTION) && ($input->getOption(self::INPUT_KEY_META_DESCRIPTION) !== null)) {
            $news->setMetaDescription((string)$input->getOption(self::INPUT_KEY_META_DESCRIPTION));
        }

        if ($input->hasOption(self::INPUT_KEY_META_KEYWORDS) && ($input->getOption(self::INPUT_KEY_META_KEYWORDS) !== null)) {
            $news->setMetaKeywords((string)$input->getOption(self::INPUT_KEY_META_KEYWORDS));
        }

        if ($input->hasOption(self::INPUT_KEY_SHORT_CONTENT) && ($input->getOption(self::INPUT_KEY_SHORT_CONTENT) !== null)) {
            $news->setShortContent((string)$input->getOption(self::INPUT_KEY_SHORT_CONTENT));
        }

        if ($input->hasOption(self::INPUT_KEY_TITLE) && ($input->getOption(self::INPUT_KEY_TITLE) !== null)) {
            $news->setTitle((string)$input->getOption(self::INPUT_KEY_TITLE));
        } else {
            $output->writeln('<error>Title should be provided.</error>');
            return Cli::RETURN_FAILURE;
        }

        if ($input->hasOption(self::INPUT_KEY_URL_KEY) && ($input->getOption(self::INPUT_KEY_URL_KEY) !== null)) {
            $news->setUrlKey((string)$input->getOption(self::INPUT_KEY_URL_KEY));
        } else {
            $output->writeln('<error>URL key should be provided.</error>');
            return Cli::RETURN_FAILURE;
        }

        if ($input->hasOption(self::INPUT_KEY_STATUS) && ($input->getOption(self::INPUT_KEY_STATUS) !== null)) {
            $news->setStatus((int)$input->getOption(self::INPUT_KEY_STATUS));
        } else {
            $output->writeln('<error>Status should be provided.</error>');
            return Cli::RETURN_FAILURE;
        }

        try {
            // Save news item
            $newsRepository = $this->newsRepository->save($news);
            $output->writeln('<info>News item was saved (ID: ' . $newsRepository->getId() . ')</info>');

            return Cli::RETURN_SUCCESS;
        } catch (\Exception $exception) {
            $output->writeln('<error>Unable to save news item.</error>');

            return Cli::RETURN_FAILURE;
        }
    }
}
