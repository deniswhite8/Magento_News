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
 * Router
 *
 * @category    Oggetto
 * @package     Oggetto_News
 */
class Oggetto_News_Controller_Router
    extends Mage_Core_Controller_Varien_Router_Abstract
{

    /**
     * Init routes
     *
     * @param Varien_Event_Observer $observer Observer
     * @return Oggetto_News_Controller_Router
     */
    public function initControllerRouters($observer)
    {
        $front = $observer->getEvent()->getFront();
        $front->addRouter('oggetto_news', $this);
        return $this;
    }

    /**
     * Validate and match entities and modify request
     *
     * @param Zend_Controller_Request_Http $request Request
     * @return bool
     */
    public function match(Zend_Controller_Request_Http $request)
    {
        if (!Mage::isInstalled()) {
            Mage::app()->getFrontController()->getResponse()
                ->setRedirect(Mage::getUrl('install'))
                ->sendResponse();
            exit;
        }

        $fullUrlPath = trim($request->getPathInfo(), '/');
        $fullUrlPathArray = explode('/', $fullUrlPath);
        $firstWord = array_shift($fullUrlPathArray);
        $urlPath = implode('/', $fullUrlPathArray);

        if ($firstWord != 'news') {
            return false;
        }

        $newsModel = Mage::getModel('oggetto_news/news');
        $categoryModel = Mage::getModel('oggetto_news/category');

        $newsId = $newsModel->checkUrlPath($urlPath, Mage::app()->getStore()->getId());
        $categoryId = $categoryModel->checkUrlPath($urlPath, Mage::app()->getStore()->getId());

        $result = false;
        if ($categoryId) {
            if (!$categoryModel->load($categoryId)->getStatusPath()) {
                return false;
            }
            $request->setModuleName('news')
                ->setControllerName('category')
                ->setActionName('view')
                ->setParam('id', $categoryId);
            $request->setAlias(
                Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
                $fullUrlPath
            );
            $result = true;
        } else if ($newsId) {
            $request->setModuleName('news')
                ->setControllerName('news')
                ->setActionName('view')
                ->setParam('id', $newsId);
            $request->setAlias(
                Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
                $fullUrlPath
            );
            $result = true;
        }

        return $result;
    }
}
