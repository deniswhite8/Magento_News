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
 * News front contrller
 *
 * @category    Oggetto
 * @package     Oggetto_News
 */
class Oggetto_News_NewsController
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
        if (Mage::helper('oggetto_news/data')->useBreadcrumbsForNews()) {
            if ($breadcrumbBlock = $this->getLayout()->getBlock('breadcrumbs')) {
                $breadcrumbBlock->addCrumb('home', array(
                        'label' => Mage::helper('oggetto_news')->__('Home'),
                        'link' => Mage::getUrl(),
                    )
                );
                $breadcrumbBlock->addCrumb('newss', array(
                        'label' => Mage::helper('oggetto_news')->__('News'),
                        'link' => '',
                    )
                );
            }
        }
        $this->renderLayout();
    }

    /**
     * Init News
     *
     * @return Oggetto_News_Model_News
     */
    protected function _initNews()
    {
        $newsId = $this->getRequest()->getParam('id');
        $news = Mage::getModel('oggetto_news/news')
//            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($newsId);

        if (!$news->getId() || !$news->getStatus()) {
            return false;
        }

        return $news;
    }

    /**
     * View news action
     *
     * @return void
     */
    public function viewAction()
    {
        $news = $this->_initNews();
        if (!$news) {
            $this->_redirect('oggetto_news');
            return;
        }
        Mage::register('current_news', $news);
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        if ($root = $this->getLayout()->getBlock('root')) {
            $root->addBodyClass('news-news news-news' . $news->getId());
        }
        if (Mage::helper('oggetto_news/data')->useBreadcrumbsForNews()) {
            if ($breadcrumbBlock = $this->getLayout()->getBlock('breadcrumbs')) {
                $breadcrumbBlock->addCrumb('home', array(
                        'label' => Mage::helper('oggetto_news/data')->__('Home'),
                        'link' => Mage::getUrl(),
                    )
                );
                $breadcrumbBlock->addCrumb('newss', array(
                        'label' => Mage::helper('oggetto_news')->__('News'),
                        'link' => Mage::helper('oggetto_news/data')->getNewsUrl(),
                    )
                );
                $breadcrumbBlock->addCrumb('news', array(
                        'label' => $news->getName(),
                        'link' => '',
                    )
                );
            }
        }
        $this->renderLayout();
    }
}
