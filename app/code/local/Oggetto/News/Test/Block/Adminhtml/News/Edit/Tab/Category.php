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
 * News - category relation edit block test
 *
 * @category    Oggetto
 * @package     Oggetto_News
 */
class Oggetto_News_Test_Block_Adminhtml_News_Edit_Tab_Category extends EcomDev_PHPUnit_Test_Case_Controller
{
    protected $_categoryIds = null;
    protected $_selectedNodes = null;

    /**
     * Test constructor
     *
     * @return void
     */
    public function testConstructor()
    {
        $block = new Oggetto_News_Block_Adminhtml_News_Edit_Tab_Category();
        $this->assertEquals('oggetto_news/news/edit/tab/category.phtml', $block->getTemplate());
    }

    /**
     * Test get news
     *
     * @return void
     */
    public function testGetNews()
    {
        Mage::unregister('current_news');
        $news = Mage::getModel('oggetto_news/news');
        Mage::register('current_news', $news);
        $block = new Oggetto_News_Block_Adminhtml_News_Edit_Tab_Category();

        $this->assertEquals($news, $block->getNews());
    }

    /**
     * Test get category ids
     *
     * @return void
     */
    public function testGetCategoryIds()
    {
        $categoryCollection = new Varien_Data_Collection();

        $categoryCollection->addItem(new Varien_Object(['id' => 42]));
        $categoryCollection->addItem(new Varien_Object(['id' => 11]));
        $categoryCollection->addItem(new Varien_Object(['id' => 15]));

        $block = $this->getBlockMock('oggetto_news/adminhtml_news_edit_tab_category', ['getNews']);

        $news = $this->getModelMock('oggetto_news/news', ['getSelectedCategories']);

        $news->expects($this->once())
            ->method('getSelectedCategories')
            ->will($this->returnValue($categoryCollection));

        $block->expects($this->once())
            ->method('getNews')
            ->will($this->returnValue($news));

        $this->assertEquals([42, 11, 15], $block->getCategoryIds());

    }

    /**
     * Test get ids string
     *
     * @return void
     */
    public function testGetIdsString()
    {
        $block = $this->getBlockMock('oggetto_news/adminhtml_news_edit_tab_category', ['getCategoryIds']);

        $block->expects($this->once())
            ->method('getCategoryIds')
            ->will($this->returnValue(array(42, 11, 15)));

        $this->assertEquals('42,11,15', $block->getIdsString());
    }

    /**
     * Mock block for get root node tests
     *
     * @param Oggetto_News_Model_Category $category Category
     * @return EcomDev_PHPUnit_Mock_Proxy
     */
    private function _mockBlockForGetRootNodeTests($category)
    {
        $block = $this->getBlockMock('oggetto_news/adminhtml_news_edit_tab_category', ['getRoot', 'getCategoryIds']);

        $block->expects($this->once())
            ->method('getRoot')
            ->will($this->returnValue($category));
        $block->expects($this->once())
            ->method('getCategoryIds')
            ->will($this->returnValue([1, 2, 3]));

        return $block;
    }

    /**
     * Test get root node checked
     *
     * @return void
     */
    public function testGetRootNodeChecked()
    {
        $category = Mage::getModel('oggetto_news/category')->setId(2)->setChecked(false);
        $block = $this->_mockBlockForGetRootNodeTests($category);

        $this->assertEquals($category, $block->getRootNode());
        $this->assertTrue($category->getChecked());
    }

    /**
     * Test get root node not checked without id
     *
     * @return void
     */
    public function testGetRootNodeNotCheckedWithoutId()
    {
        $category = Mage::getModel('oggetto_news/category')->unsId()->setChecked(false);
        $block = $this->_mockBlockForGetRootNodeTests($category);

        $this->assertEquals($category, $block->getRootNode());
        $this->assertFalse($category->getChecked());
    }

    /**
     * Test get root node not checked out of array
     *
     * @return void
     */
    public function testGetRootNodeNotCheckedOutOfArray()
    {
        $category = Mage::getModel('oggetto_news/category')->setId(42)->setChecked(false);
        $block = $this->_mockBlockForGetRootNodeTests($category);

        $this->assertEquals($category, $block->getRootNode());
        $this->assertFalse($category->getChecked());
    }

    /**
     * Test get root with correct parameters
     *
     * @return void
     */
    public function testGetRootWithCorrectParameters()
    {
        $block = $this->getBlockMock('oggetto_news/adminhtml_news_edit_tab_category', ['getNode']);
        $category = Mage::getModel('oggetto_news/category')->setId(1);

        $block->expects($this->once())
            ->method('getNode')
            ->with($category, 2)
            ->will($this->returnValue(3));

        $this->assertEquals(3, $block->getRoot($category, 2));
    }

    /**
     * Test get root from registry
     *
     * @return void
     */
    public function testGetRootFromRegistry()
    {
        $category = Mage::getModel('oggetto_news/category')->setId(1);
        Mage::unregister('category_root');
        Mage::register('category_root', $category);

        $block = new Oggetto_News_Block_Adminhtml_News_Edit_Tab_Category();
        $this->assertEquals($category, $block->getRoot());
    }

    /**
     * Test get root from registry
     *
     * @return void
     */
    public function testGetRootFromTree()
    {
        Mage::unregister('category_root');

        $rootId = 1;
        $ids = [1, 2, 3];
        $result = 42;

        $dataHelperMock = $this->getHelperMock('oggetto_news/data');
        $dataHelperMock->expects($this->once())
            ->method('getRootCategoryId')
            ->will($this->returnValue($rootId));
        $this->replaceByMock('helper', 'oggetto_news/data', $dataHelperMock);

        $block = $this->getBlockMock('oggetto_news/adminhtml_news_edit_tab_category', ['getSelectedCategoryPathIds', 'getCategory']);
        $block->expects($this->once())
            ->method('getSelectedCategoryPathIds')
            ->with($rootId)
            ->will($this->returnValue($ids));
        $block->expects($this->once())
            ->method('getCategory')
            ->will($this->returnValue(null));

        $categoryTreeMock = $this->getResourceModelMock('oggetto_news/category_tree', ['loadByIds', 'addCollectionData', 'getNodeById']);
        $categoryTreeMock->expects($this->once())
            ->method('loadByIds')
            ->with($ids, false, false)
            ->will($this->returnSelf());
        $categoryTreeMock->expects($this->once())
            ->method('addCollectionData')
            ->will($this->returnSelf());
        $categoryTreeMock->expects($this->once())
            ->method('getNodeById')
            ->with($rootId)
            ->will($this->returnValue($result));
        $this->replaceByMock('resource_model', 'oggetto_news/category_tree', $categoryTreeMock);

        $realResult = $block->getRoot();
        $this->assertEquals($result, $realResult);
        $this->assertEquals(Mage::registry('category_root'), $result);
    }


    /**
     * Mock block for get children json tests
     *
     * @param mixed $getNodeByIdReturnValue getNodeByIdR method return value
     * @param int   $categoryId             Category id
     *
     * @return Oggetto_News_Block_Adminhtml_News_Edit_Tab_Category
     */
    private function _mockBlockForGetChildrenJsonTests($categoryId, $getNodeByIdReturnValue)
    {
        $category = Mage::getModel('oggetto_news/category')->load($categoryId);

        $block = $this->getBlockMock('oggetto_news/adminhtml_news_edit_tab_category', ['getRoot', 'getTree', 'getNodeById', '_getNodeJson']);

        $block->expects($this->once())
            ->method('getRoot')
            ->with($category, $categoryId)
            ->will($this->returnSelf());

        $block->expects($this->once())
            ->method('getTree')
            ->with()
            ->will($this->returnSelf());

        $block->expects($this->once())
            ->method('getNodeById')
            ->with($categoryId)
            ->will($this->returnValue($getNodeByIdReturnValue));

        return $block;
    }

    /**
     * Test get category children json with null node
     *
     * @loadFixture testGetCategoryChildrenJson
     *
     * @return void
     */
    public function testGetCategoryChildrenJsonWithNullNode()
    {
        $categoryId = 1;
        $block = $this->_mockBlockForGetChildrenJsonTests($categoryId, null);
        $this->assertEquals('[]', $block->getCategoryChildrenJson($categoryId));
    }


    /**
     * Test get category children json without children
     *
     * @loadFixture testGetCategoryChildrenJson
     *
     * @return void
     */
    public function testGetCategoryChildrenJsonWithoutChildren()
    {
        $node = $this->getMock('Varien_Object', ['hasChildren']);
        $node->expects($this->once())
            ->method('hasChildren')
            ->will($this->returnValue(false));

        $categoryId = 1;
        $block = $this->_mockBlockForGetChildrenJsonTests($categoryId, $node);

        $this->assertEquals('[]', $block->getCategoryChildrenJson($categoryId));
    }

    /**
     * Test get category children json with children
     *
     * @loadFixture testGetCategoryChildrenJson
     *
     * @return void
     */
    public function testGetCategoryChildrenJsonWithChildren()
    {
        $node = $this->getMock('Varien_Object', ['hasChildren', 'getChildren']);
        $node->expects($this->once())
            ->method('hasChildren')
            ->will($this->returnValue(true));
        $node->expects($this->once())
            ->method('getChildren')
            ->will($this->returnValue(['1', '2', '3']));

        $categoryId = 1;
        $block = $this->_mockBlockForGetChildrenJsonTests($categoryId, $node);

        $block->expects($this->any())
            ->method('_getNodeJson')
            ->will($this->returnArgument(0));

        $this->assertEquals('["1","2","3"]', $block->getCategoryChildrenJson($categoryId));
    }


    /**
     * Test get loaded tree url
     *
     * @return void
     */
    public function testGetLoadedTreeUrl()
    {
        $block = $this->getBlockMock('oggetto_news/adminhtml_news_edit_tab_category', ['getUrl']);

        $block->expects($this->once())
            ->method('getUrl')
            ->with('*/*/categoriesJson', array('_current' => true))
            ->will($this->returnValue('123'));

        $this->assertEquals('123', $block->getLoadTreeUrl());
    }


    /**
     * Test get selected category path ids without results
     *
     * @return void
     */
    public function testGetSelectedCategoryPathIdsWithoutResults()
    {
        $block = $this->getBlockMock('oggetto_news/adminhtml_news_edit_tab_category', ['getCategoryIds']);

        $block->expects($this->once())
            ->method('getCategoryIds')
            ->will($this->returnValue([]));

        $this->assertEquals([], $block->getSelectedCategoryPathIds());
    }


    /**
     * Test get selected category path ids without results
     *
     * @loadFixture testGetSelectedCategoryPathIds
     *
     * @return void
     */
    public function testGetSelectedCategoryPathIdsWithoutRootId()
    {
        $block = $this->getBlockMock('oggetto_news/adminhtml_news_edit_tab_category', ['getCategoryIds']);
        $block->expects($this->once())
            ->method('getCategoryIds')
            ->will($this->returnValue([1, 2, 3]));

        $items = [new Varien_Object([
            'path_ids' => [1, 2, 3]
        ]), new Varien_Object([
            'path_ids' => [4, 5, 6]
        ]), new Varien_Object([
            'path_ids' => [7, 8, 9]
        ])];

        $categoryCollectionMock = $this->getResourceModelMock('oggetto_news/category_collection', ['getIterator']);
        $categoryCollectionMock->expects($this->once())
            ->method('getIterator')
            ->will($this->returnValue(new ArrayIterator($items)));
        $this->replaceByMock('resource_model', 'oggetto_news/category_collection', $categoryCollectionMock);

        $this->assertEquals([1, 2, 3, 4, 5, 6, 7, 8, 9], $block->getSelectedCategoryPathIds());
    }

    /**
     * Test get selected category path ids with results
     *
     * @loadFixture testGetSelectedCategoryPathIds
     *
     * @return void
     */
    public function testGetSelectedCategoryPathIdsWithRootId()
    {
        $block = $this->getBlockMock('oggetto_news/adminhtml_news_edit_tab_category', ['getCategoryIds']);
        $block->expects($this->exactly(2))
            ->method('getCategoryIds')
            ->will($this->returnValue([1, 2, 3]));

        $items = [new Varien_Object([
            'path_ids' => [1, 2, 3]
        ]), new Varien_Object([
            'path_ids' => [4, 5, 6]
        ]), new Varien_Object([
            'path_ids' => [7, 8, 9]
        ])];

        $categoryCollectionMock = $this->getResourceModelMock('oggetto_news/category_collection', ['getIterator']);
        $categoryCollectionMock->expects($this->exactly(2))
            ->method('getIterator')
            ->will($this->returnValue(new ArrayIterator($items)));
        $this->replaceByMock('resource_model', 'oggetto_news/category_collection', $categoryCollectionMock);

        $this->assertEquals([1, 2, 3], $block->getSelectedCategoryPathIds(2));
        $this->assertEquals([], $block->getSelectedCategoryPathIds(42));
    }

    /**
     * Test build node name
     *
     * @return void
     */
    public function testBuildNodeName()
    {
        $block = $this->getBlockMock('oggetto_news/adminhtml_news_edit_tab_category', ['getUrl']);

        $block->expects($this->once())
            ->method('getUrl')
            ->with('adminhtml/news_category/index', array('id' => 42, 'clear' => 1))
            ->will($this->returnValue('this_is_url'));


        $this->assertEquals('ololo<a target="_blank" href="this_is_url"><em> - Edit</em></a>', $block->buildNodeName(new Varien_Object([
            'id' => 42,
            'name' => 'ololo'
        ])));
    }
}
