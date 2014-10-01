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
 * News category collection resource model
 *
 * @category    Oggetto
 * @package     Oggetto_News
 */
class Oggetto_News_Model_Resource_Category_Collection
    extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

    /** @var array $_joinedFields Joined fields */
    protected $_joinedFields = array();

    /**
     * Constructor
     *
     * @return Oggetto_News_Model_Resource_Category_Collection
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('oggetto_news/category');
    }

    /**
     * Add Id filter
     *
     * @param array $categoryIds Category ids
     * @return Oggetto_News_Model_Resource_Category_Collection
     */
    public function addIdFilter($categoryIds)
    {
        if (is_array($categoryIds)) {
            if (empty($categoryIds)) {
                $condition = '';
            } else {
                $condition = array('in' => $categoryIds);
            }
        } elseif (is_numeric($categoryIds)) {
            $condition = $categoryIds;
        } elseif (is_string($categoryIds)) {
            $ids = explode(',', $categoryIds);
            if (empty($ids)) {
                $condition = $categoryIds;
            } else {
                $condition = array('in' => $ids);
            }
        }
        $this->addFieldToFilter('entity_id', $condition);
        return $this;
    }

    /**
     * Add news category path filter
     *
     * @param string $regexp Regexp
     * @return Oggetto_News_Model_Resource_Category_Collection
     */
    public function addPathFilter($regexp)
    {
        $this->addFieldToFilter('path', array('regexp' => $regexp));
        return $this;
    }

    /**
     * Add news category path filter
     *
     * @param array|string $paths Paths
     * @return Oggetto_News_Model_Resource_Category_Collection
     */
    public function addPathsFilter($paths)
    {
        if (!is_array($paths)) {
            $paths = array($paths);
        }
        $write = $this->getResource()->getWriteConnection();
        $cond = array();
        foreach ($paths as $path) {
            $cond[] = $write->quoteInto('e.path LIKE ?', "$path%");
        }
        if ($cond) {
            $this->getSelect()->where(join(' OR ', $cond));
        }
        return $this;
    }

    /**
     * Add news category level filter
     *
     * @param int|string $level Level
     * @return Oggetto_News_Model_Resource_Category_Collection
     */
    public function addLevelFilter($level)
    {
        $this->addFieldToFilter('level', array('lteq' => $level));
        return $this;
    }

    /**
     * Add root news category filter
     *
     * @return Oggetto_News_Model_Resource_Category_Collection
     */
    public function addRootLevelFilter()
    {
        $this->addFieldToFilter('path', array('neq' => '1'));
        $this->addLevelFilter(1);
        return $this;
    }

    /**
     * Add order field
     *
     * @param string $field Field
     * @return Oggetto_News_Model_Resource_Category_Collection
     */
    public function addOrderField($field)
    {
        $this->setOrder($field, self::SORT_ORDER_ASC);
        return $this;
    }

    /**
     * Add active news category filter
     *
     * @return Oggetto_News_Model_Resource_Category_Collection
     */
    public function addStatusFilter($status = 1)
    {
        $this->addFieldToFilter('status', $status);
        return $this;
    }

    /**
     * Get categories as array
     *
     * @param string $valueField Value field
     * @param string $labelField Label field
     * @param array $additional Additional
     *
     * @return array
     */
    protected function _toOptionArray($valueField = 'entity_id', $labelField = 'name', $additional = array())
    {
        $res = array();
        $additional['value'] = $valueField;
        $additional['label'] = $labelField;

        foreach ($this as $item) {
            if ($item->getId() == Mage::helper('oggetto_news/data')->getRootCategoryId()) {
                continue;
            }
            foreach ($additional as $code => $field) {
                $data[$code] = $item->getData($field);
            }
            $res[] = $data;
        }
        return $res;
    }

    /**
     * Get options hash
     *
     * @param string $valueField ValueField
     * @param string $labelField LabelField
     *
     * @return array
     */
    protected function _toOptionHash($valueField = 'entity_id', $labelField = 'name')
    {
        $res = array();
        foreach ($this as $item) {
            if ($item->getId() == Mage::helper('oggetto_news/data')->getRootCategoryId()) {
                continue;
            }
            $res[$item->getData($valueField)] = $item->getData($labelField);
        }
        return $res;
    }

    /**
     * Add the news filter to collection
     *
     * @param Oggetto_News_Model_News|int $news News
     * @return Oggetto_News_Model_Resource_Category_Collection
     */
    public function addNewsFilter($news)
    {
        if ($news instanceof Oggetto_News_Model_News) {
            $news = $news->getId();
        }
        if (!isset($this->_joinedFields['news'])) {
            $this->getSelect()->join(
                array('related_news' => $this->getTable('oggetto_news/category_news')),
                'related_news.category_id = main_table.entity_id',
                array('position')
            );
            $this->getSelect()->where('related_news.news_id = ?', $news);
            $this->_joinedFields['news'] = true;
        }
        return $this;
    }

    /**
     * Get SQL for get record count, extra GROUP BY strip added.
     *
     * @return Varien_Db_Select
     */
    public function getSelectCountSql()
    {
        $countSelect = parent::getSelectCountSql();
        $countSelect->reset(Zend_Db_Select::GROUP);
        return $countSelect;
    }
}
