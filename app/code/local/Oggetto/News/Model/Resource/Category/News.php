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
 * News category - News relation model
 *
 * @category    Oggetto
 * @package     Oggetto_News
 */
class Oggetto_News_Model_Resource_Category_News
    extends Mage_Core_Model_Resource_Db_Abstract
{

    /**
     * Initialize resource model
     *
     * @return Oggetto_News_Model_Resource_Category_News
     */
    protected function  _construct()
    {
        $this->_init('oggetto_news/category_news', 'rel_id');
    }

    /**
     * Save category - news relations
     *
     * @param Oggetto_News_Model_Category $category Category
     * @param array $data Data
     *
     * @return Oggetto_News_Model_Resource_Category_News
     */
    public function saveCategoryRelation($category, $data)
    {
        if (!is_array($data)) {
            $data = array();
        }

        $adapter = $this->_getWriteAdapter();
        $bind = array(
            ':category_id' => (int)$category->getId(),
        );
        $select = $adapter->select()
            ->from($this->getMainTable(), array('rel_id', 'news_id'))
            ->where('category_id = :category_id');

        $related = $adapter->fetchPairs($select, $bind);
        $deleteIds = array();
        foreach ($related as $relId => $newsId) {
            if (!isset($data[$newsId])) {
                $deleteIds[] = (int)$relId;
            }
        }
        if (!empty($deleteIds)) {
            $adapter->delete($this->getMainTable(), array(
                'rel_id IN (?)' => $deleteIds,
            ));
        }

        foreach ($data as $newsId => $info) {
            $adapter->insertOnDuplicate($this->getMainTable(), array(
                'category_id' => $category->getId(),
                'news_id' => $newsId,
                'position' => @$info['position']
            ), array('position'));
        }
        return $this;
    }

    /**
     * Save news url path
     *
     * @param Oggetto_News_Model_News|Oggetto_News_Model_Category $news News or category
     * @return void
     */
    public function saveNewsUrlPath($object)
    {
        $id = $object->getId();
        $adapter = $this->_getWriteAdapter();

        $newsCondition = '';
        $categoryCondition = '';

        if ($object instanceof Oggetto_News_Model_News) {
            $newsCondition = $adapter->quoteInto('news.entity_id = news_id AND news.entity_id = ?', $id);
            $categoryCondition = 'category.entity_id = category_id';
        } elseif ($object instanceof Oggetto_News_Model_Category) {
            $newsCondition = 'news.entity_id = news_id';
            $categoryCondition = $adapter->quoteInto('category.entity_id = category_id AND category.entity_id = ?', $id);
        }

        $select = $adapter->select()
            ->join(array('news' => $this->getTable('oggetto_news/news')), $newsCondition)
            ->join(array('category' => $this->getTable('oggetto_news/category')), $categoryCondition);

        $select->setPart(Zend_Db_Select::COLUMNS, array(array(null, new Zend_Db_Expr("CONCAT(category.url_path, '/', news.url_key)"), 'news_url_path')));
        $adapter->query($adapter->updateFromSelect($select, $this->getMainTable()));
    }
}
