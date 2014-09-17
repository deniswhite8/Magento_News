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
 * category - news relation edit block
 *
 * @category    Oggetto
 * @package     Oggetto_News
 */
class Oggetto_News_Block_Adminhtml_Category_Edit_Tab_News
    extends Mage_Adminhtml_Block_Widget_Grid
{

    /**
     * Set grid params
     *
     * @return Oggetto_News_Block_Adminhtml_Category_Edit_Tab_News
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('news_grid');
        $this->setDefaultSort('position');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        if ($this->getCategory()->getId()) {
            $this->setDefaultFilter(array('in_news' => 1));
        }
    }

    /**
     * Prepare the news collection
     *
     * @return Oggetto_News_Block_Adminhtml_Category_Edit_Tab_News
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('oggetto_news/news_collection');
        if ($this->getCategory()->getId()) {
            $constraint = 'related.category_id=' . $this->getCategory()->getId();
        } else {
            $constraint = 'related.category_id=0';
        }
        $collection->getSelect()->joinLeft(
            array('related' => $collection->getTable('oggetto_news/category_news')),
            'related.news_id=main_table.entity_id AND ' . $constraint,
            array('position'));
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    /**
     * Prepare mass action grid
     *
     * @return Oggetto_News_Block_Adminhtml_Category_Edit_Tab_News
     */
    protected function _prepareMassaction()
    {
        return $this;
    }

    /**
     * Prepare the grid columns
     *
     * @return Oggetto_News_Block_Adminhtml_Category_Edit_Tab_News
     */
    protected function _prepareColumns()
    {
        $this->addColumn('in_news', array(
            'header_css_class' => 'a-center',
            'type' => 'checkbox',
            'name' => 'in_news',
            'values' => $this->_getSelectedNews(),
            'align' => 'center',
            'index' => 'entity_id'
        ));
        $this->addColumn('name', array(
            'header' => Mage::helper('oggetto_news')->__('Name'),
            'align' => 'left',
            'index' => 'name',
            'renderer' => 'oggetto_news/adminhtml_helper_column_renderer_relation',
            'params' => array(
                'id' => 'getId'
            ),
            'base_link' => 'adminhtml/news_news/edit',
        ));
        $this->addColumn('position', array(
            'header' => Mage::helper('oggetto_news')->__('Position'),
            'name' => 'position',
            'width' => 60,
            'type' => 'number',
            'validate_class' => 'validate-number',
            'index' => 'position',
            'editable' => true,
        ));
    }

    /**
     * Retrieve selected news
     *
     * @return array
     */
    protected function _getSelectedNews()
    {
        $news = $this->getCategoryNews();
        if (!is_array($news)) {
            $news = array_keys($this->getSelectedNews());
        }
        return $news;
    }

    /**
     * Retrieve selected news
     *
     * @return array
     */
    public function getSelectedNews()
    {
        $news = array();
        $selected = Mage::registry('current_category')->getSelectedNews();
        if (!is_array($selected)) {
            $selected = array();
        }
        foreach ($selected as $_news) {
            $news[$_news->getId()] = array('position' => $_news->getPosition());
        }
        return $news;
    }

    /**
     * Get row url
     *
     * @param Oggetto_News_Model_News $item Item
     * @return string
     */
    public function getRowUrl($item)
    {
        return '#';
    }

    /**
     * Get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/newsGrid', array(
            'id' => $this->getCategory()->getId()
        ));
    }

    /**
     * Get the current category
     *
     * @return Oggetto_News_Model_Category
     */
    public function getCategory()
    {
        return Mage::registry('current_category');
    }

    /**
     * Add filter
     *
     * @param object $column Column
     * @return Oggetto_News_Block_Adminhtml_Category_Edit_Tab_News
     */
    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        if ($column->getId() == 'in_news') {
            $newsIds = $this->_getSelectedNews();
            if (empty($newsIds)) {
                $newsIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in' => $newsIds));
            } else {
                if ($newsIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('nin' => $newsIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }
}
