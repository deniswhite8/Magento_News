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
 * News - News category relation model
 *
 * @category    Oggetto
 * @package     Oggetto_News
 */
class Oggetto_News_Model_Resource_News_Category
    extends Mage_Core_Model_Resource_Db_Abstract
{

    /**
     * Initialize resource model
     *
     * @return Oggetto_News_Model_Resource_News_Category
     */
    protected function _construct()
    {
        $this->_init('oggetto_news/news_category', 'rel_id');
    }

    /**
     * Save news - category relations
     *
     * @param Oggetto_News_Model_News $news        News
     * @param array                   $categoryIds Data
     *
     * @return Oggetto_News_Model_Resource_News_Category
     */
    public function saveNewsRelation($news, $categoryIds)
    {
        if (is_null($categoryIds)) {
            return $this;
        }
        $oldCategories = $news->getSelectedCategories();
        $oldCategoryIds = array();
        foreach ($oldCategories as $category) {
            $oldCategoryIds[] = $category->getId();
        }
        $insert = array_diff($categoryIds, $oldCategoryIds);
        $delete = array_diff($oldCategoryIds, $categoryIds);
        $write = $this->_getWriteAdapter();
        if (!empty($insert)) {
            $data = array();
            foreach ($insert as $categoryId) {
                if (empty($categoryId)) {
                    continue;
                }
                $data[] = array(
                    'category_id' => (int)$categoryId,
                    'news_id' => (int)$news->getId(),
                    'position' => 1
                );
            }
            if ($data) {
                $write->insertMultiple($this->getMainTable(), $data);
            }
        }
        if (!empty($delete)) {
            foreach ($delete as $categoryId) {
                $where = array(
                    'news_id = ?' => (int)$news->getId(),
                    'category_id = ?' => (int)$categoryId,
                );
                $write->delete($this->getMainTable(), $where);
            }
        }
        return $this;
    }
}
