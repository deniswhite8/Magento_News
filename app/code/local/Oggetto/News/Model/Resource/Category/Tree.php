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
 * News category tree resource model
 * @category    Oggetto
 * @package     Oggetto_News
 */
class Oggetto_News_Model_Resource_Category_Tree
    extends Varien_Data_Tree_Dbp
{

    const ID_FIELD = 'entity_id';
    const PATH_FIELD = 'path';
    const ORDER_FIELD = 'order';
    const LEVEL_FIELD = 'level';

    /**
     * News categories resource collection
     * @var Oggetto_News_Model_Resource_Category_Collection
     */
    protected $_collection;
    protected $_storeId;

    /**
     * Inactive news categories ids
     * @var array
     */
    protected $_inactiveCategoryIds = null;

    /**
     * Initialize tree
     *
     * @return Oggetto_News_Model_Resource_Category_Tree
     */
    public function __construct()
    {
        $resource = Mage::getSingleton('core/resource');
        parent::__construct(
            $resource->getConnection('oggetto_news_write'),
            $resource->getTableName('oggetto_news/category'),
            array(
                Varien_Data_Tree_Dbp::ID_FIELD => 'entity_id',
                Varien_Data_Tree_Dbp::PATH_FIELD => 'path',
                Varien_Data_Tree_Dbp::ORDER_FIELD => 'position',
                Varien_Data_Tree_Dbp::LEVEL_FIELD => 'level',
            )
        );
    }

    /**
     * Get news categories collection
     *
     * @param boolean $sorted Sorted
     * @return Oggetto_News_Model_Resource_Category_Collection
     */
    public function getCollection($sorted = false)
    {
        if (is_null($this->_collection)) {
            $this->_collection = $this->_getDefaultCollection($sorted);
        }
        return $this->_collection;
    }

    /**
     * Set the collection
     *
     * @param Oggetto_News_Model_Resource_Category_Collection $collection Collection
     * @return Oggetto_News_Model_Resource_Category_Tree
     */
    public function setCollection($collection)
    {
        if (!is_null($this->_collection)) {
            destruct($this->_collection);
        }
        $this->_collection = $collection;
        return $this;
    }

    /**
     * Get the default collection
     *
     * @param boolean $sorted Sorted
     * @return Oggetto_News_Model_Resource_Category_Collection
     */
    protected function _getDefaultCollection($sorted = false)
    {
        $collection = Mage::getModel('oggetto_news/category')->getCollection();
        if ($sorted) {
            if (is_string($sorted)) {
                $collection->setOrder($sorted);
            } else {
                $collection->setOrder('name');
            }
        }
        return $collection;
    }

    /**
     * Executing parents move method and cleaning cache after it
     *
     * @param Varien_Data_Tree_Node $category Category
     * @param Varien_Data_Tree_Node $newParent New parent
     * @param Varien_Data_Tree_Node $prevNode Prev node
     *
     * @return void
     */
    public function move($category, $newParent, $prevNode = null)
    {
        Mage::getResourceSingleton('oggetto_news/category')->move($category->getId(), $newParent->getId());
        parent::move($category, $newParent, $prevNode);
        $this->_afterMove($category, $newParent, $prevNode);
    }

    /**
     * Move tree after
     *
     * @param Varien_Data_Tree_Node $category Category
     * @param Varien_Data_Tree_Node $newParent New parent
     * @param Varien_Data_Tree_Node $prevNode Prev node
     *
     * @return Oggetto_News_Model_Resource_Category_Tree
     */
    protected function _afterMove($category, $newParent, $prevNode)
    {
        Mage::app()->cleanCache(array(Oggetto_News_Model_Category::CACHE_TAG));
        return $this;
    }

    /**
     * Load whole news category tree, that will include specified news categories ids.
     *
     * @param array $ids Ids
     * @param bool $addCollectionData Add collection data
     *
     * @return Oggetto_News_Model_Resource_Category_Tree
     */
    public function loadByIds($ids, $addCollectionData = true)
    {
        $levelField = $this->_conn->quoteIdentifier('level');
        $pathField = $this->_conn->quoteIdentifier('path');
        // load first two levels, if no ids specified
        if (empty($ids)) {
            $select = $this->_conn->select()
                ->from($this->_table, 'entity_id')
                ->where($levelField . ' <= 2');
            $ids = $this->_conn->fetchCol($select);
        }
        if (!is_array($ids)) {
            $ids = array($ids);
        }
        foreach ($ids as $key => $id) {
            $ids[$key] = (int)$id;
        }
        // collect paths of specified IDs and prepare to collect all their parents and neighbours
        $select = $this->_conn->select()
            ->from($this->_table, array('path', 'level'))
            ->where('entity_id IN (?)', $ids);
        $where = array($levelField . '=0' => true);

        foreach ($this->_conn->fetchAll($select) as $item) {
            $pathIds = explode('/', $item['path']);
            $level = (int)$item['level'];
            while ($level > 0) {
                $pathIds[count($pathIds) - 1] = '%';
                $path = implode('/', $pathIds);
                $where["$levelField=$level AND $pathField LIKE '$path'"] = true;
                array_pop($pathIds);
                $level--;
            }
        }
        $where = array_keys($where);

        // get all required records
        if ($addCollectionData) {
            $select = $this->_createCollectionDataSelect();
        } else {
            $select = clone $this->_select;
            $select->order($this->_orderField . ' ' . Varien_Db_Select::SQL_ASC);
        }
        $select->where(implode(' OR ', $where));

        // get array of records and add them as nodes to the tree
        $arrNodes = $this->_conn->fetchAll($select);
        if (!$arrNodes) {
            return false;
        }
        $childrenItems = array();
        foreach ($arrNodes as $key => $nodeInfo) {
            $pathToParent = explode('/', $nodeInfo[$this->_pathField]);
            array_pop($pathToParent);
            $pathToParent = implode('/', $pathToParent);
            $childrenItems[$pathToParent][] = $nodeInfo;
        }
        $this->addChildNodes($childrenItems, '', null);
        return $this;
    }

    /**
     * Load array of news category parents
     *
     * @param string $path Path
     * @param bool $addCollectionData Add collection data
     * @param bool $withRootNode With root node
     *
     * @return array
     */
    public function loadBreadcrumbsArray($path, $addCollectionData = true, $withRootNode = false)
    {
        $pathIds = explode('/', $path);
        if (!$withRootNode) {
            array_shift($pathIds);
        }
        $result = array();
        if (!empty($pathIds)) {
            if ($addCollectionData) {
                $select = $this->_createCollectionDataSelect(false);
            } else {
                $select = clone $this->_select;
            }
            $select
                ->where('main_table.entity_id IN(?)', $pathIds)
                ->order($this->_conn->getLengthSql('main_table.path') . ' ' . Varien_Db_Select::SQL_ASC);
            $result = $this->_conn->fetchAll($select);
        }
        return $result;
    }

    /**
     * Obtain select for news categories, by default everything from entity table is selected + name
     *
     * @param bool $sorted Sorted
     * @param array $optionalAttributes Optional attributes
     *
     * @return Zend_Db_Select
     */
    protected function _createCollectionDataSelect($sorted = true)
    {
        $select = $this->_getDefaultCollection($sorted ? $this->_orderField : false)->getSelect();
        $categoriesTable = Mage::getSingleton('core/resource')->getTableName('oggetto_news/category');
        $subConcat = $this->_conn->getConcatSql(array('main_table.path', $this->_conn->quote('/%')));
        $subSelect = $this->_conn->select()
            ->from(array('see' => $categoriesTable), null)
            ->where('see.entity_id = main_table.entity_id')
            ->orWhere('see.path LIKE ?', $subConcat);
        return $select;
    }

    /**
     * Get real existing news category ids by specified ids
     *
     * @param array $ids Ids
     * @return array
     */
    public function getExistingCategoryIdsBySpecifiedIds($ids)
    {
        if (empty($ids)) {
            return array();
        }
        if (!is_array($ids)) {
            $ids = array($ids);
        }
        $select = $this->_conn->select()
            ->from($this->_table, array('entity_id'))
            ->where('entity_id IN (?)', $ids);
        return $this->_conn->fetchCol($select);
    }

    /**
     * Add collection data
     *
     * @param Oggetto_News_Model_Resource_Category_Collection $collection Collection
     * @param boolean $sorted Sorted
     * @param array $exclude Exclude
     * @param boolean $toLoad To load
     * @param boolean $onlyActive Only active
     *
     * @return Oggetto_News_Model_Resource_Category_Tree
     */
    public function addCollectionData($collection = null, $sorted = false, $exclude = array(), $toLoad = true, $onlyActive = false)
    {
        if (is_null($collection)) {
            $collection = $this->getCollection($sorted);
        } else {
            $this->setCollection($collection);
        }
        if (!is_array($exclude)) {
            $exclude = array($exclude);
        }
        $nodeIds = array();
        foreach ($this->getNodes() as $node) {
            if (!in_array($node->getId(), $exclude)) {
                $nodeIds[] = $node->getId();
            }
        }
        $collection->addIdFilter($nodeIds);
        if ($onlyActive) {
            $disabledIds = $this->_getDisabledIds($collection);
            if ($disabledIds) {
                $collection->addFieldToFilter('entity_id', array('nin' => $disabledIds));
            }
            $collection->addFieldToFilter('status', 1);
        }
        if ($toLoad) {
            $collection->load();
            foreach ($collection as $category) {
                if ($this->getNodeById($category->getId())) {
                    $this->getNodeById($category->getId())->addData($category->getData());
                }
            }
            foreach ($this->getNodes() as $node) {
                if (!$collection->getItemById($node->getId()) && $node->getParent()) {
                    $this->removeNode($node);
                }
            }
        }
        return $this;
    }

    /**
     * Add inactive news categories ids
     *
     * @param array $ids Ids
     * @return Oggetto_News_Model_Resource_Category_Tree
     */
    public function addInactiveCategoryIds($ids)
    {
        if (!is_array($this->_inactiveCategoryIds)) {
            $this->_initInactiveCategoryIds();
        }
        $this->_inactiveCategoryIds = array_merge($ids, $this->_inactiveCategoryIds);
        return $this;
    }

    /**
     * Retrieve inactive news categories ids
     *
     * @return Oggetto_News_Model_Resource_Category_Tree
     */
    protected function _initInactiveCategoryIds()
    {
        $this->_inactiveCategoryIds = array();
        return $this;
    }

    /**
     * Retrieve inactive news categories ids
     *
     * @return array
     */
    public function getInactiveCategoryIds()
    {
        if (!is_array($this->_inactiveCategoryIds)) {
            $this->_initInactiveCategoryIds();
        }
        return $this->_inactiveCategoryIds;
    }

    /**
     * Return disable news category ids
     *
     * @param Oggetto_News_Model_Resource_Category_Collection $collection Collection
     * @return array
     */
    protected function _getDisabledIds($collection)
    {
        $this->_inactiveItems = $this->getInactiveCategoryIds();
        $this->_inactiveItems = array_merge(
            $this->_getInactiveItemIds($collection),
            $this->_inactiveItems
        );
        $allIds = $collection->getAllIds();
        $disabledIds = array();

        foreach ($allIds as $id) {
            $parents = $this->getNodeById($id)->getPath();
            foreach ($parents as $parent) {
                if (!$this->_getItemIsActive($parent->getId())) {
                    $disabledIds[] = $id;
                    continue;
                }
            }
        }
        return $disabledIds;
    }

    /**
     * Retrieve inactive news category item ids
     *
     * @param Oggetto_News_Model_Resource_Category_Collection $collection Collection
     * @return array
     */
    protected function _getInactiveItemIds($collection)
    {
        $filter = $collection->getAllIdsSql();
        $table = Mage::getSingleton('core/resource')->getTable('oggetto_news/category');
        $bind = array(
            'cond' => 0,
        );
        $select = $this->_conn->select()
            ->from(array('d' => $table), array('d.entity_id'))
            ->where('d.entity_id IN (?)', new Zend_Db_Expr($filter))
            ->where('status = :cond');
        return $this->_conn->fetchCol($select, $bind);
    }

    /**
     * Check is news category items active
     *
     * @param int $id Id
     * @return boolean
     */
    protected function _getItemIsActive($id)
    {
        if (!in_array($id, $this->_inactiveItems)) {
            return true;
        }
        return false;
    }
}