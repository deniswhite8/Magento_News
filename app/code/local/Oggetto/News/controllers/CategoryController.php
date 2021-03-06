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
 * News category front contrller
 *
 * @category    Oggetto
 * @package     Oggetto_News
 */
class Oggetto_News_CategoryController
    extends Mage_Core_Controller_Front_Action
{

    /**
     * Default action
     *
     * @return void
     */
    public function indexAction()
    {
        $this->loadLayout();

        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');

        if (Mage::helper('oggetto_news/data')->useBreadcrumbsForCategory() &&
            ($breadcrumbBlock = $this->getLayout()->getBlock('breadcrumbs'))) {

            $breadcrumbBlock
                ->addCrumb('home', array(
                        'label' => Mage::helper('oggetto_news')->__('Home'),
                        'link' => Mage::getUrl(),
                    )
                )
                ->addCrumb('categories', array(
                        'label' => Mage::helper('oggetto_news')->__('News categories'),
                        'link' => '',
                    )
                );
        }

        $this->renderLayout();
    }

    /**
     * Init news category
     *
     * @return Oggetto_News_Model_Category
     */
    protected function _initCategory()
    {
        $categoryId = $this->getRequest()->getParam('id', Mage::helper('oggetto_news/data')->getRootCategoryId());
        $category = Mage::getModel('oggetto_news/category')
            ->load($categoryId);

        if (!$category->getId() || !$category->getStatus()) {
            return false;
        }

        return $category;
    }

    /**
     * View news category action
     *
     * @return void
     */
    public function viewAction()
    {
        $category = $this->_initCategory();

        if (!$category || !$category->getStatusPath()) {
            $this->_forward('no-route');
            return;
        }

        Mage::register('current_category', $category);

        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');

        if ($root = $this->getLayout()->getBlock('root')) {
            $root->addBodyClass('news-category news-category' . $category->getId());
        }

        if (Mage::helper('oggetto_news/data')->useBreadcrumbsForCategory() &&
            ($breadcrumbBlock = $this->getLayout()->getBlock('breadcrumbs'))) {

            $breadcrumbBlock
                ->addCrumb('home', array(
                        'label' => Mage::helper('oggetto_news')->__('Home'),
                        'link' => Mage::getUrl(),
                    )
                )
                ->addCrumb('categories', array(
                        'label' => Mage::helper('oggetto_news')->__('News categories'),
                        'link' => Mage::helper('oggetto_news/data')->getCategoriesUrl(),
                    )
                )
                ->addCrumb('category', array(
                        'label' => $category->getName(),
                        'link' => '',
                    )
                );

            $parents = $category->getParentCategories();
            foreach ($parents as $parent) {
                if ($parent->getId() != Mage::helper('oggetto_news/data')->getRootCategoryId()
                    && $parent->getId() != $category->getId()) {

                    $breadcrumbBlock->addCrumb('category-' . $parent->getId(), array(
                        'label' => $parent->getName(),
                        'link' => $link = $parent->getCategoryUrl(),
                    ));
                }
            }
        }

        $this->renderLayout();
    }
}
