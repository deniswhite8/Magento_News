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
 * News category list block test
 *
 * @category    Oggetto
 * @package     Oggetto_News
 */
class Oggetto_News_Test_Block_News_Category_List
    extends EcomDev_PHPUnit_Test_Case_Controller
{

    /**
     * Test get the current news
     *
     * @return Oggetto_News_Model_News|null
     */
    public function testGetCurrentNews()
    {
        $news = Mage::getModel('oggetto_news/news');

        Mage::unregister('current_news');

        $block = new Oggetto_News_Block_News_Category_List();

        Mage::register('current_news', $news);

        $this->assertEquals($news, $block->getNews());
    }
}
