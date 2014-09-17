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
 * News category - News relation resource model collection
 *
 * @category    Oggetto
 * @package     Oggetto_News
 */
class Oggetto_News_Model_Resource_Category_News_Collection
    extends Oggetto_News_Model_Resource_News_Collection
{

    /**
     * Remember if fields have been joined
     * @var bool
     */
    protected $_joinedFields = false;

    /**
     * Join the link table
     *
     * @return Oggetto_News_Model_Resource_Category_News_Collection
     */
    public function joinFields()
    {
        if (!$this->_joinedFields) {
            $this->getSelect()->join(
                array('related' => $this->getTable('oggetto_news/category_news')),
                'related.news_id = main_table.entity_id',
                array('position')
            );
            $this->_joinedFields = true;
        }
        return $this;
    }

    /**
     * Add category filter
     *
     * @param Oggetto_News_Model_Category | int $category Category
     * @return Oggetto_News_Model_Resource_Category_News_Collection
     */
    public function addCategoryFilter($category)
    {
        if ($category instanceof Oggetto_News_Model_Category) {
            $category = $category->getId();
        }
        if (!$this->_joinedFields) {
            $this->joinFields();
        }
        $this->getSelect()->where('related.category_id = ?', $category);
        return $this;
    }
}
