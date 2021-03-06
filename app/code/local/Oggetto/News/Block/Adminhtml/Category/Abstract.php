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
 * News category admin block abstract
 *
 * @category    Oggetto
 * @package     Oggetto_News
 */
class Oggetto_News_Block_Adminhtml_Category_Abstract
    extends Mage_Adminhtml_Block_Template
{

    /**
     * Get current news category
     *
     * @return Oggetto_News_Model_Entity
     */
    public function getCategory()
    {
        return Mage::registry('category');
    }

    /**
     * Get current news category id
     *
     * @return int
     */
    public function getCategoryId()
    {
        if ($this->getCategory()) {
            return $this->getCategory()->getId();
        }
        return null;
    }

    /**
     * Get current news category Name
     *
     * @return string
     */
    public function getCategoryName()
    {
        return $this->getCategory()->getName();
    }

    /**
     * Get current news category path
     *
     * @return string
     */
    public function getCategoryPath()
    {
        if ($this->getCategory()) {
            return $this->getCategory()->getPath();
        }
        return Mage::helper('oggetto_news/data')->getRootCategoryId();
    }

    /**
     * Check if there is a root news category
     *
     * @return bool
     */
    public function hasRootCategory()
    {
        $root = $this->getRoot();
        if ($root && $root->getId()) {
            return true;
        }
        return false;
    }

    /**
     * Get the root
     * @param Oggetto_News_Model_Category|null $parentNodeCategory Parent node category
     * @param int $recursionLevel Recursion level
     *
     * @return Varien_Data_Tree_Node
     */
    public function getRoot($parentNodeCategory = null, $recursionLevel = 3)
    {
        if (!is_null($parentNodeCategory) && $parentNodeCategory->getId()) {
            return $this->getNode($parentNodeCategory, $recursionLevel);
        }
        $root = Mage::registry('root');
        if (is_null($root)) {
            $rootId = Mage::helper('oggetto_news/data')->getRootCategoryId();
            $tree = Mage::getResourceSingleton('oggetto_news/category_tree')
                ->load(null, $recursionLevel);
            if ($this->getCategory()) {
                $tree->loadEnsuredNodes($this->getCategory(), $tree->getNodeById($rootId));
            }
            $tree->addCollectionData($this->getCategoryCollection());
            $root = $tree->getNodeById($rootId);
            if ($root && $rootId != Mage::helper('oggetto_news/data')->getRootCategoryId()) {
                $root->setIsVisible(true);
            } elseif ($root && $root->getId() == Mage::helper('oggetto_news/data')->getRootCategoryId()) {
                $root->setName(Mage::helper('oggetto_news')->__('Root'));
            }
            Mage::register('root', $root);
        }
        return $root;
    }

    /**
     * Get and register news categories root by specified news categories IDs
     *
     * @param array $ids IDs
     * @return Varien_Data_Tree_Node
     */
    public function getRootByIds($ids)
    {
        $root = Mage::registry('root');
        if (null === $root) {
            $categoryTreeResource = Mage::getResourceSingleton('oggetto_news/category_tree');
            $ids = $categoryTreeResource->getExistingCategoryIdsBySpecifiedIds($ids);
            $tree = $categoryTreeResource->loadByIds($ids);
            $rootId = Mage::helper('oggetto_news/data')->getRootCategoryId();
            $root = $tree->getNodeById($rootId);
            if ($root && $rootId != Mage::helper('oggetto_news/data')->getRootCategoryId()) {
                $root->setIsVisible(true);
            } else if ($root && $root->getId() == Mage::helper('oggetto_news/data')->getRootCategoryId()) {
                $root->setName(Mage::helper('oggetto_news')->__('Root'));
            }
            $tree->addCollectionData($this->getCategoryCollection());
            Mage::register('root', $root);
        }
        return $root;
    }

    /**
     * Get specific node
     *
     * @param Oggetto_News_Model_Category $parentNodeCategory Parent node category
     * @param int $recursionLevel Recursion level
     *
     * @return Varien_Data_Tree_Node
     */
    public function getNode($parentNodeCategory, $recursionLevel = 2)
    {
        $tree = Mage::getResourceModel('oggetto_news/category_tree');
        $nodeId = $parentNodeCategory->getId();
        $parentId = $parentNodeCategory->getParentId();
        $node = $tree->loadNode($nodeId);
        $node->loadChildren($recursionLevel);
        if ($node && $nodeId != Mage::helper('oggetto_news/data')->getRootCategoryId()) {
            $node->setIsVisible(true);
        } elseif ($node && $node->getId() == Mage::helper('oggetto_news/data')->getRootCategoryId()) {
            $node->setName(Mage::helper('oggetto_news')->__('Root'));
        }
        $tree->addCollectionData($this->getCategoryCollection());
        return $node;
    }

    /**
     * Get url for saving data
     *
     * @param array $args Arguments
     * @return string
     */
    public function getSaveUrl(array $args = array())
    {
        $params = array('_current' => true);
        $params = array_merge($params, $args);
        return $this->getUrl('*/*/save', $params);
    }

    /**
     * Get url for edit
     *
     * @return string
     */
    public function getEditUrl()
    {
        return $this->getUrl("*/news_category/edit", array('_current' => true, '_query' => false, 'id' => null, 'parent' => null));
    }

    /**
     * Return root ids
     *
     * @return array
     */
    public function getRootIds()
    {
        return array(Mage::helper('oggetto_news/data')->getRootCategoryId());
    }
}
