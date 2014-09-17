<?php
/**
 * Oggetto Web extension for Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade
 * the Oggetto News module to newer versions in the future.
 * If you wish to customize the Oggetto News module for your needs
 * please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Oggetto
 * @package   Oggetto_News
 * @copyright Copyright (C) 2014, Oggetto Web (http://oggettoweb.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/**
 * News module install script
 *
 * @category    Oggetto
 * @package     Oggetto_News
 */
$this->startSetup();


$table = $this->getConnection()
    ->newTable($this->getTable('oggetto_news/news'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'nullable' => false,
        'primary' => true,
    ), 'News ID')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable' => false,
    ), 'Name')

    ->addColumn('text', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        'nullable' => false,
    ), 'Text')

    ->addColumn('url_key', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable' => false,
    ), 'Url key')

    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(), 'Enabled')

    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(), 'News Status')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'News Modification Time')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'News Creation Time')
    ->setComment('News Table');

$this->getConnection()->createTable($table);


$table = $this->getConnection()
    ->newTable($this->getTable('oggetto_news/category'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'nullable' => false,
        'primary' => true,
    ), 'News category ID')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable' => false,
    ), 'Name')

    ->addColumn('url_key', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable' => false,
    ), 'Url key')

    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(), 'Enabled')

    ->addColumn('parent_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
    ), 'Parent id')

    ->addColumn('path', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(), 'Path')

    ->addColumn('url_path', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(), 'Url path')

    ->addColumn('position', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
    ), 'Position')

    ->addColumn('level', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
    ), 'Level')

    ->addColumn('children_count', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
    ), 'Children count')

    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(), 'News category Status')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'News category Modification Time')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'News category Creation Time')
    ->setComment('News category Table');

$this->getConnection()->createTable($table);


$table = $this->getConnection()
    ->newTable($this->getTable('oggetto_news/news_category'))
    ->addColumn('rel_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'identity' => true,
        'nullable' => false,
        'primary' => true,
    ), 'Relation ID')
    ->addColumn('news_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'nullable' => false,
        'default' => '0',
    ), 'News ID')
    ->addColumn('category_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'nullable' => false,
        'default' => '0',
    ), 'News category ID')
    ->addColumn('position', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => false,
        'default' => '0',
    ), 'Position')
    ->addColumn('news_url_path', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(), 'News url path')
    ->addForeignKey($this->getFkName('oggetto_news/news_category', 'news_id', 'oggetto_news/news', 'entity_id'), 'news_id', $this->getTable('oggetto_news/news'), 'entity_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($this->getFkName('oggetto_news/news_category', 'category_id', 'oggetto_news/news', 'entity_id'), 'category_id', $this->getTable('oggetto_news/category'), 'entity_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addIndex(
        $this->getIdxName(
            'oggetto_news/news_category',
            array('news_id', 'category_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('news_id', 'category_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->setComment('News to News category Linkage Table');

$this->getConnection()->createTable($table);


$this->endSetup();
