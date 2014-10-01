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
 * News category resource model test
 *
 * @category    Oggetto
 * @package     Oggetto_News
 */
class Oggetto_News_Test_Model_Resource_Category
    extends EcomDev_PHPUnit_Test_Case_Controller
{

    /**
     * Test category node
     *
     * @param int   $id           Category id
     * @param array $expectedData Expected data
     */
    private function _testCategoryNode($id, $expectedData)
    {
        $categoryNodeData = Mage::getModel('oggetto_news/category')->load($id)->getData();

        if (empty($expectedData)) {
            $this->assertEmpty($categoryNodeData);
            return;
        }

        foreach ($expectedData as $key => $value) {
            $this->assertEquals($value, $categoryNodeData[$key]);
        }
    }

    /**
     * Test delete children
     *
     * @loadFixture category
     *
     * @return void
     */
    public function testDeleteChildren()
    {
        $object = new Varien_Object(array(
            'path' => '1/2'
        ));

        $categoryResourceModel = Mage::getResourceModel('oggetto_news/category');
        $categoryResourceModel->deleteChildren($object);

        $this->_testCategoryNode(1, array(
            'entity_id' => 1,
            'path' => '1'
        ));
        $this->_testCategoryNode(2, array(
            'entity_id' => 2,
            'path' => '1/2'
        ));
        $this->_testCategoryNode(3, array());
        $this->_testCategoryNode(4, array());
        $this->_testCategoryNode(5, array());

        $this->assertEquals($object->getDeletedChildrenIds(), array(3, 4, 5));
    }

    /**
     * Test get children count
     *
     * @loadFixture category
     *
     * @return void
     */
    public function testGetChildrenCount()
    {
        $categoryResourceModel = Mage::getResourceModel('oggetto_news/category');
        $this->assertEquals(4, $categoryResourceModel->getChildrenCount(1));
    }

    /**
     * Test get parent categories
     *
     * @loadFixture category
     *
     * @return void
     */
    public function testGetParentCategories()
    {
        $categoryResourceModel = Mage::getResourceModel('oggetto_news/category');
        $parents = $categoryResourceModel->getParentCategories(Mage::getModel('oggetto_news/category')->load(3));

        $this->assertCount(3, $parents);
        $this->assertEquals(1, $parents[1]->getId());
        $this->assertEquals(2, $parents[2]->getId());
    }

    /**
     * Test get children categories
     *
     * @loadFixture category
     *
     * @return void
     */
    public function testGetChildrenCategories()
    {
        $categoryResourceModel = Mage::getResourceModel('oggetto_news/category');
        $children = $categoryResourceModel->getChildrenCategories(Mage::getModel('oggetto_news/category')->load(2))
            ->getItems();

        $this->assertCount(2, $children);
        $this->assertEquals(3, $children[3]->getId());
        $this->assertEquals(4, $children[4]->getId());
    }

    /**
     * Test get children ids recursive
     *
     * @loadFixture category
     *
     * @return void
     */
    public function testGetChildrenRecursive()
    {
        $categoryResourceModel = Mage::getResourceModel('oggetto_news/category');
        $children = $categoryResourceModel->getChildren(Mage::getModel('oggetto_news/category')->load(2), true);

        $this->assertEquals(array(3, 4, 5), $children);
    }

    /**
     * Test get children ids not recorsive
     *
     * @loadFixture category
     *
     * @return void
     */
    public function testGetChildrenNotRecursive()
    {
        $categoryResourceModel = Mage::getResourceModel('oggetto_news/category');
        $children = $categoryResourceModel->getChildren(Mage::getModel('oggetto_news/category')->load(2), false);

        $this->assertEquals(array(3, 4), $children);
    }

    /**
     * Test return all children ids of category (with category id)
     *
     * @return void
     */
    public function testGetAllChildren()
    {
        $categoryResourceModel = $this->getResourceModelMock('oggetto_news/category', array('getChildren'));
        $categoryResourceModel->expects($this->any())
            ->method('getChildren')
            ->will($this->returnValue(array(1, 2, 3)));
        $this->replaceByMock('resource_model', 'oggetto_news/category', $categoryResourceModel);

        $category = Mage::getModel('oggetto_news/category');
        $category->setId(4);

        $children = $categoryResourceModel->getAllChildren($category);
        $this->assertEquals(array(4, 1, 2, 3), $children);
    }

    /**
     * Test check news category is forbidden to delete.
     *
     * @return void
     */
    public function testIsForbiddenToDelete()
    {
        $helperDataMock = $this->getHelperMock('oggetto_news/data', array('getRootCategoryId'));
        $helperDataMock->expects($this->any())
            ->method('getRootCategoryId')
            ->will($this->returnValue(1));
        $this->replaceByMock('helper', 'oggetto_news/data', $helperDataMock);

        $categoryResourceModel = Mage::getResourceModel('oggetto_news/category');

        $this->assertTrue($categoryResourceModel->isForbiddenToDelete(1));
        $this->assertFalse($categoryResourceModel->isForbiddenToDelete(2));
    }

    /**
     * Test get id by url path
     *
     * @return void
     */
    public function testGetIdByUrlPath()
    {
        $categoryResourceModel = Mage::getResourceModel('oggetto_news/category');

        $this->assertEquals(2, $categoryResourceModel->getIdByUrlPath('lol'));
        $this->assertFalse($categoryResourceModel->getIdByUrlPath('sdgfsdg'));
    }
}