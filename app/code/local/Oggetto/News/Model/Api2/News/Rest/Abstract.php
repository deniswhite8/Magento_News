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
 * News rest api abstract model
 *
 * @category    Oggetto
 * @package     Oggetto_News
 */
class Oggetto_News_Model_Api2_News_Rest_Abstract
    extends Mage_Api2_Model_Resource
{
    /**
     * Retrieve news list
     *
     * @return array
     */
    public function _retrieveCollection()
    {
        $newsCollection = Mage::getResourceModel('oggetto_news/news_collection');

        $limit = $this->getRequest()->getParam('limit');
        $page = $this->getRequest()->getParam('page');
        $categoryId = $this->getRequest()->getParam('category');

        $newsCollection->setPageSize($limit);
        $newsCollection->setCurPage($page);
        if ($categoryId) {
            $newsCollection->addCategoryFilter($categoryId);
        }

        $response = [];
        foreach ($newsCollection as $news) {
            $response[] = [
                'entity_id'    => $news->getId(),
                'name'         => $news->getName(),
                'text'         => $news->getText(),
                'status'       => $news->getStatus(),
                'created_at'   => $news->getCreatedAt(),
                'category_ids' => $news->getCategoriesIds()
            ];
        }

        return $response;
    }

    /**
     * Retrieve news entity
     *
     * @return array
     */
    public function _retrieve()
    {
        $newsId = $this->getRequest()->getParam('entity_id');
        $news = Mage::getModel('oggetto_news/news')->load($newsId);

        $response = [
            'entity_id'    => $news->getId(),
            'name'         => $news->getName(),
            'text'         => $news->getText(),
            'status'       => $news->getStatus(),
            'created_at'   => $news->getCreatedAt(),
            'category_ids' => $news->getCategoriesIds(),
        ];

        if (!$response['entity_id']) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }

        return $response;
    }

    /**
     * Delete news
     *
     * @return void
     */
    public function _delete()
    {
        $this->_critical(self::RESOURCE_METHOD_NOT_ALLOWED);
    }

    /**
     * Create news
     *
     * @return void
     */
    public function _create()
    {
        $this->_critical(self::RESOURCE_METHOD_NOT_ALLOWED);
    }

    /**
     * Update news
     *
     * @return void
     */
    public function _update()
    {
        $this->_critical(self::RESOURCE_METHOD_NOT_ALLOWED);
    }
}
