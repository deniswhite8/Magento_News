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
 * News category model
 *
 * @category    Oggetto
 * @package     Oggetto_News
 */
class Oggetto_News_Test_Model_Category
    extends EcomDev_PHPUnit_Test_Case_Controller
{
    /**
     * Test entity code
     *
     * @return void
     */
    public function testEntityCode()
    {
        $this->assertEquals('oggetto_news_category', Oggetto_News_Model_Category::ENTITY);
    }

    /**
     * Test cache tag
     *
     * @return void
     */
    public function testCacheTag()
    {
        $this->assertEquals('oggetto_news_category', Oggetto_News_Model_Category::CACHE_TAG);
    }

    /**
     * Mock date model
     *
     * @param string $dateString Data string
     * @return void
     */
    private function _mockDateModel($dateString)
    {
        $dateMock = $this->getModelMock('core/date', array('gmtDate'));
        $dateMock->expects($this->any())
            ->method('gmtDate')
            ->will($this->returnValue($dateString));
        $this->replaceByMock('model', 'core/date', $dateMock);
    }

    /**
     * Test before save for new object
     *
     * @return void
     */
    public function testBeforeSaveForNewObject()
    {
        $dateString = '2000-01-01 12:12:12';
        $this->_mockDateModel($dateString);

        $model = Mage::getModel('oggetto_news/category');
        $model->setName('name');
        $model->save();

        $this->assertEquals($dateString, $model->getCreatedAt());
        $this->assertEquals($dateString, $model->getUpdatedAt());
    }

    /**
     * Test before save for old object
     *
     * @return void
     */
    public function testBeforeSaveForOldObject()
    {
        $dateString = '2000-01-01 12:12:12';
        $this->_mockDateModel($dateString);

        $model = Mage::getModel('oggetto_news/category');
        $model->setId(12);
        $model->save();

        $this->assertNull($model->getCreatedAt());
        $this->assertEquals($dateString, $model->getUpdatedAt());
    }

    /**
     * Test get url for category with url key
     *
     * @loadFixture storeConfig
     *
     * @return void
     */
    public function testGetUrlForCategoryWithUrlPath()
    {
        $model = Mage::getModel('oggetto_news/category');
        $model->setUrlPath('p/a/t/h');

        $this->assertEquals(Mage::getBaseUrl() . 'news/p/a/t/h', $model->getCategoryUrl());
    }

    /**
     * Test get url for news without url key
     *
     * @loadFixture storeConfig
     *
     * @return void
     */
    public function testGetUrlForCategoryWithoutUrlKey()
    {
        $model = Mage::getModel('oggetto_news/category');
        $model->setId(12);

        $this->assertEquals(Mage::getBaseUrl() . 'news/category/view/id/12/', $model->getCategoryUrl());
    }

    /**
     * Mock category resource model
     *
     * @param string $methodName  Method name
     * @param mixed  $returnValue Return value
     */
    private function _testPublicResourceMethod($methodName, $returnValue)
    {
        $model = Mage::getModel('oggetto_news/category');

        $newsResourceModelMock = $this->getResourceModelMock('oggetto_news/category', array($methodName));
        $newsResourceModelMock->expects($this->any())
            ->method($methodName)
            ->will($this->returnArgument(0));
        $this->replaceByMock('resource_model', 'oggetto_news/category', $newsResourceModelMock);

        $this->assertEquals($returnValue, $model->$methodName($returnValue));
    }

    /**
     * Test get id by url path
     *
     * @return void
     */
    public function testGetIdByUrlPath()
    {
        $this->_testPublicResourceMethod('getIdByUrlPath', 'p/a/t/h');
    }

    /**
     * Test after save
     *
     * @return void
     */
    public function testAfterSave()
    {
        $param = null;

        $categoryNewsMock = $this->getModelMock('oggetto_news/category_news', array('saveCategoryRelation'));
        $categoryNewsMock->expects($this->any())
            ->method('saveCategoryRelation')
            ->will($this->returnCallback(function ($_param) use (&$param) {
                $param = $_param;
            }));
        $this->replaceByMock('singleton', 'oggetto_news/category_news', $categoryNewsMock);

        $model = Mage::getModel('oggetto_news/category');
        $model->setId(12);
        $model->save();

        $this->assertEquals($model, $param);
    }

    /**
     * Test get news instance
     *
     * @return void
     */
    public function testGetNewsInstance()
    {
        $this->assertInstanceOf('Oggetto_News_Model_Category_News',
            Mage::getModel('oggetto_news/category')->getNewsInstance());
    }

    /**
     * Mock news collection
     *
     * @return Varien_Data_Collection
     */
    private function _mockNewsCollection()
    {
        $collection = new Varien_Data_Collection();
        $collection->addItem(new Varien_Object(array(
            'entity_id' => 1,
            'name' => 'name 1',
        )));
        $collection->addItem(new Varien_Object(array(
            'entity_id' => 2,
            'name' => 'name 2',
        )));

        $categoryNewsMock = $this->getModelMock('oggetto_news/category_news', array('getNewsCollection'));
        $categoryNewsMock->expects($this->any())
            ->method('getNewsCollection')
            ->will($this->returnValue($collection));
        $this->replaceByMock('singleton', 'oggetto_news/category_news', $categoryNewsMock);

        return $collection;
    }

    /**
     * Test get selected news
     *
     * @return void
     */
    public function testGetSelectedNews()
    {
        $collection = $this->_mockNewsCollection();
        $model = Mage::getModel('oggetto_news/category');

        $this->assertEquals($collection->getItems(), $model->getSelectedNews());
        $this->assertEquals($collection->getItems(), $model->getData('selected_news'));
    }

    /**
     * Test get selected news collection
     *
     * @return void
     */
    public function testGetSelectedNewsCollection()
    {
        $collection = $this->_mockNewsCollection();
        $model = Mage::getModel('oggetto_news/category');

        $this->assertEquals($collection, $model->getSelectedNewsCollection());
    }

    /**
     * Test get default values
     *
     * @return void
     */
    public function testDefaultValues()
    {
        $this->assertEquals(array(
            'status' => 1
        ), Mage::getModel('oggetto_news/category')->getDefaultValues());
    }

    /**
     * Test get the tree model
     *
     * @return void
     */
    public function testGetTreeModel()
    {
        $this->assertInstanceOf('Oggetto_News_Model_Resource_Category_Tree',
            Mage::getModel('oggetto_news/category')->getTreeModel());
    }

    /**
     * Test get tree model instance
     *
     * @return void
     */
    public function testGetTreeModelInstance()
    {
        $this->assertInstanceOf('Oggetto_News_Model_Resource_Category_Tree',
            Mage::getModel('oggetto_news/category')->getTreeModelInstance());
    }

    /**
     * Test get the parent news category
     *
     * @return void
     */
    public function testGetParentCategory()
    {
        $parentId = 123;

        $categoryModelMock = $this->getModelMock('oggetto_news/category', array('getParentId'));
        $categoryModelMock->expects($this->any())
            ->method('getParentId')
            ->will($this->returnValue($parentId));
        $this->replaceByMock('model', 'oggetto_news/category', $categoryModelMock);

        $category = Mage::getModel('oggetto_news/category');

        $this->assertInstanceOf('Oggetto_News_Model_Category', $category->getParentCategory());
        $this->assertEquals($category->getParentCategory(), $category->getData('parent_category'));
    }

    /**
     * Test get the parent id
     *
     * @return void
     */
    public function testGetParentId()
    {
        $categoryModelMock = $this->getModelMock('oggetto_news/category', array('getParentIds'));
        $categoryModelMock->expects($this->any())
            ->method('getParentIds')
            ->will($this->returnValue(array('1', '2', '3')));
        $this->replaceByMock('model', 'oggetto_news/category', $categoryModelMock);

        $this->assertEquals(3, Mage::getModel('oggetto_news/category')->getParentId());
    }

    /**
     * Test get all parent news categories ids
     *
     * @return void
     */
    public function testGetParentIds()
    {
        $categoryModelMock = $this->getModelMock('oggetto_news/category', array('getPathIds'));
        $categoryModelMock->expects($this->any())
            ->method('getPathIds')
            ->will($this->returnValue(array('1', '2', '3')));
        $this->replaceByMock('model', 'oggetto_news/category', $categoryModelMock);

        $category = Mage::getModel('oggetto_news/category');
        $category->setId(3);

        $this->assertEquals(array(1, 2), $category->getParentIds());
    }

    /**
     * Test get all news categories children
     *
     * @return void
     */
    public function testGetAllChildren()
    {
        $newsResourceModelMock = $this->getResourceModelMock('oggetto_news/category', array('getAllChildren'));
        $newsResourceModelMock->expects($this->any())
            ->method('getAllChildren')
            ->will($this->returnValue(array(1, 2, 3)));
        $this->replaceByMock('resource_model', 'oggetto_news/category', $newsResourceModelMock);

        $category = Mage::getModel('oggetto_news/category');

        $this->assertEquals('1,2,3', $category->getAllChildren());
        $this->assertEquals(array(1, 2, 3), $category->getAllChildren(true));
        $this->assertEquals('1,2,3', $category->getAllChildren(false));
    }

    /**
     * Test get all news categories children
     *
     * @return void
     */
    public function getChildCategories()
    {
        $newsResourceModelMock = $this->getResourceModelMock('oggetto_news/category', array('getChildren'));
        $newsResourceModelMock->expects($this->any())
            ->method('getChildren')
            ->will($this->returnValue(array(1, 2, 3)));
        $this->replaceByMock('resource_model', 'oggetto_news/category', $newsResourceModelMock);

        $category = Mage::getModel('oggetto_news/category');

        $this->assertEquals('1,2,3', $category->getChildCategories());
    }


    /**
     * Mock getPath method in category model
     *
     * @return void
     */
    private function _mockPath()
    {
        $categoryModelMock = $this->getModelMock('oggetto_news/category', array('getPath'));
        $categoryModelMock->expects($this->any())
            ->method('getPath')
            ->will($this->returnValue('1/2/3'));
        $this->replaceByMock('model', 'oggetto_news/category', $categoryModelMock);
    }


    /**
     * Test get array news categories ids which are part of news category path
     *
     * @return void
     */
    public function testGetPathIds()
    {
        $this->_mockPath();

        $category = Mage::getModel('oggetto_news/category');

        $this->assertEquals(array(1, 2, 3), $category->getPathIds());
        $this->assertEquals(array(1, 2, 3), $category->getData('path_ids'));
    }

    /**
     * Testr retrieved level
     *
     * @return int
     */
    public function testGetLevel()
    {
        $this->_mockPath();

        $category = Mage::getModel('oggetto_news/category');
        $this->assertEquals(2, $category->getLevel());
        $category->setLevel(10);
        $this->assertEquals(10, $category->getLevel());
    }


    /**
     * Test category node
     *
     * @param int   $id           Category id
     * @param array $expectedData Expected data
     */
    private function _testCategoryNode($id, $expectedData)
    {
        $categoryNodeData = Mage::getModel('oggetto_news/category')->load($id)->getData();

        foreach ($expectedData as $key => $value) {
            $this->assertEquals($value, $categoryNodeData[$key]);
        }
    }


    /**
     * Test move category
     *
     * @loadFixture
     *
     * @return void
     */
    public function testMove()
    {
        Mage::getModel('oggetto_news/category')->load(3)->move(1, 1);

        $this->_testCategoryNode(1, array(
            'entity_id' => 1,
            'name' => 'ROOT',
            'url_key' => null,
            'parent_id' => 0,
            'path' => '1',
            'url_path' => null,
            'level' => 0,
            'children_count' => 5,
            'status' => 0
        ));

        $this->_testCategoryNode(2, array(
            'entity_id' => 2,
            'name' => 'q',
            'url_key' => 'q',
            'parent_id' => 1,
            'path' => '1/2',
            'url_path' => 'q',
            'level' => 1,
            'children_count' => 0,
            'status' => 1
        ));

        $this->_testCategoryNode(3, array(
            'entity_id' => 3,
            'name' => 'w',
            'url_key' => 'w',
            'parent_id' => 1,
            'path' => '1/3',
            'url_path' => 'w',
            'level' => 1,
            'children_count' => 3,
            'status' => 1
        ));

        $this->_testCategoryNode(4, array(
            'entity_id' => 4,
            'name' => 'e',
            'url_key' => 'e',
            'parent_id' => 3,
            'path' => '1/3/4',
            'url_path' => 'w/e',
            'level' => 2,
            'children_count' => 2,
            'status' => 1
        ));

        $this->_testCategoryNode(5, array(
            'entity_id' => 5,
            'name' => 'r',
            'url_key' => 'r',
            'parent_id' => 4,
            'path' => '1/3/4/5',
            'url_path' => 'w/e/r',
            'level' => 3,
            'children_count' => 0,
            'status' => 1
        ));

        $this->_testCategoryNode(6, array(
            'entity_id' => 6,
            'name' => 't',
            'url_key' => 't',
            'parent_id' => 4,
            'path' => '1/3/4/6',
            'url_path' => 'w/e/t',
            'level' => 3,
            'children_count' => 0,
            'status' => 1
        ));
    }


    /**
     * Mock get parents
     *
     * @param array $ids        Categories IDs
     * @param array $statuses   Categories statuses
     * @param int   $selfStatus Self status
     *
     * @return void
     */
    private function _mockGetParents($ids, $statuses, $selfStatus)
    {
        $parentsCollection = new Varien_Data_Collection();

        foreach ($ids as $index => $id) {
            $parentsCollection->addItem(new Varien_Object(array(
                'id' => $id,
                'entity_id' => $id,
                'status' => $statuses[$index]
            )));
        }

        $dataHelperMock = $this->getHelperMock('oggetto_news/data', array('getRootCategoryId'));
        $dataHelperMock->expects($this->any())
            ->method('getRootCategoryId')
            ->will($this->returnValue(1));
        $this->replaceByMock('helper', 'oggetto_news/data', $dataHelperMock);

        $categoryModelMock = $this->getModelMock('oggetto_news/category', array('getParentCategories', 'getStatus'));
        $categoryModelMock->expects($this->any())
            ->method('getParentCategories')
            ->will($this->returnValue($parentsCollection));
        $categoryModelMock->expects($this->any())
            ->method('getStatus')
            ->will($this->returnValue($selfStatus));
        $this->replaceByMock('model', 'oggetto_news/category', $categoryModelMock);
    }


    /**
     * Test get enable status path with self enable
     *
     * @return void
     */
    public function testGetEnableStatusPathWithSelfEnable()
    {
        $this->_mockGetParents(array(1, 2, 3), array(0, 1, 1), 1);
        $this->assertTrue((bool)Mage::getModel('oggetto_news/category')->getStatusPath());
    }

    /**
     * Test get disable status path with self enable
     *
     * @return void
     */
    public function testGetDisableStatusPathWithSelfEnable()
    {
        $this->_mockGetParents(array(1, 2, 3), array(0, 0, 1), 1);
        $this->assertFalse(Mage::getModel('oggetto_news/category')->getStatusPath());
    }

    /**
     * Test get enable status path with self disable
     *
     * @return void
     */
    public function testGetEnableStatusPathWithSelfDisable()
    {
        $this->_mockGetParents(array(1, 2, 3), array(0, 1, 1), 0);
        $this->assertFalse((bool)Mage::getModel('oggetto_news/category')->getStatusPath());
    }

    /**
     * Test get disable status path with self disable
     *
     * @return void
     */
    public function testGetDisableStatusPathWithSelfDisable()
    {
        $this->_mockGetParents(array(1, 2, 3), array(0, 0, 0), 0);
        $this->assertFalse(Mage::getModel('oggetto_news/category')->getStatusPath());
    }
}
