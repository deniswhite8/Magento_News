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
 * News default helper
 *
 * @category    Oggetto
 * @package     Oggetto_News
 */
class Oggetto_News_Helper_Data
    extends Mage_Core_Helper_Abstract
{

    /**
     * @var int CATEGORY_ROOT_ID Root category id
     */
    const CATEGORY_ROOT_ID = 1;

    /**
     * Get category url rewrite list
     *
     * @return string
     */
    public function getCategoryUrlRewriteList()
    {
        return Mage::getStoreConfig('oggetto_news/category/url_rewrite_list');
    }

    /**
     * Get the url to the news categories list page
     *
     * @return string
     */
    public function getCategoriesUrl()
    {
        if ($listKey = $this->getCategoryUrlRewriteList()) {
            return Mage::getUrl('', array('_direct' => $listKey));
        }
        return Mage::getUrl('oggetto_news/category/index');
    }

    /**
     * Check if breadcrumbs can be used
     *
     * @return bool
     */
    public function useBreadcrumbsForCategory()
    {
        return Mage::getStoreConfigFlag('oggetto_news/category/breadcrumbs');
    }

    /**
     * Get the root id
     *
     * @return int
     */
    public function getRootCategoryId()
    {
        return self::CATEGORY_ROOT_ID;
    }

    /**
     * Get Url prefix
     *
     * @param string $type Model type
     * @return string
     */
    public function getUrlPrefix($type)
    {
        return Mage::getStoreConfig('oggetto_news/' . $type . '/url_prefix');
    }

    /**
     * Get Url suffix
     *
     * @param string $type Model type
     * @return string
     */
    public function getUrlSuffix($type)
    {
        return Mage::getStoreConfig('oggetto_news/' . $type . '/url_suffix');
    }

    /**
     * Get news url prefix
     *
     * @return string
     */
    public function getNewsUrlPrefix()
    {
        return $this->getUrlPrefix('news');
    }

    /**
     * Get news url suffix
     *
     * @return string
     */
    public function getNewsUrlSuffix()
    {
        return $this->getUrlSuffix('news');
    }

    /**
     * Get category url prefix
     *
     * @return string
     */
    public function getCategoryUrlPrefix()
    {
        return $this->getUrlPrefix('category');
    }

    /**
     * Get category url suffix
     *
     * @return string
     */
    public function getCategoryUrlSuffix()
    {
        return $this->getUrlSuffix('category');
    }

    /**
     * Get news url rewrite list
     *
     * @return string
     */
    public function getNewsUrlRewriteList()
    {
        return Mage::getStoreConfig('oggetto_news/news/url_rewrite_list');
    }

    /**
     * Get url rewrite list
     *
     * @param string $type Model type
     * @return string
     */
    public function getUrlRewriteList($type)
    {
        return Mage::getStoreConfig('oggetto_news/' . $type . '/url_rewrite_list');
    }


    /**
     * Get the url to the news list page
     *
     * @return string
     */
    public function getNewsUrl()
    {
        if ($listKey = $this->getNewsUrlRewriteList()) {
            return Mage::getUrl('', array('_direct' => $listKey));
        }
        return Mage::getUrl('oggetto_news/news/index');
    }

    /**
     * Check if breadcrumbs can be used
     *
     * @return bool
     */
    public function useBreadcrumbsForNews()
    {
        return Mage::getStoreConfigFlag('oggetto_news/news/breadcrumbs');
    }

    /**
     * Get category recursion
     *
     * @return string
     */
    public function getCategoryRecursion()
    {
        return Mage::getStoreConfig('oggetto_news/category/recursion');
    }

    /**
     * Get category tree flag
     *
     * @return bool
     */
    public function getCategoryTreeFlag()
    {
        return Mage::getStoreConfigFlag('oggetto_news/category/tree');
    }
}
