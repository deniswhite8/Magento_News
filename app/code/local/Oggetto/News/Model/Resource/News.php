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
 * News resource model
 *
 * @category    Oggetto
 * @package     Oggetto_News
 */
class Oggetto_News_Model_Resource_News
    extends Oggetto_News_Model_Resource_Entity
{

    /**
     * Constructor
     *
     * @return Oggetto_News_Model_Resource_News
     */
    public function _construct()
    {
        $this->_init('oggetto_news/news', 'entity_id');
    }

    /**
     * Get id by url key
     *
     * @param string $urlKey Url key
     * @return mixed
     */
    public function getIdByUrlKey($urlKey)
    {
        $select = $this->_initUrlKeySelect($urlKey);
        $select->reset(Zend_Db_Select::COLUMNS)
            ->columns('e.entity_id')
            ->limit(1);

        return $this->_getReadAdapter()->fetchOne($select);
    }

    /**
     * Get id by url path
     *
     * @param string $urlPath Url path
     * @return mixed
     */
    public function getIdByUrlPath($urlPath)
    {
        $select = $this->_initUrlPathSelect($urlPath);
        $select->reset(Zend_Db_Select::COLUMNS)
            ->columns('e.news_id')
            ->limit(1);

        return $this->_getReadAdapter()->fetchOne($select);
    }

    /**
     * Init the url key select
     *
     * @param string $urlKey Url key
     * @return Zend_Db_Select
     */
    protected function _initUrlKeySelect($urlKey)
    {
        $select = $this->_getReadAdapter()->select()
            ->from(array('e' => $this->getMainTable()))
            ->where('e.url_key = ?', $urlKey);
        return $select;
    }

    /**
     * Init the url path select
     *
     * @param string $urlPath Url path
     * @return Zend_Db_Select
     */
    protected function _initUrlPathSelect($urlPath)
    {
        $select = $this->_getReadAdapter()->select()
            ->from(array('e' => $this->getTable('oggetto_news/news_category')))
            ->where('e.news_url_path = ?', $urlPath);

        return $select;
    }

    /**
     * Validate before saving
     *
     * @param Mage_Core_Model_Abstract $object Object
     * @return Oggetto_News_Model_Resource_News
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        $urlKey = $object->getData('url_key');
        if ($urlKey == '') {
            $urlKey = $object->getName();
        }
        $urlKey = $this->formatUrlKey($urlKey);
//        $validKey = false;
//        while (!$validKey) {
//            $entityId = $this->checkUrlKey($urlKey, $object->getStoreId(), false);
//            if ($entityId == $object->getId() || empty($entityId)) {
//                $validKey = true;
//            }
//            else {
//                $parts = explode('-', $urlKey);
//                $last = $parts[count($parts) - 1];
//                if (!is_numeric($last)){
//                    $urlKey = $urlKey.'-1';
//                }
//                else {
//                    $suffix = '-'.($last + 1);
//                    unset($parts[count($parts) - 1]);
//                    $urlKey = implode('-', $parts).$suffix;
//                }
//            }
//        }
        $object->setData('url_key', $urlKey);

        Mage::getResourceModel('oggetto_news/category_news')->saveNewsUrlPath($object);

        return parent::_beforeSave($object);
    }
}
