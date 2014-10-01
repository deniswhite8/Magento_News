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
 * Router test
 *
 * @category    Oggetto
 * @package     Oggetto_News
 */
class Oggetto_News_Test_Controller_Router
    extends EcomDev_PHPUnit_Test_Case_Controller
{

    /**
     * Test init routes
     *
     * @return void
     */
    public function testInitControllerRouters()
    {
        $observer = new Varien_Event_Observer();
        $event = new Varien_Event();
        $observer->setEvent($event);
        $event->setFront(new Mage_Core_Controller_Varien_Front());

        $router = new Oggetto_News_Controller_Router();
        $router->initControllerRouters($observer);

        $this->assertEquals($router, $observer->getEvent()->getFront()->getRouter('oggetto_news'));
    }


    /**
     * Test one match case
     *
     * @param string $pathInfo       Path url
     * @param bool   $result         Expected result
     * @param string $controllerName Controller name
     * @param string $actionName     Action name
     * @param int    $id             Id
     *
     * @return void
     */
    private function _testMatchCase($pathInfo, $result, $controllerName = '', $actionName = '', $id = null)
    {
        $router = new Oggetto_News_Controller_Router();
        $request = new Zend_Controller_Request_Http();

        $request->setPathInfo($pathInfo);
        $matchResult = $router->match($request);

        if ($result) {
            $this->assertTrue($matchResult);

            $this->assertEquals('news', $request->getModuleName());
            $this->assertEquals($controllerName, $request->getControllerName());
            $this->assertEquals($actionName, $request->getActionName());
            if ($id) {
                $this->assertEquals(array('id' => $id), $request->getParams());
            }
        } else {
            $this->assertFalse($matchResult);
        }
    }

    private function _entitiesModelMock()
    {
        $newsMock = $this->getModelMock('oggetto_news/news', array('getIdByUrlPath', 'getIdByUrlKey'));
        $newsMock->expects($this->any())
            ->method('getIdByUrlPath')
            ->will($this->returnCallback(function ($path) {
                return $path == 'cool/path/for/good/category/newsKey' ? 1 : null;
            }));
        $newsMock->expects($this->any())
            ->method('getIdByUrlKey')
            ->will($this->returnCallback(function ($key) {
                return $key == 'newsKey' ? 2 : null;
            }));
        $this->replaceByMock('model', 'oggetto_news/news', $newsMock);


        $categoryMock = $this->getModelMock('oggetto_news/news', array('getIdByUrlPath'));
        $categoryMock->expects($this->any())
            ->method('getIdByUrlPath')
            ->will($this->returnCallback(function ($path) {
                return $path == 'cool/path/for/good/category' ? 3 : null;
            }));
        $this->replaceByMock('model', 'oggetto_news/category', $categoryMock);
    }


    /**
     * Test incorrect match
     *
     * @loadFixture storeConfig
     *
     * @return void
     */
    public function testIncorrectMatch()
    {
        $this->_entitiesModelMock();
        $this->_testMatchCase('/lolo', false);
    }

    /**
     * Test news list match
     *
     * @loadFixture storeConfig
     *
     * @return void
     */
    public function testNewsListMatch()
    {
        $this->_entitiesModelMock();
        $this->_testMatchCase('/all_news', true, 'news', 'index');
    }

    /**
     * Test categories list match
     *
     * @loadFixture storeConfig
     *
     * @return void
     */
    public function testCategoriesListMatch()
    {
        $this->_entitiesModelMock();
        $this->_testMatchCase('/all_categories', true, 'category', 'index');
    }

    /**
     * Test news path match
     *
     * @loadFixture storeConfig
     *
     * @return void
     */
    public function testNewsPathMatch()
    {
        $this->_entitiesModelMock();
        $this->_testMatchCase('/news/cool/path/for/good/category/newsKey.html', true, 'news', 'view', 1);
    }

    /**
     * Test news key match
     *
     * @loadFixture storeConfig
     *
     * @return void
     */
    public function testNewsKeyMatch()
    {
        $this->_entitiesModelMock();
        $this->_testMatchCase('/news/newsKey.html', true, 'news', 'view', 2);
    }

    /**
     * Test category path match
     *
     * @loadFixture storeConfig
     *
     * @return void
     */
    public function testCategoryPathMatch()
    {
        $this->_entitiesModelMock();
        $this->_testMatchCase('/news/cool/path/for/good/category', true, 'category', 'view', 3);
    }
}
