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
 * Test for news default helper
 *
 * @category    Oggetto
 * @package     Oggetto_News
 */
class Oggetto_News_Test_Helper_Data
    extends EcomDev_PHPUnit_Test_Case_Controller
{

    /**
     * Test get category url rewrite list
     *
     * @loadFixture storeConfig
     *
     * @return void
     */
    public function testGetCategoryUrlRewriteList()
    {
        $this->assertEquals('all_categories', Mage::helper('oggetto_news/data')->getCategoryUrlRewriteList());
    }

    /**
     * Test get the url to the news categories list page
     *
     * @loadFixture storeConfig
     *
     * @return void
     */
    public function testGetCategoriesUrl()
    {
        $this->assertEquals(Mage::getBaseUrl() . 'all_categories',
            Mage::helper('oggetto_news/data')->getCategoriesUrl());
    }

    /**
     * Test check if breadcrumbs can be used
     *
     * @loadFixture storeConfig
     *
     * @return void
     */
    public function testUseBreadcrumbsForCategory()
    {
        $this->assertFalse(Mage::helper('oggetto_news/data')->useBreadcrumbsForCategory());
    }

    /**
     * Test get the root id
     *
     * @return void
     */
    public function testGetRootCategoryId()
    {
        $this->assertEquals(1, Mage::helper('oggetto_news/data')->getRootCategoryId());
    }

    /**
     * Test get Url prefix
     *
     * @loadFixture storeConfig
     *
     * @return void
     */
    public function testGetUrlPrefix()
    {
        $this->assertEquals('news/', Mage::helper('oggetto_news/data')->getUrlPrefix('news'));
        $this->assertEquals('news/', Mage::helper('oggetto_news/data')->getUrlPrefix('category'));
    }

    /**
     * Test get Url suffix
     *
     * @loadFixture storeConfig
     *
     * @return void
     */
    public function testGetUrlSuffix()
    {
        $this->assertEquals('.html', Mage::helper('oggetto_news/data')->getUrlSuffix('news'));
        $this->assertEquals('', Mage::helper('oggetto_news/data')->getUrlSuffix('category'));
    }

    /**
     * Test get news url prefix
     *
     * @loadFixture storeConfig
     *
     * @return void
     */
    public function testGetNewsUrlPrefix()
    {
        $this->assertEquals('news/', Mage::helper('oggetto_news/data')->getNewsUrlPrefix());
    }

    /**
     * Test get news url suffix
     *
     * @loadFixture storeConfig
     *
     * @return void
     */
    public function testGetNewsUrlSuffix()
    {
        $this->assertEquals('.html', Mage::helper('oggetto_news/data')->getNewsUrlSuffix());
    }

    /**
     * Test get category url prefix
     *
     * @loadFixture storeConfig
     *
     * @return void
     */
    public function testGetCategoryUrlPrefix()
    {
        $this->assertEquals('news/', Mage::helper('oggetto_news/data')->getCategoryUrlPrefix());
    }

    /**
     * Test get category url suffix
     *
     * @loadFixture storeConfig
     *
     * @return void
     */
    public function testGetCategoryUrlSuffix()
    {
        $this->assertEquals('', Mage::helper('oggetto_news/data')->getCategoryUrlSuffix());
    }

    /**
     * Test get news url rewrite list
     *
     * @loadFixture storeConfig
     *
     * @return void
     */
    public function testGetNewsUrlRewriteList()
    {
        $this->assertEquals('all_news', Mage::helper('oggetto_news/data')->getNewsUrlRewriteList());
    }

    /**
     * Test get url rewrite list
     *
     * @loadFixture storeConfig
     *
     * @return void
     */
    public function testGetUrlRewriteList()
    {
        $this->assertEquals('all_news', Mage::helper('oggetto_news/data')->getUrlRewriteList('news'));
        $this->assertEquals('all_categories', Mage::helper('oggetto_news/data')->getUrlRewriteList('category'));
    }


    /**
     * Test get the url to the news list page
     *
     * @loadFixture storeConfig
     *
     * @return void
     */
    public function testGetNewsUrl()
    {
        $this->assertEquals(Mage::getBaseUrl() . 'all_news', Mage::helper('oggetto_news/data')->getNewsUrl());
    }

    /**
     * Test check if breadcrumbs can be used
     *
     * @loadFixture storeConfig
     *
     * @return void
     */
    public function testUseBreadcrumbsForNews()
    {
        $this->assertTrue(Mage::helper('oggetto_news/data')->useBreadcrumbsForNews());
    }

    /**
     * Test get category recursion
     *
     * @loadFixture storeConfig
     *
     * @return void
     */
    public function testGetCategoryRecursion()
    {
        $this->assertEquals(10, Mage::helper('oggetto_news/data')->getCategoryRecursion());
    }

    /**
     * Test get category tree flag
     *
     * @loadFixture storeConfig
     *
     * @return void
     */
    public function testGetCategoryTreeFlag()
    {
        $this->assertTrue(Mage::helper('oggetto_news/data')->getCategoryTreeFlag());
    }
}