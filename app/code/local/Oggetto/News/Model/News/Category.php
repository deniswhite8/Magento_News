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
 * News news category model
 *
 * @category    Oggetto
 * @package     Oggetto_News
 */
class Oggetto_News_Model_News_Category
    extends Mage_Core_Model_Abstract
{
    /**
     * Initialize resource
     *
     * @return Oggetto_News_Model_News_Category
     */
    protected function _construct()
    {
        $this->_init('oggetto_news/news_category');
    }

    /**
     * Save data for news - category relation
     *
     * @param  Oggetto_News_Model_News $news News
     * @return Oggetto_News_Model_News_Category
     */
    public function saveNewsRelation($news)
    {
        $data = $news->getCategoriesData();
        if (!is_null($data)) {
            $this->_getResource()->saveNewsRelation($news, $data);
        }
        return $this;
    }

    /**
     * Get categories for news
     *
     * @param Oggetto_News_Model_News $news News
     * @return Oggetto_News_Model_Resource_News_Category_Collection
     */
    public function getCategoriesCollection($news)
    {
        $collection = Mage::getResourceModel('oggetto_news/news_category_collection')
            ->addNewsFilter($news);
        return $collection;
    }
}
