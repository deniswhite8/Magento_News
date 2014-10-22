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
 * News collection resource model
 *
 * @category    Oggetto
 * @package     Oggetto_News
 */
class Oggetto_News_Model_Resource_News_Collection
    extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

    /** @var array $_joinedFields Joined fields */
    protected $_joinedFields = array();

    /**
     * Constructor
     *
     * @return Oggetto_News_Model_Resource_News_Collection
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('oggetto_news/news');
    }

    /**
     * Add is(s) filter
     *
     * @param int|array $id News id(s)
     * @return Oggetto_News_Model_Resource_News_Collection
     */
    public function addIdFilter($id)
    {
        $idFieldName = $this->getResource()->getIdFieldName();

        $filter = $id;
        if (is_array($id)) {
            $filter = ['in', $id];
        }

        $this->addFieldToFilter($idFieldName, $filter);
    }

    /**
     * Get news as array
     *
     * @param string $valueField Value field
     * @param string $labelField Label field
     * @param array $additional Additional
     *
     * @return array
     */
    protected function _toOptionArray($valueField = 'entity_id', $labelField = 'name', $additional = array())
    {
        return parent::_toOptionArray($valueField, $labelField, $additional);
    }

    /**
     * Get options hash
     *
     * @param string $valueField Value field
     * @param string $labelField Label field
     *
     * @return array
     */
    protected function _toOptionHash($valueField = 'entity_id', $labelField = 'name')
    {
        return parent::_toOptionHash($valueField, $labelField);
    }

    /**
     * Add the category filter to collection
     *
     * @param Oggetto_News_Model_Category|int $category Category
     * @return Oggetto_News_Model_Resource_News_Collection
     */
    public function addCategoryFilter($category)
    {
        if ($category instanceof Oggetto_News_Model_Category) {
            $category = $category->getId();
        }
        if (!isset($this->_joinedFields['category'])) {
            $this->getSelect()->join(
                array('related_category' => $this->getTable('oggetto_news/news_category')),
                'related_category.news_id = main_table.entity_id',
                array('position')
            );
            $this->getSelect()->where('related_category.category_id = ?', $category);
            $this->_joinedFields['category'] = true;
        }
        return $this;
    }

    /**
     * Get SQL for get record count.
     * Extra GROUP BY strip added.
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
