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
 * News category admin tree block
 *
 * @category    Oggetto
 * @package     Oggetto_News
 */
class Oggetto_News_Block_Adminhtml_Category_Tree
    extends Oggetto_News_Block_Adminhtml_Category_Abstract
{

    /**
     * Constructor
     *
     * @return Oggetto_News_Block_Adminhtml_Category_Tree
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('oggetto_news/category/tree.phtml');
        $this->setUseAjax(true);
        $this->_withProductCount = true;
    }

    /**
     * Prepare the layout
     *
     * @return Oggetto_News_Block_Adminhtml_Category_Tree
     */
    protected function _prepareLayout()
    {
        $addUrl = $this->getUrl("*/*/add", array(
            '_current' => true,
            'id' => null,
            '_query' => false
        ));

        $this->setChild('add_sub_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('oggetto_news')->__('Add Child News category'),
                    'onclick' => "addNew('" . $addUrl . "', false)",
                    'class' => 'add',
                    'id' => 'add_child_category_button',
                    'style' => $this->canAddChild() ? '' : 'display: none;'
                ))
        );

        $this->setChild('add_root_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('oggetto_news')->__('Add Root News category'),
                    'onclick' => "addNew('" . $addUrl . "', true)",
                    'class' => 'add',
                    'id' => 'add_root_category_button'
                ))
        );
        return parent::_prepareLayout();
    }

    /**
     * Get the news category collection
     *
     * @return Oggetto_News_Model_Resource_Category_Collection
     */
    public function getCategoryCollection()
    {
        $collection = $this->getData('category_collection');
        if (is_null($collection)) {
            $collection = Mage::getModel('oggetto_news/category')->getCollection();
            $this->setData('category_collection', $collection);
        }
        return $collection;
    }

    /**
     * Get html for add root button
     *
     * @return string
     */
    public function getAddRootButtonHtml()
    {
        return $this->getChildHtml('add_root_button');
    }

    /**
     * Get html for add child button
     *
     * @return string
     */
    public function getAddSubButtonHtml()
    {
        return $this->getChildHtml('add_sub_button');
    }

    /**
     * Get html for expand button
     *
     * @return string
     */
    public function getExpandButtonHtml()
    {
        return $this->getChildHtml('expand_button');
    }

    /**
     * Get html for add collapse button
     *
     * @return string
     */
    public function getCollapseButtonHtml()
    {
        return $this->getChildHtml('collapse_button');
    }

    /**
     * Get url for tree load
     *
     * @param mixed $expanded Expanded
     * @return string
     */
    public function getLoadTreeUrl($expanded = null)
    {
        $params = array('_current' => true, 'id' => null, 'store' => null);
        if ((is_null($expanded) && Mage::getSingleton('admin/session')->getCategoryIsTreeWasExpanded()) || $expanded == true) {
            $params['expand_all'] = true;
        }
        return $this->getUrl('*/*/categoriesJson', $params);
    }

    /**
     * Get url for loading nodes
     *
     * @return string
     */
    public function getNodesUrl()
    {
        return $this->getUrl('*/news_categories/jsonTree');
    }

    /**
     * Check if tree is expanded
     *
     * @return string
     */
    public function getIsWasExpanded()
    {
        return Mage::getSingleton('admin/session')->getCategoryIsTreeWasExpanded();
    }

    /**
     * Get url for moving news category
     *
     * @return string
     */
    public function getMoveUrl()
    {
        return $this->getUrl('*/news_category/move');
    }

    /**
     * Get the tree as json
     *
     * @param mixed $parentNodeCategory Parent node category
     * @return string
     */
    public function getTree($parentNodeCategory = null)
    {
        $rootArray = $this->_getNodeJson($this->getRoot($parentNodeCategory));
        $tree = isset($rootArray['children']) ? $rootArray['children'] : array();
        return $tree;
    }

    /**
     * Get the tree as json
     *
     * @param mixed $parentNodeCategory Parent node category
     * @return string
     */
    public function getTreeJson($parentNodeCategory = null)
    {
        $rootArray = $this->_getNodeJson($this->getRoot($parentNodeCategory));
        $json = Mage::helper('core')->jsonEncode(isset($rootArray['children']) ? $rootArray['children'] : array());
        return $json;
    }

    /**
     * Get JSON of array of news categories, that are breadcrumbs for specified news category path
     *
     * @param string $path Path
     * @param string $javascriptVarName Javascript var name
     *
     * @return string
     */
    public function getBreadcrumbsJavascript($path, $javascriptVarName)
    {
        if (empty($path)) {
            return '';
        }

        $categories = Mage::getResourceSingleton('oggetto_news/category_tree')->loadBreadcrumbsArray($path);
        if (empty($categories)) {
            return '';
        }
        foreach ($categories as $key => $category) {
            $categories[$key] = $this->_getNodeJson($category);
        }
        return
            '<script type="text/javascript">'
            . $javascriptVarName . ' = ' . Mage::helper('core')->jsonEncode($categories) . ';'
            . ($this->canAddChild() ? '$("add_child_category_button").show();' : '$("add_child_category_button").hide();')
            . '</script>';
    }

    /**
     * Get JSON of a tree node or an associative array
     *
     * @param Varien_Data_Tree_Node|array $node Node
     * @param int $level Level
     *
     * @return string
     */
    protected function _getNodeJson($node, $level = 0)
    {
        // create a node from data array
        if (is_array($node)) {
            $node = new Varien_Data_Tree_Node($node, 'entity_id', new Varien_Data_Tree);
        }
        $item = array();
        $item['text'] = $this->buildNodeName($node);
        $item['id'] = $node->getId();
        $item['path'] = $node->getData('path');
        $item['cls'] = 'folder';
        if ($node->getStatus()) {
            $item['cls'] .= ' active-category';
        } else {
            $item['cls'] .= ' no-active-category';
        }
        $item['allowDrop'] = true;
        $item['allowDrag'] = true;
        if ((int)$node->getChildrenCount() > 0) {
            $item['children'] = array();
        }
        $isParent = $this->_isParentSelectedCategory($node);
        if ($node->hasChildren()) {
            $item['children'] = array();
            if (!($this->getUseAjax() && $node->getLevel() > 1 && !$isParent)) {
                foreach ($node->getChildren() as $child) {
                    $item['children'][] = $this->_getNodeJson($child, $level + 1);
                }
            }
        }
        if ($isParent || $node->getLevel() < 1) {
            $item['expanded'] = true;
        }
        return $item;
    }

    /**
     * Get node label
     *
     * @param Varien_Object $node Node
     * @return string
     */
    public function buildNodeName($node)
    {
        $result = $this->escapeHtml($node->getName());
        return $result;
    }

    /**
     * Check if entity is movable
     *
     * @param Varien_Object $node Node
     * @return bool
     */
    protected function _isCategoryMoveable($node)
    {
        return true;
    }

    /**
     * Check if parent is selected
     *
     * @param Varien_Object $node Node
     * @return bool
     */
    protected function _isParentSelectedCategory($node)
    {
        if ($node && $this->getCategory()) {
            $pathIds = $this->getCategory()->getPathIds();
            if (in_array($node->getId(), $pathIds)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if page loaded by outside link to news category edit
     *
     * @return boolean
     */
    public function isClearEdit()
    {
        return (bool)$this->getRequest()->getParam('clear');
    }

    /**
     * Check availability of adding root news category
     *
     * @return boolean
     */
    public function canAddRootCategory()
    {
        return true;
    }

    /**
     * Check availability of adding child news category
     *
     * @return boolean
     */
    public function canAddChild()
    {
        return true;
    }
}
