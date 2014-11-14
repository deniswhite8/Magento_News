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
 * Category REST API
 *
 * @category   Oggetto
 * @package    Oggetto_News
 * @subpackage Test
 * @author     Denis Belov <dbelov@oggettoweb.com>
 */
class Oggetto_News_Test_Model_Api2_Category_Rest
    extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Get all API user types
     *
     * @return array
     */
    public function getAllApiUserTypes()
    {
        return [
            ['admin'],
            ['customer'],
            ['guest'],
        ];
    }

    /**
     * Test retrieve collection method
     *
     * @param string $type API user type
     *
     * @return void
     * @dataProvider getAllApiUserTypes
     * @loadFixture categories
     */
    public function testRetrieveCollectionMethod($type)
    {
        /** @var $resource Oggetto_News_Model_Api2_Category_Rest_Abstract */
        $resource = Mage::getModel("oggetto_news/api2_category_rest_{$type}_v1");
        $request = Mage::getModel('api2/request')->setParams([
            'page' => 2,
            'limit' => 3
        ]);
        $resource->setRequest($request);

        $this->assertEquals([
            ['entity_id' => '4', 'name' => '', 'parent_id' => 0],
            ['entity_id' => '5', 'name' => '', 'parent_id' => 0],
        ], $resource->_retrieveCollection());
    }

    /**
     * Test retrieve entity method
     *
     * @param string $type API user type
     *
     * @return void
     * @dataProvider getAllApiUserTypes
     * @loadFixture categories
     */
    public function testRetrieveEntityMethod($type)
    {
        /** @var $resource Oggetto_News_Model_Api2_Category_Rest_Abstract */
        $resource = Mage::getModel("oggetto_news/api2_category_rest_{$type}_v1");
        $request = Mage::getModel('api2/request')->setParams([
            'entity_id' => 4
        ]);
        $resource->setRequest($request);

        $this->assertEquals(['entity_id' => '4', 'name' => '', 'parent_id' => 0], $resource->_retrieve());
    }
}