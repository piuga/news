<?php
declare(strict_types=1);

namespace Piuga\News\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;


/**
 * Class AddSampleNews
 * @package Piuga\News\Setup\Patch\Data
 */
class AddSampleNews implements DataPatchInterface, PatchRevertableInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    protected $moduleDataSetup;

    /**
     * AddSampleNews constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $connection = $this->moduleDataSetup->getConnection();
        $connection->startSetup();

        $table = $this->moduleDataSetup->getTable('piuga_news_items');

        // Prepare sample data
        $content = '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut ' .
            'labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ' .
            'ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse ' .
            'cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa ' .
            'qui officia deserunt mollit anim id est laborum.</p><p>Sed ut perspiciatis unde omnis iste natus error ' .
            'sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore ' .
            'veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas ' .
            'sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi ' .
            'nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, ' .
            'sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem.</p>' .
            '<p>At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum ' .
            'deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, ' .
            'similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga.</p>';
        $shortContent = '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ' .
            'ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris ' .
            'nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse ' .
            'cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa ' .
            'qui officia deserunt mollit anim id est laborum.</p>';
        $data = [
            [
                'url_key' => 'lorem-ipsum',
                'title' => 'Lorem ipsum',
                'content' => $content,
                'short_content' => $shortContent,
                'author' => 'Cicero',
                'meta_description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit',
                'meta_keywords' => 'Lorem, ipsum, news',
                'publish_at' => '2017-04-03 00:00:00',
                'status' => 1
            ],
            [
                'url_key' => 'dolor',
                'title' => 'Dolor',
                'content' => $content,
                'short_content' => $shortContent,
                'author' => 'Petru Iuga',
                'meta_description' => 'Dolor, consectetur adipiscing elit',
                'meta_keywords' => 'dolor, news',
                'publish_at' => '2017-02-22 00:00:00',
                'status' => 1
            ],
            [
                'url_key' => 'at-vero-eos',
                'title' => 'At vero eos',
                'content' => $content,
                'short_content' => $shortContent,
                'author' => 'Cicero',
                'meta_description' => 'At vero eos, consectetur adipiscing elit',
                'meta_keywords' => 'At vero, eos, news',
                'publish_at' => '2017-04-17 00:00:00',
                'status' => 1
            ],
            [
                'url_key' => 'laborum',
                'title' => 'Laborum',
                'content' => $content,
                'short_content' => $shortContent,
                'author' => 'John',
                'meta_description' => 'Laborum, consectetur adipiscing elit',
                'meta_keywords' => 'laborum, news',
                'publish_at' => '2018-05-22 00:00:00',
                'status' => 1
            ],
            [
                'url_key' => 'dolorum-fuga',
                'title' => 'Dolorum Fuga',
                'content' => $content,
                'short_content' => $shortContent,
                'author' => 'Cicero',
                'meta_description' => 'Dolorum Fuga, consectetur adipiscing elit',
                'meta_keywords' => 'Dolorum, Fuga, news',
                'publish_at' => '2017-04-17 00:00:00',
                'status' => 0
            ]
        ];

        // Insert multiple rows
        $connection->insertMultiple($table, $data);

        // Insert single row
        $item = [
            'url_key' => 'consectetur',
            'title' => 'Consectetur',
            'content' => $content,
            'short_content' => $shortContent,
            'author' => 'Petru'
        ];
        $connection->insert($table, $item);

        $connection->endSetup();
    }

    /**
     * {@inheritdoc}
     */
    public function revert()
    {
        $connection = $this->moduleDataSetup->getConnection();
        $connection->startSetup();

        $table = $this->moduleDataSetup->getTable('piuga_news_items');
        $connection->delete(
            $table,
            ["url_key IN ('lorem-ipsum', 'dolor', 'at-vero-eos', 'laborum', 'dolorum-fuga', 'consectetur')"]
        );

        $connection->endSetup();
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}
