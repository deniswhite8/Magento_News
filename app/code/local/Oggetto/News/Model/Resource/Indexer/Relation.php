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
 * News - Category relation indexer model
 *
 * @category    Oggetto
 * @package     Oggetto_News
 */
class Oggetto_News_Model_Resource_Indexer_Relation
    extends Mage_Index_Model_Resource_Abstract
{

    private $_cacheData = [];

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('oggetto_news/indexer_relation', 'relation_id');
        $this->_setResource('oggetto_label');
    }


    /**
     * Trim url
     *
     * @param string $url Url
     * @return string
     */
    private function _trimUrl($url)
    {
        return trim($url, '/');
    }

    /**
     * Get news url path data for news collection
     *
     * @param Oggetto_News_Model_Resource_News_Collection $newsCollection News collection
     * @return array
     */
    private function _getNewsUrlPathDataForNewsCollection($newsCollection)
    {
        $data = [];

        /** @var Oggetto_News_Model_News $news */
        foreach ($newsCollection as $news) {
            $data[] = [
                'url_path' => $this->_trimUrl($news->getSpecialNewsUrl()),
                'news_id' => $news->getId(),
                'category_id' => null
            ];

            /** @var Oggetto_News_Model_Category $category */
            foreach ($news->getSelectedCategoriesCollection()->addStatusFilter() as $category) {
                $data[] = [
                    'url_path' => $this->_trimUrl(Mage::helper('oggetto_news')->getNewsUrlPrefix() .
                            $category->getSpecialCategoryUrl() . '/' . $news->getUrlKey() .
                            Mage::helper('oggetto_news')->getNewsUrlSuffix()),
                    'news_id' => $news->getId(),
                    'category_id' => $category->getId()
                ];
            }
        }

        return $data;
    }

    /**
     * Get news url path data for category collection
     *
     * @param Oggetto_News_Model_Resource_Category_Collection $categoryCollection Category collection
     * @return array
     */
    private function _getNewsUrlPathDataForCategoryCollection($categoryCollection)
    {
        $data = [];

        /** @var Oggetto_News_Model_Category $category */
        foreach ($categoryCollection->addStatusFilter() as $category) {
            $data[] = [
                'url_path' => $this->_trimUrl($category->getSpecialCategoryUrl()),
                'news_id' => null,
                'category_id' => $category->getId()
            ];

            /** @var Oggetto_News_Model_News $news */
            foreach ($category->getSelectedNewsCollection() as $news) {
                $data[] = [
                    'url_path' => $this->_trimUrl(
                                        Mage::helper('oggetto_news')->getNewsUrlPrefix() .
                                        $category->getSpecialCategoryUrl() . '/' . $news->getUrlKey() .
                                        Mage::helper('oggetto_news')->getNewsUrlSuffix()),
                    'news_id' => $news->getId(),
                    'category_id' => $category->getId()
                ];
            }
        }

        return $data;
    }

    /**
     * Reindex news
     *
     * @param int|array|null $ids News id(s)
     * @return void
     */
    protected function _reindexNews($ids = null)
    {
        $newsCollection = Mage::getResourceModel('oggetto_news/news_collection');

        if (!is_null($ids)) {
            $newsCollection->addIdFilter($ids);
        }

        $insertData = $this->_getNewsUrlPathDataForNewsCollection($newsCollection);

        if (!empty($insertData)) {
            $this->_getWriteAdapter()->insertMultiple($this->_getNewsIndexTableName(), $insertData);
        }
    }

    /**
     * Reindex categories
     *
     * @param int|array|null $ids Category id(s)
     * @return void
     */
    protected function _reindexCategories($ids = null)
    {
        $categoryCollection = Mage::getResourceModel('oggetto_news/category_collection');

        if (!is_null($ids)) {
            $categoryCollection->addIdFilter($ids);
        }

        $insertData = $this->_getNewsUrlPathDataForCategoryCollection($categoryCollection);

        if (!empty($insertData)) {
            $this->_getWriteAdapter()->insertMultiple($this->_getNewsIndexTableName(), $insertData);
        }
    }


    /**
     * Get news id by path from index
     *
     * @param string $path News url path
     * @return int
     */
    public function getNewsIdByPathFromIndex($path)
    {
        $path = $this->_trimUrl($path);

        if ($result = $this->_cacheData[$path]) {
            return $result['news_id'];
        }

        $select = $this->_getReadAdapter()->select()
            ->from($this->_getNewsIndexTableName())
            ->where("url_path = '{$path}'");
        $row = $this->_getReadAdapter()->fetchRow((string)$select);

        $this->_cacheData[$path] = $row;

        return $row['news_id'];
    }

    /**
     * Get category id by path from index
     *
     * @param string $path News url path
     * @return int
     */
    public function getCategoryIdByPathFromIndex($path)
    {
        if ($result = $this->_cacheData[$path]) {
            return $result['category_id'];
        }

        $select = $this->_getReadAdapter()->select()
            ->from($this->_getNewsIndexTableName())
            ->where("url_path = '{$path}'");
        $row = $this->_getReadAdapter()->fetchRow((string)$select);

        $this->_cacheData[$path] = $row;

        return $row['category_id'];
    }

    /**
     * Reindex all entities
     *
     * @return void
     */
    public function reindexAll()
    {
        $this->_clearOldData(null, 'news');
        $this->_clearOldData(null, 'category');

        $this->_reindexNews();
        $this->_reindexCategories();
    }

    /**
     * Reindex on product save
     *
     * @param Mage_Index_Model_Event $event Index event
     * @return void
     */
    public function oggettoNewsNewsSave($event)
    {
        $this->_clearOldData($event->getData('news_id'), 'news');
        $this->_reindexNews($event->getData('news_id'));
    }

    /**
     * Reindex on products mass action
     *
     * @param Mage_Index_Model_Event $event Index event
     * @return void
     */
    public function oggettoNewsCategorySave($event)
    {
        $this->_clearOldData($event->getData('category_id'), 'category');
        $this->_reindexCategories($event->getData('category_id'));
    }

    /**
     * Clear old data
     *
     * @param int|array|null $ids  News/categories id(s)
     * @param string         $type "news" or "category"
     *
     * @return void
     */
    protected function _clearOldData($ids, $type)
    {
        $tableName = $this->_getNewsIndexTableName();

        if (isset($ids)) {
            $idsString = is_array($ids) ? implode(',', $ids) : $ids;
            $this->_getWriteAdapter()->delete($tableName, "{$type}_id IN ($idsString)");
        } else {
            $this->_getWriteAdapter()->truncate($tableName);
        }
    }

    /**
     * Get label index table name
     *
     * @return string
     */
    protected function _getNewsIndexTableName()
    {
        return $this->getTable('oggetto_news/indexer_relation');
    }
}
