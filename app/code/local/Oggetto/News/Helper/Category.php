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
 * News category helper
 *
 * @category    Oggetto
 * @package     Oggetto_News
 */
class Oggetto_News_Helper_Category
    extends Mage_Core_Helper_Abstract
{

    /**
     * @var int CATEGORY_ROOT_ID Root category id
     */
    const CATEGORY_ROOT_ID = 1;

    /**
     * Get the url to the news categories list page
     *
     * @return string
     */
    public function getCategoriesUrl()
    {
        if ($listKey = Mage::getStoreConfig('oggetto_news/category/url_rewrite_list')) {
            return Mage::getUrl('', array('_direct' => $listKey));
        }
        return Mage::getUrl('oggetto_news/category/index');
    }

    /**
     * Check if breadcrumbs can be used
     *
     * @return bool
     */
    public function getUseBreadcrumbs()
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
}
