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
 * News REST API
 *
 * @category   Oggetto
 * @package    Oggetto_News
 * @subpackage Test
 * @author     Denis Belov <dbelov@oggettoweb.com>
 */
class Oggetto_News_Test_Model_Api2_News_Rest
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
     * Get API user types without admin
     *
     * @return array
     */
    public function getApiUserTypesWithoutAdmin()
    {
        return [
            ['customer'],
            ['guest'],
        ];
    }

    /**
     * Get admin API user types
     *
     * @return array
     */
    public function getAdminApiUserTypes()
    {
        return [
            ['admin']
        ];
    }

    /**
     * Test retrieve collection method
     *
     * @param string $type API user type
     *
     * @return void
     * @dataProvider getAllApiUserTypes
     * @loadFixture news
     */
    public function testRetrieveCollectionMethod($type)
    {
        /** @var $resource Oggetto_News_Model_Api2_News_Rest_Abstract */
        $resource = Mage::getModel("oggetto_news/api2_news_rest_{$type}_v1");
        $request = Mage::getModel('api2/request')->setParams([
            'category' => 1,
            'page' => 2,
            'limit' => 3
        ]);
        $resource->setRequest($request);

        $this->assertEquals([
            ['entity_id' => '7', 'category_ids' => [['category_id' => 1], ['category_id' => 2]], 'name' => '',
                'text' => '', 'status' => null, 'created_at' => null],
            ['entity_id' => '9', 'category_ids' => [['category_id' => 1]], 'name' => '',
                'text' => '', 'status' => null, 'created_at' => null],
        ], $resource->_retrieveCollection());
    }

    /**
     * Test retrieve entity method
     *
     * @param string $type API user type
     *
     * @return void
     * @dataProvider getAllApiUserTypes
     * @loadFixture news
     */
    public function testRetrieveEntityMethod($type)
    {
        /** @var $resource Oggetto_News_Model_Api2_News_Rest_Abstract */
        $resource = Mage::getModel("oggetto_news/api2_news_rest_{$type}_v1");
        $request = Mage::getModel('api2/request')->setParams([
            'entity_id' => 7
        ]);
        $resource->setRequest($request);

        $this->assertEquals(['entity_id' => '7', 'category_ids' => [['category_id' => 1], ['category_id' => 2]],
            'name' => '', 'text' => '', 'status' => null, 'created_at' => null], $resource->_retrieve());
    }

    /**
     * Test deny admin methods for other roles
     *
     * @param string $type API user type
     *
     * @return void
     * @dataProvider getApiUserTypesWithoutAdmin
     */
    public function testDenyAdminMethodsForOtherRoles($type)
    {
        /** @var $resource Oggetto_News_Model_Api2_News_Rest_Abstract */
        $resource = Mage::getModel("oggetto_news/api2_news_rest_{$type}_v1");

        foreach (['_delete', '_create', '_update'] as $method) {
            try {
                $resource->$method();
                $this->markTestIncomplete("Method $method for user $type complete successfully");
            } catch (Exception $e) {
                $this->assertEquals(Mage_Api2_Model_Resource::RESOURCE_METHOD_NOT_ALLOWED, $e->getMessage());
            }
        }
    }

    /**
     * Test delete method for admin
     *
     * @param string $type API user type
     *
     * @return void
     * @dataProvider getAdminApiUserTypes
     */
    public function testDeleteMethodForAdmin($type)
    {
        /** @var $resource Oggetto_News_Model_Api2_News_Rest_Abstract */
        $resource = Mage::getModel("oggetto_news/api2_news_rest_{$type}_v1");
        $request = Mage::getModel('api2/request')->setParams([
            'entity_id' => 42
        ]);
        $resource->setRequest($request);

        $newsMock = $this->getModelMock('oggetto_news/news', ['setId', 'delete']);
        $newsMock->expects($this->at(0))
            ->method('setId')
            ->with(42)
            ->willReturnSelf();
        $newsMock->expects($this->at(1))
            ->method('delete')
            ->willReturnSelf();
        $this->replaceByMock('model', 'oggetto_news/news', $newsMock);

        $resource->_delete();
    }

    /**
     * Test create method for admin
     *
     * @param string $type API user type
     *
     * @return void
     * @dataProvider getAdminApiUserTypes
     */
    public function testCreateMethodForAdmin($type)
    {
        /** @var $resource Oggetto_News_Model_Api2_News_Rest_Abstract */
        $resource = Mage::getModel("oggetto_news/api2_news_rest_{$type}_v1");

        $newsMock = $this->getModelMock('oggetto_news/news', ['setData', 'setCategoriesData', 'save']);
        $newsMock->expects($this->at(0))
            ->method('setData')
            ->with([
                'name'      => 'Breaking news!',
                'text'      => 'trololololo',
                'category_ids' => [
                    ['category_id' => 1],
                    ['category_id' => 2],
                ]
            ])
            ->willReturnSelf();
        $newsMock->expects($this->at(1))
            ->method('setCategoriesData')
            ->with([1, 2])
            ->willReturnSelf();
        $newsMock->expects($this->at(2))
            ->method('save')
            ->willReturnSelf();
        $this->replaceByMock('model', 'oggetto_news/news', $newsMock);

        $resource->_create([
            'entity_id'    => 42,
            'name'         => 'Breaking news!',
            'text'         => 'trololololo',
            'category_ids' => [
                ['category_id' => 1],
                ['category_id' => 2],
            ]
        ]);
    }

    /**
     * Test update method for admin
     *
     * @param string $type API user type
     *
     * @return void
     * @dataProvider getAdminApiUserTypes
     * @loadFixture
     */
    public function testUpdateMethodForAdmin($type)
    {
        /** @var $resource Oggetto_News_Model_Api2_News_Rest_Abstract */
        $resource = Mage::getModel("oggetto_news/api2_news_rest_{$type}_v1");
        $request = Mage::getModel('api2/request')->setParams([
            'entity_id' => 7
        ]);
        $resource->setRequest($request);

        $resource->_update([
            'entity_id'    => 42,
            'name'         => 'Good news, everyone!',
            'category_ids' => [
                ['category_id' => 1],
                ['category_id' => 2],
            ]
        ]);

        $news = Mage::getModel('oggetto_news/news')->load(7);
        $result = $news->getData();
        $result['updated_at'] = null;

        $this->assertEquals(['entity_id' => '7', 'name' => 'Good news, everyone!', 'text' => 'trololololo',
                'status' => null, 'created_at' => null, 'updated_at' => null, 'url_key' => 'good-news-everyone'],
            $result);
        $this->assertEquals([['category_id' => 1], ['category_id' => 2]], $news->getCategoriesIds());
    }
}