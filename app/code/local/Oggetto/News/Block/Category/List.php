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
 * News category list block
 *
 * @category    Oggetto
 * @package     Oggetto_News
 */
class Oggetto_News_Block_Category_List
    extends Mage_Core_Block_Template
{

    /**
     * Initialize
     *
     * @return Oggetto_News_Block_Category_List
     */
    public function __construct()
    {
        parent::__construct();
//        $categories = Mage::getResourceModel('oggetto_news/category_collection')
//                         ->addFieldToFilter('status', 1);

//        $categories->getSelect()->order('main_table.position');
//        $this->setCategories($categories);

        $this->setCategories(Mage::getResourceModel('oggetto_news/category_collection')->addFieldToFilter('level', 1));
    }

    /**
     * Prepare the layout
     *
     * @return Oggetto_News_Block_Category_List
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

//        $this->getCategories()->addFieldToFilter('level', 1);
        if ($this->_getDisplayMode() == 0) {
            $pager = $this->getLayout()->createBlock('page/html_pager', 'oggetto_news.categories.html.pager');
            $pager->setAvailableLimit(array(10 => 10));
            $pager->setCollection($this->getCategories());
            $this->setChild('pager', $pager);
        }
        return $this;
    }

    /**
     * Get the pager html
     *
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * Get the tree html
     *
     * @return string
     */
    public function getTreeHtml()
    {
        return $this->getChildHtml('category_tree');
    }

    /**
     * Get the display mode
     *
     * @return int
     */
    protected function _getDisplayMode()
    {
        return Mage::helper('oggetto_news/data')->getCategoryTreeFlag();
    }

    /**
     * Draw news category
     *
     * @param Oggetto_News_Model_Category $category Category
     * @param int $level Level
     *
     * @return int
     */
    public function drawCategory($category, $level = 0)
    {
        $html = '';
        $categories = $category->getChildSubTree()->getItems();
        $startLevel = $category->getLevel() + 1;
        $keys = array_keys($categories);

        for ($i = 0; $i < count($keys); $i++) {
            $category = $categories[$keys[$i]];
            $nextCategory = $categories[$keys[$i + 1]];

            $html .= '<li>';
            $html .= '<a href="' . $category->getCategoryUrl() . '">' . $category->getName() . '</a>';
            if ($nextCategory && $nextCategory->getLevel() > $category->getLevel()) {
                $html .= '<ul>';
            } else {
                $html .= '</li>';

                if ($nextCategory) {
                    $lowLevel = $nextCategory->getLevel();
                } else {
                    $lowLevel = $startLevel;
                }
                for ($j = 0; $j < $category->getLevel() - $lowLevel; $j++) {
                    $html .= '</ul></li>';
                }
            }
        }

        return $html;
    }

    /**
     * Get recursion
     *
     * @return int
     */
    public function getRecursion()
    {
        if (!$this->hasData('recursion')) {
            $this->setData('recursion', Mage::helper('oggetto_news/data')->getCategoryRecursion());
        }
        return $this->getData('recursion');
    }
}
