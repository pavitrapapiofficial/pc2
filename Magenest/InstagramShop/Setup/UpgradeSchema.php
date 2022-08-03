<?php

namespace Magenest\InstagramShop\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class UpgradeSchema
 * @package Magenest\InstagramShop\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        //Install Table magenest_instagram_photo
        $table = $installer->getConnection()->newTable(
            $installer->getTable('magenest_instagram_photo')
        )->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true
            ],
            'Id'
        )->addColumn(
            'photo_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Instagram Photo Id'
        )->addColumn(
            'url',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Instagram Photo Url'
        )->addColumn(
            'source',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Instagram Photo Source Link'
        )->addColumn(
            'caption',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => true],
            'Instagram Photo Caption'
        )->addColumn(
            'product_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => true],
            'Created Product Id'
        )->addColumn(
            'likes',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => true],
            'Photo Likes'
        )->addColumn(
            'comments',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => true],
            'Photo Comments'
        )->setComment(
            'Instagram Photo on Store\'s Account'
        );
        $installer->getConnection()->createTable($table);

        //Install Table magenest_instagram_taggedphoto
        $table = $installer->getConnection()->newTable(
            $installer->getTable('magenest_instagram_taggedphoto')
        )->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true
            ],
            'Id'
        )->addColumn(
            'photo_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Instagram Photo Id'
        )->addColumn(
            'tag_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Instagram Photo\'s Tag Name'
        )->addColumn(
            'user',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Instagram Photos User'
        )->addColumn(
            'url',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Instagram Photo Url'
        )->addColumn(
            'source',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Instagram Photo Source Link'
        )->addColumn(
            'caption',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => true],
            'Instagram Photo Caption'
        )->addColumn(
            'min_tag_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => true],
            'Instagram Min Tag Id'
        )->addColumn(
            'likes',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => true],
            'Photo Likes'
        )->addColumn(
            'comments',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => true],
            'Photo Comments'
        )->setComment(
            'Instagram Tagged Photo'
        );

        if (version_compare($context->getVersion(), '2.0.2') < 0) {
            $this->addShowInWidgetColumn($installer);
        }
        if (version_compare($context->getVersion(), '2.0.3') < 0) {
            $this->addCreatedAtColumn($installer);
        }
        $installer->getConnection()->createTable($table);
        $installer->endSetup();
    }

    /**
     * @param SchemaSetupInterface $installer
     */
    private function addShowInWidgetColumn($installer)
    {
        $installer->getConnection()->addColumn(
            $installer->getTable('magenest_instagram_photo'),
            'show_in_widget',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'comment' => 'Show in widget',
                'default' => 1
            ]
        );
    }

    /**
     * @param SchemaSetupInterface $installer
     */
    private function addCreatedAtColumn($installer)
    {
        $installer->getConnection()->addColumn(
            $installer->getTable('magenest_instagram_photo'),
            'created_at',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                'comment' => 'Created At'
            ]
        );
    }
}
