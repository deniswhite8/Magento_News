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
 * News resource model test
 *
 * @category    Oggetto
 * @package     Oggetto_News
 */
class Oggetto_News_Test_Model_Resource_News
    extends EcomDev_PHPUnit_Test_Case_Controller
{
    /**
     * Test get id by url key with result
     *
     * @loadFixture news
     *
     * @return void
     */
    public function testGetIdByUrlKeyWithResult()
    {
        $this->assertEquals(2, Mage::getResourceModel('oggetto_news/news')->getIdByUrlKey('ololo'));
    }

    /**
     * Test get id by url key without result
     *
     * @return mixed
     */
    public function testGetIdByUrlKeyWithoutResult()
    {
        $this->assertFalse(Mage::getResourceModel('oggetto_news/news')->getIdByUrlKey('sdfewgferg'));
    }

    /**
     * Test get id by url path with result
     *
     * @loadFixture news
     *
     * @return mixed
     */
    public function testGetIdByUrlPathWithResult()
    {
        $this->assertFalse(Mage::getResourceModel('oggetto_news/news')->getIdByUrlKey('qwe/ololo'));
    }

    /**
     * Test get id by url path without result
     *
     * @loadFixture news
     *
     * @return mixed
     */
    public function testGetIdByUrlPathWithoutResult()
    {
        $this->assertFalse(Mage::getResourceModel('oggetto_news/news')->getIdByUrlKey('qwe/sdfewgferg'));
    }
}