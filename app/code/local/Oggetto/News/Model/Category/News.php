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
 * News category news model
 *
 * @category    Oggetto
 * @package     Oggetto_News
 */
class Oggetto_News_Model_Category_News
    extends Mage_Core_Model_Abstract
{

    /**
     * Initialize resource
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('oggetto_news/category_news');
    }

    /**
     * Save data for category - news relation
     *
     * @param  Oggetto_News_Model_Category $category Category
     * @return Oggetto_News_Model_Category_News
     */
    public function saveCategoryRelation($category)
    {
        $data = $category->getNewsData();
        if (!is_null($data)) {
            $this->_getResource()->saveCategoryRelation($category, $data);
        }
        return $this;
    }

    /**
     * Get news for category
     *
     * @param Oggetto_News_Model_Category $category Category
     * @return Oggetto_News_Model_Resource_Category_News_Collection
     */
    public function getNewsCollection($category)
    {
        $collection = Mage::getResourceModel('oggetto_news/category_news_collection')
            ->addCategoryFilter($category);
        return $collection;
    }
}
