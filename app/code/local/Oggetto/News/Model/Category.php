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
 * News category model
 *
 * @category    Oggetto
 * @package     Oggetto_News
 */
class Oggetto_News_Model_Category
    extends Mage_Core_Model_Abstract
{

    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY = 'oggetto_news_category';
    const CACHE_TAG = 'oggetto_news_category';

    /**
     * Prefix of model events names
     * @var string
     */
    protected $_eventPrefix = 'oggetto_news_category';

    /**
     * Parameter name in event
     * @var string
     */
    protected $_eventObject = 'category';
    protected $_newsInstance = null;

    /**
     * Constructor
     *
     * @return Oggetto_News_Model_Category
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('oggetto_news/category');
    }

    /**
     * Before save news category
     *
     * @return Oggetto_News_Model_Category
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
     * Get the url to the news category details page
     *
     * @return string
     */
    public function getCategoryUrl()
    {
        if ($urlPath = $this->getUrlPath()) {
            $dataHelper = Mage::helper('oggetto_news/data');
            $urlKey = $dataHelper->getCategoryUrlPrefix() . $urlPath . $dataHelper->getCategoryUrlSuffix();
            return Mage::getModel('core/url')->getDirectUrl($urlKey);
        }
        return Mage::getUrl('oggetto_news/category/view', array('id' => $this->getId()));
    }

    /**
     * Check URL key
     *
     * @param string $urlPath Url path
     * @return mixed
     */
    public function getIdByUrlPath($urlPath)
    {
        return $this->_getResource()->getIdByUrlPath($urlPath);
    }

    /**
     * Save category relation
     *
     * @return Oggetto_News_Model_Category
     */
    protected function _afterSave()
    {
        $this->getNewsInstance()->saveCategoryRelation($this);
        return parent::_afterSave();
    }

    /**
     * Get news relation model
     *
     * @return Oggetto_News_Model_Category_News
     */
    public function getNewsInstance()
    {
        if (!$this->_newsInstance) {
            $this->_newsInstance = Mage::getSingleton('oggetto_news/category_news');
        }
        return $this->_newsInstance;
    }

    /**
     * Get selected news array
     *
     * @return array
     */
    public function getSelectedNews()
    {
        if (!$this->hasSelectedNews()) {
            $news = array();
            foreach ($this->getSelectedNewsCollection() as $_news) {
                $news[] = $_news;
            }
            $this->setSelectedNews($news);
        }
        return $this->getData('selected_news');
    }

    /**
     * Retrieve collection selected news
     *
     * @return Oggetto_News_Model_Resource_Category_News_Collection
     */
    public function getSelectedNewsCollection()
    {
        $collection = $this->getNewsInstance()->getNewsCollection($this);
        return $collection;
    }

    /**
     * Get the tree model
     *
     * @return Oggetto_News_Model_Resource_Category_Tree
     */
    public function getTreeModel()
    {
        return Mage::getResourceModel('oggetto_news/category_tree');
    }

    /**
     * Get tree model instance
     *
     * @return Oggetto_News_Model_Resource_Category_Tree
     */
    public function getTreeModelInstance()
    {
        if (is_null($this->_treeModel)) {
            $this->_treeModel = Mage::getResourceSingleton('oggetto_news/category_tree');
        }
        return $this->_treeModel;
    }

    /**
     * Move news category
     *
     * @param   int $parentId        New parent news category id
     * @param   int $afterCategoryId News category id after which we have put current news category
     *
     * @return  Oggetto_News_Model_Category
     */
    public function move($parentId, $afterCategoryId)
    {
        $parent = Mage::getModel('oggetto_news/category')->load($parentId);
        if (!$parent->getId()) {
            Mage::throwException(
                Mage::helper('oggetto_news')->__('News category move operation is not possible: the new parent news category was not found.')
            );
        }
        if (!$this->getId()) {
            Mage::throwException(
                Mage::helper('oggetto_news')->__('News category move operation is not possible: the current news category was not found.')
            );
        } elseif ($parent->getId() == $this->getId()) {
            Mage::throwException(
                Mage::helper('oggetto_news')->__('News category move operation is not possible: parent news category is equal to child news category.')
            );
        }
        $this->setMovedCategoryId($this->getId());
        $eventParams = array(
            $this->_eventObject => $this,
            'parent' => $parent,
            'category_id' => $this->getId(),
            'prev_parent_id' => $this->getParentId(),
            'parent_id' => $parentId
        );
        $moveComplete = false;
        $this->_getResource()->beginTransaction();
        try {
            $this->getResource()->changeParent($this, $parent, $afterCategoryId);
            $this->_getResource()->commit();
            $this->setAffectedCategoryIds(array($this->getId(), $this->getParentId(), $parentId));
            $moveComplete = true;
        } catch (Exception $e) {
            $this->_getResource()->rollBack();
            throw $e;
        }
        if ($moveComplete) {
            Mage::app()->cleanCache(array(self::CACHE_TAG));

            $this->unsetData('path_ids');
            $this->save();
        }
        return $this;
    }

    /**
     * Get the parent news category
     *
     * @return  Oggetto_News_Model_Category
     */
    public function getParentCategory()
    {
        if (!$this->hasData('parent_category')) {
            $this->setData('parent_category', Mage::getModel('oggetto_news/category')->load($this->getParentId()));
        }
        return $this->_getData('parent_category');
    }

    /**
     * Get the parent id
     *
     * @return  int
     */
    public function getParentId()
    {
        $parentIds = $this->getParentIds();
        return intval(array_pop($parentIds));
    }

    /**
     * Get all parent news categories ids
     *
     * @return array
     */
    public function getParentIds()
    {
        return array_diff($this->getPathIds(), array($this->getId()));
    }

    /**
     * Get all news categories children
     *
     * @param bool $asArray As array
     * @return array|string
     */
    public function getAllChildren($asArray = false)
    {
        $children = $this->getResource()->getAllChildren($this);
        if ($asArray) {
            return $children;
        } else {
            return implode(',', $children);
        }
    }

    /**
     * Get all news categories children
     *
     * @return string
     */
    public function getChildCategories()
    {
        return implode(',', $this->getResource()->getChildren($this, false));
    }

    /**
     * Get array news categories ids which are part of news category path
     *
     * @return array
     */
    public function getPathIds()
    {
        $ids = $this->getData('path_ids');
        if (is_null($ids)) {
            $ids = explode('/', $this->getPath());
            $this->setData('path_ids', $ids);
        }
        return $ids;
    }

    /**
     * Retrieve level
     *
     * @return int
     */
    public function getLevel()
    {
        if (!$this->hasLevel()) {
            return count(explode('/', $this->getPath())) - 1;
        }
        return $this->getData('level');
    }

    /**
     * Check if news category has children
     *
     * @return bool
     */
    public function hasChildren()
    {
        return $this->_getResource()->getChildrenAmount($this) > 0;
    }

    /**
     * Check if news category can be deleted
     *
     * @return Oggetto_News_Model_Category
     */
    protected function _beforeDelete()
    {
        if ($this->getResource()->isForbiddenToDelete($this->getId())) {
            Mage::throwException(Mage::helper('oggetto_news')->__("Can't delete root news category."));
        }
        return parent::_beforeDelete();
    }

    /**
     * Get the news categories
     *
     * @param Oggetto_News_Model_Category $parent
     * @param int $recursionLevel
     * @param bool $sorted
     * @param bool $asCollection
     * @param bool $toLoad
     *
     * @return Oggetto_News_Model_Resource_Category_Collection
     */
    public function getCategories($parent, $recursionLevel = 0, $sorted = false, $asCollection = false, $toLoad = true)
    {
        return $this->getResource()->getCategories($parent, $recursionLevel, $sorted, $asCollection, $toLoad);
    }

    /**
     * Return parent news categories of current news category
     *
     * @return array
     */
    public function getParentCategories()
    {
        return $this->getResource()->getParentCategories($this);
    }

    /**
     * Retuen children news categories of current news category
     *
     * @return array
     */
    public function getChildrenCategories()
    {
        return $this->getResource()->getChildrenCategories($this);
    }

    /**
     * Get child sub tree
     *
     * @return Oggetto_News_Model_Resource_Category_Collection
     */
    public function getChildSubTree()
    {
        return $this->getResource()->getChildSubTree($this);
    }

    /**
     * Check if parents are enabled
     *
     * @return bool
     */
    public function getStatusPath()
    {
        $selfStatus = $this->getStatus();

        if (!$selfStatus) {
            return false;
        }

        $parents = $this->getParentCategories();
        $rootId = Mage::helper('oggetto_news/data')->getRootCategoryId();
        foreach ($parents as $parent) {
            if ($parent->getId() == $rootId) {
                continue;
            }
            if (!$parent->getStatus()) {
                return false;
            }
        }
        return $selfStatus;
    }

    /**
     * Get default values
     *
     * @return array
     */
    public function getDefaultValues()
    {
        $values = array();
        $values['status'] = 1;
        return $values;
    }
}
