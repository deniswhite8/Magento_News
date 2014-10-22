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
     * Prepare url
     *
     * @param string $url  Url
     * @param string $type Model type
     *
     * @return string
     */
    private function _prepareUrl($url, $type)
    {
        $helper = Mage::helper('oggetto_news/data');
        $fullUrlPath = trim($url, '/');

        $urlPrefix = $helper->getUrlPrefix($type);
        $urlSuffix = $helper->getUrlSuffix($type);

        $prefixCount = 0;
        $suffixCount = 0;

        $urlPath = preg_replace('/^'. preg_quote($urlPrefix, '/') . '/', '', $fullUrlPath, 1, $prefixCount);
        $urlPath = preg_replace('/'. preg_quote($urlSuffix, '/') . '$/', '', $urlPath, 1, $suffixCount);

        if ($prefixCount == 1 && $suffixCount == 1) {
            return $urlPath;
        }

        return null;
    }

    /**
     * Check for main list
     *
     * @param Zend_Controller_Request_Http $request     Request
     * @param string                       $url         Url
     * @param string                       $type        Model type
     *
     * @return bool
     */
    private function _checkForMainList($request, $url, $type)
    {
        $fullUrlPath = trim($url, '/');

        if ($fullUrlPath == trim(Mage::helper('oggetto_news/data')->getUrlRewriteList($type), '/')) {
            $request->setModuleName('news')
                ->setControllerName($type)
                ->setActionName('index');

            $request->setAlias(
                Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
                $url
            );

            return true;
        }

        return false;
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

        $fullUrl = $request->getPathInfo();

        if ($this->_checkForMainList($request, $fullUrl, 'news') ||
            $this->_checkForMainList($request, $fullUrl, 'category')) {

            return true;
        }

        $indexerModel = Mage::getModel('oggetto_news/indexer_relation');
        if ($indexerModel->indexIsAvailable()) {
            $possibleСases = array(
                array('news' => $indexerModel->getNewsIdByPathFromIndex($fullUrl)),
                array('category' => $indexerModel->getCategoryIdByPathFromIndex($fullUrl)),
            );
        } else {
            $newsUrlPath = $this->_prepareUrl($fullUrl, 'news');
            $categoryUrlPath = $this->_prepareUrl($fullUrl, 'category');

            if (!$newsUrlPath && !$categoryUrlPath) {
                return false;
            }

            $newsModel = Mage::getModel('oggetto_news/news');
            $categoryModel = Mage::getModel('oggetto_news/category');

            $possibleСases = array(
                array('news' => $newsModel->getIdByUrlPath($newsUrlPath)),
                array('news' => $newsModel->getIdByUrlKey($newsUrlPath)),
                array('category' => $categoryModel->getIdByUrlPath($categoryUrlPath)),
            );
        }


        foreach ($possibleСases as $case) {
            if ($this->_processCase($request, key($case), reset($case), $fullUrl)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check case
     *
     * @param Zend_Controller_Request_Http $request     Request
     * @param string                       $type        Model type
     * @param string                       $id          Model id
     * @param string                       $fullUrlPath Full URL path
     *
     * @return bool
     */
    private function _processCase($request, $type, $id, $fullUrlPath)
    {
        if (!$id) {
            return false;
        }

        $request->setModuleName('news')
                ->setControllerName($type)
                ->setActionName('view')
                ->setParam('id', $id);

        $request->setAlias(
            Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
            $fullUrlPath
        );

        return true;
    }
}
