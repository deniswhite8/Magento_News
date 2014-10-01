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
 * News model test
 *
 * @category    Oggetto
 * @package     Oggetto_News
 */
class Oggetto_News_Test_Model_News
    extends EcomDev_PHPUnit_Test_Case_Controller
{
    /**
     * Test entity code
     *
     * @return void
     */
    public function testEntityCode()
    {
        $this->assertEquals('oggetto_news_news', Oggetto_News_Model_News::ENTITY);
    }

    /**
     * Test cache tag
     *
     * @return void
     */
    public function testCacheTag()
    {
        $this->assertEquals('oggetto_news_news', Oggetto_News_Model_News::CACHE_TAG);
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

        $model = Mage::getModel('oggetto_news/news');
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

        $model = Mage::getModel('oggetto_news/news');
        $model->setId(12);
        $model->save();

        $this->assertNull($model->getCreatedAt());
        $this->assertEquals($dateString, $model->getUpdatedAt());
    }

    /**
     * Test get url for news with url key
     *
     * @loadFixture storeConfig
     *
     * @return void
     */
    public function testGetUrlForNewsWithUrlKey()
    {
        $model = Mage::getModel('oggetto_news/news');
        $model->setUrlKey('key');

        $this->assertEquals(Mage::getBaseUrl() . 'news/key.html', $model->getNewsUrl());
    }

    /**
     * Test get url for news without url key
     *
     * @loadFixture storeConfig
     *
     * @return void
     */
    public function testGetUrlForNewsWithoutUrlKey()
    {
        $model = Mage::getModel('oggetto_news/news');
        $model->setId(12);

        $this->assertEquals(Mage::getBaseUrl() . 'news/news/view/id/12/', $model->getNewsUrl());
    }

    /**
     * Test get id by url key
     *
     * @return void
     */
    public function testGetIdByUrlKey()
    {
        $model = Mage::getModel('oggetto_news/news');

        $newsResourceModelMock = $this->getResourceModelMock('oggetto_news/news', array('getIdByUrlKey'));
        $newsResourceModelMock->expects($this->any())
            ->method('getIdByUrlKey')
            ->will($this->returnArgument(0));
        $this->replaceByMock('resource_model', 'oggetto_news/news', $newsResourceModelMock);

        $this->assertEquals('key', $model->getIdByUrlKey('key'));
    }

    /**
     * Test get id by url path
     *
     * @return void
     */
    public function testGetIdByUrlPath()
    {
        $model = Mage::getModel('oggetto_news/news');

        $newsResourceModelMock = $this->getResourceModelMock('oggetto_news/news', array('getIdByUrlPath'));
        $newsResourceModelMock->expects($this->any())
            ->method('getIdByUrlPath')
            ->will($this->returnArgument(0));
        $this->replaceByMock('resource_model', 'oggetto_news/news', $newsResourceModelMock);

        $this->assertEquals('p/a/t/h', $model->getIdByUrlPath('p/a/t/h'));
    }

    /**
     * Test get text
     *
     * @return void
     */
    public function testGetText()
    {
        $model = Mage::getModel('oggetto_news/news');
        $model->setText('<img src="{{media url="123.png"}}" alt="" />');

        $this->assertEquals('<img src="' . Mage::getUrl('media') . '123.png' . '" alt="" />', $model->getText());
    }

    /**
     * Test after save
     *
     * @return void
     */
    public function testAfterSave()
    {
        $param = null;

        $newsCategoryMock = $this->getModelMock('oggetto_news/news_category', array('saveNewsRelation'));
        $newsCategoryMock->expects($this->any())
            ->method('saveNewsRelation')
            ->will($this->returnCallback(function ($_param) use (&$param) {
                $param = $_param;
            }));
        $this->replaceByMock('singleton', 'oggetto_news/news_category', $newsCategoryMock);

        $model = Mage::getModel('oggetto_news/news');
        $model->setId(12);
        $model->save();

        $this->assertEquals($model, $param);
    }

    /**
     * Test get category instance
     *
     * @return void
     */
    public function testGetCategoryInstance()
    {
        $this->assertInstanceOf('Oggetto_News_Model_News_Category',
            Mage::getModel('oggetto_news/news')->getCategoryInstance());
    }

    /**
     * Mock categories collection
     *
     * @return Varien_Data_Collection
     */
    private function _mockCategoriesCollection()
    {
        $collection = new Varien_Data_Collection();
        $collection->addItem(new Varien_Object(array(
            'entity_id' => 1,
            'name' => 'name 1',
            'text' => 'text 1'
        )));
        $collection->addItem(new Varien_Object(array(
            'entity_id' => 2,
            'name' => 'name 2',
            'text' => 'text 2'
        )));

        $newsCategoryMock = $this->getModelMock('oggetto_news/news_category', array('getCategoriesCollection'));
        $newsCategoryMock->expects($this->any())
            ->method('getCategoriesCollection')
            ->will($this->returnValue($collection));
        $this->replaceByMock('singleton', 'oggetto_news/news_category', $newsCategoryMock);

        return $collection;
    }

    /**
     * Test get selected categories
     *
     * @return void
     */
    public function testGetSelectedCategories()
    {
        $collection = $this->_mockCategoriesCollection();
        $model = Mage::getModel('oggetto_news/news');

        $this->assertEquals($collection->getItems(), $model->getSelectedCategories());
        $this->assertEquals($collection->getItems(), $model->getData('selected_categories'));
    }

    /**
     * Test get selected categories collection
     *
     * @return void
     */
    public function testGetSelectedCategoriesCollection()
    {
        $collection = $this->_mockCategoriesCollection();
        $model = Mage::getModel('oggetto_news/news');

        $this->assertEquals($collection, $model->getSelectedCategoriesCollection());
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
        ), Mage::getModel('oggetto_news/news')->getDefaultValues());
    }
}