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
 * News model
 *
 * @category    Oggetto
 * @package     Oggetto_News
 */
class Oggetto_News_Model_News
    extends Mage_Core_Model_Abstract
{

    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY = 'oggetto_news_news';
    const CACHE_TAG = 'oggetto_news_news';

    /**
     * Prefix of model events names
     * @var string
     */
    protected $_eventPrefix = 'oggetto_news_news';

    /**
     * Parameter name in event
     * @var string
     */
    protected $_eventObject = 'news';
    protected $_categoryInstance = null;

    /**
     * Constructor
     *
     * @return Oggetto_News_Model_News
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('oggetto_news/news');
    }

    /**
     * Before save news
     *
     * @return Oggetto_News_Model_News
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        $now = Mage::getSingleton('core/date')->gmtDate();
        if ($this->isObjectNew()) {
            $this->setCreatedAt($now);
        }
        $this->setUpdatedAt($now);
        return $this;
    }

    /**
     * Get the url to the news details page
     *
     * @return string
     */
    public function getNewsUrl()
    {
        if ($urlKey = $this->getUrlKey()) {
            $dataHelper = Mage::helper('oggetto_news/data');
            $urlKey = $dataHelper->getNewsUrlPrefix() . $urlKey . $dataHelper->getNewsUrlSuffix();
            return Mage::getModel('core/url')->getDirectUrl($urlKey);
        }
        return Mage::getUrl('oggetto_news/news/view', array('id' => $this->getId()));
    }

    /**
     * Check URL key
     *
     * @param string $urlKey Url key
     * @return mixed
     */
    public function getIdByUrlKey($urlKey)
    {
        return $this->_getResource()->getIdByUrlKey($urlKey);
    }

    /**
     * Check URL path
     *
     * @param string $urlPath Url path
     * @return mixed
     */
    public function getIdByUrlPath($urlPath)
    {
        return $this->_getResource()->getIdByUrlPath($urlPath);
    }

    /**
     * Get the news Text
     *
     * @return string
     */
    public function getText()
    {
        $text = $this->getData('text');
        $helper = Mage::helper('cms');
        $processor = $helper->getBlockTemplateProcessor();
        $html = $processor->filter($text);
        return $html;
    }

    /**
     * Save news relation
     *
     * @return Oggetto_News_Model_News
     */
    protected function _afterSave()
    {
        $this->getCategoryInstance()->saveNewsRelation($this);
        return parent::_afterSave();
    }

    /**
     * Get category relation model
     *
     * @return Oggetto_News_Model_News_Category
     */
    public function getCategoryInstance()
    {
        if (!$this->_categoryInstance) {
            $this->_categoryInstance = Mage::getSingleton('oggetto_news/news_category');
        }
        return $this->_categoryInstance;
    }

    /**
     * Get selected categories array
     *
     * @return array
     */
    public function getSelectedCategories()
    {
        if (!$this->hasSelectedCategories()) {
            $categories = array();
            foreach ($this->getSelectedCategoriesCollection() as $category) {
                $categories[] = $category;
            }
            $this->setSelectedCategories($categories);
        }
        return $this->getData('selected_categories');
    }

    /**
     * Retrieve collection selected categories
     *
     * @return Oggetto_News_Model_Resource_News_Category_Collection
     */
    public function getSelectedCategoriesCollection()
    {
        $collection = $this->getCategoryInstance()->getCategoriesCollection($this);
        return $collection;
    }

    /**
     * Get default values
     *
     * @return array
     */
    public function getDefaultValues()
    {
        $values = array(
            'status' => 1
        );

        return $values;
    }
}
