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

try {
    $table = $this->getConnection()
        ->newTable($this->getTable('oggetto_news/indexer_relation'))
        ->addColumn('relation_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'identity' => true,
            'nullable' => false,
            'primary' => true,
        ), 'Relation ID')
        ->addColumn('news_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'default' => '0',
        ), 'News ID')
        ->addColumn('category_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'default' => '0',
        ), 'News category ID')
        ->addColumn('url_path', Varien_Db_Ddl_Table::TYPE_TEXT, 255,
            array(), 'News url path')
        ->addForeignKey($this->getFkName('oggetto_news/indexer_relation', 'news_id', 'oggetto_news/news', 'entity_id'), 'news_id', $this->getTable('oggetto_news/news'), 'entity_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->addForeignKey($this->getFkName('oggetto_news/indexer_relation', 'category_id', 'oggetto_news/news', 'entity_id'), 'category_id', $this->getTable('oggetto_news/category'), 'entity_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->addIndex(
            $this->getIdxName(
                'oggetto_news/news_category',
                'url_path',
                Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
            ),
            'url_path',
            array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
        ->setComment('News - Сategory relation indexer');

    $this->getConnection()->createTable($table);
} catch (Exception $e) {
    Mage::logException($e);
}

$this->endSetup();
