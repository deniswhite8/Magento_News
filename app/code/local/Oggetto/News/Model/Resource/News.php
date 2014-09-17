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
    extends Mage_Core_Model_Resource_Db_Abstract
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
     * Check url key
     *
     * @param string $urlKey Url key
     * @param int $storeId Store id
     * @param bool $active Active
     *
     * @return mixed
     */
    public function checkUrlKey($urlKey, $storeId, $active = true)
    {
        $stores = array(Mage_Core_Model_App::ADMIN_STORE_ID, $storeId);
        $select = $this->_initCheckUrlKeySelect($urlKey, $stores);
        if ($active) {
            $select->where('e.status = ?', $active);
        }
        $select->reset(Zend_Db_Select::COLUMNS)
            ->columns('e.entity_id')
            ->limit(1);

        return $this->_getReadAdapter()->fetchOne($select);
    }

    /**
     * Check url path
     *
     * @param string $urlPath Url path
     * @param int $storeId Store id
     * @param bool $active Active
     *
     * @return mixed
     */
    public function checkUrlPath($urlPath, $storeId, $active = true)
    {
        $stores = array(Mage_Core_Model_App::ADMIN_STORE_ID, $storeId);
        $select = $this->_initCheckUrlPathSelect($urlPath, $stores);
        if ($active) {
//            $select->where('e.news_status = ?', $active);
        }
        $select->reset(Zend_Db_Select::COLUMNS)
            ->columns('e.news_id')
            ->limit(1);

        return $this->_getReadAdapter()->fetchOne($select);
    }

    /**
     * Get first url path
     *
     * @param int $storeId Store id
     * @param bool $active Active
     *
     * @return string
     */
    public function getFirstUrlPath($newsId, $storeId, $active = true)
    {
        $stores = array(Mage_Core_Model_App::ADMIN_STORE_ID, $storeId);
        $select = $this->_initGetUrlPathSelect($newsId, $stores);

        $select->reset(Zend_Db_Select::COLUMNS)
            ->columns('e.news_url_path')
            ->limit(1);

        return $this->_getReadAdapter()->fetchOne($select);
    }

    /**
     * Check for unique URL key
     *
     * @param Mage_Core_Model_Abstract $object Object
     * @return bool
     */
    public function getIsUniqueUrlKey(Mage_Core_Model_Abstract $object)
    {
        if (Mage::app()->isSingleStoreMode() || !$object->hasStores()) {
            $stores = array(Mage_Core_Model_App::ADMIN_STORE_ID);
        } else {
            $stores = (array)$object->getData('stores');
        }
        $select = $this->_initCheckUrlKeySelect($object->getData('url_key'), $stores);
        if ($object->getId()) {
            $select->where('e.entity_id <> ?', $object->getId());
        }
        if ($this->_getWriteAdapter()->fetchRow($select)) {
            return false;
        }
        return true;
    }

    /**
     * Check if the URL key is numeric
     *
     * @param Mage_Core_Model_Abstract $object Object
     * @return bool
     */
    protected function isNumericUrlKey(Mage_Core_Model_Abstract $object)
    {
        return preg_match('/^[0-9]+$/', $object->getData('url_key'));
    }

    /**
     * Check if the URL key is valid
     *
     * @param Mage_Core_Model_Abstract $object Object
     * @return bool
     */
    protected function isValidUrlKey(Mage_Core_Model_Abstract $object)
    {
        return preg_match('/^[a-z0-9][a-z0-9_\/-]+(\.[a-z0-9_-]+)?$/', $object->getData('url_key'));
    }

    /**
     * Format string as url key
     *
     * @param string $str Url
     * @return string
     */
    public function formatUrlKey($str)
    {
        $urlKey = preg_replace('#[^0-9a-z]+#i', '-', Mage::helper('catalog/product_url')->format($str));
        $urlKey = strtolower($urlKey);
        $urlKey = trim($urlKey, '-');
        return $urlKey;
    }

    /**
     * Init the check select
     *
     * @param string $urlKey Url key
     * @param array $store Store
     *
     * @return Zend_Db_Select
     */
    protected function _initCheckUrlKeySelect($urlKey, $store)
    {
        $select = $this->_getReadAdapter()->select()
            ->from(array('e' => $this->getMainTable()))
            ->where('e.url_key = ?', $urlKey);
        return $select;
    }

    /**
     * Init the check select
     *
     * @param string $urlPath Url path
     * @param array  $store Store
     *
     * @return Zend_Db_Select
     */
    protected function _initCheckUrlPathSelect($urlPath, $store)
    {
        $select = $this->_getReadAdapter()->select()
            ->from(array('e' => $this->getTable('oggetto_news/news_category')))
            ->where('e.news_url_path = ?', $urlPath);

        return $select;
    }

    /**
     * Init the check select
     *
     * @param int   $newsId News id
     * @param array $store  Store
     *
     * @return Zend_Db_Select
     */
    protected function _initGetUrlPathSelect($newsId, $store)
    {
        $select = $this->_getReadAdapter()->select()
            ->from(array('e' => $this->getTable('oggetto_news/news_category')))
            ->where('e.news_id = ?', $newsId);

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
