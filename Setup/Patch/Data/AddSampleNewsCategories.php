<?php
declare(strict_types=1);

namespace Piuga\News\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;

/**
 * Class AddSampleNewsCategories
 * @package Piuga\News\Setup\Patch\Data
 */
class AddSampleNewsCategories implements DataPatchInterface, PatchRevertableInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    protected $moduleDataSetup;

    /**
     * AddSampleNewsCategories constructor.
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

        $table = $this->moduleDataSetup->getTable('piuga_news_categories');

        // Prepare sample data
        $content = '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ' .
            'ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris ' .
            'nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse ' .
            'cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa ' .
            'qui officia deserunt mollit anim id est laborum.</p>';
        $data = [
            [
                'url_key' => 'our-team',
                'title' => 'Our Team',
                'content' => $content,
                'meta_description' => 'News about our team',
                'meta_keywords' => 'Team, news',
                'status' => 1,
                'position' => 10
            ],
            [
                'url_key' => 'products',
                'title' => 'Products',
                'content' => $content,
                'meta_description' => 'News about our products',
                'meta_keywords' => 'Products, news',
                'status' => 1,
                'position' => 20
            ],
            [
                'url_key' => 'our-company',
                'title' => 'Our Company',
                'content' => $content,
                'meta_description' => 'News about our company',
                'meta_keywords' => 'Company, news',
                'status' => 1,
                'position' => 30
            ],
            [
                'url_key' => 'industry',
                'title' => 'Industry',
                'content' => $content,
                'meta_description' => 'News about industry',
                'meta_keywords' => 'Industry, news',
                'status' => 0,
                'position' => 40
            ]
        ];

        // Insert multiple rows
        $connection->insertMultiple($table, $data);

        $connection->endSetup();
    }

    /**
     * {@inheritdoc}
     */
    public function revert()
    {
        $connection = $this->moduleDataSetup->getConnection();
        $connection->startSetup();

        $table = $this->moduleDataSetup->getTable('piuga_news_categories');
        $connection->delete(
            $table,
            ["url_key IN ('our-team', 'products', 'our-company', 'industry')"]
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
