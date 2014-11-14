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
 * News rest api admin model
 *
 * @category    Oggetto
 * @package     Oggetto_News
 */
class Oggetto_News_Model_Api2_News_Rest_Admin_V1
    extends Oggetto_News_Model_Api2_News_Rest_Abstract
{
    /**
     * Delete news
     *
     * @return void
     */
    public function _delete()
    {
        $newsId = $this->getRequest()->getParam('entity_id');
        $news = Mage::getModel('oggetto_news/news')->setId($newsId);

        try {
            $news->delete();
        } catch (Mage_Core_Exception $e) {
            $this->_critical($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        } catch (Exception $e) {
            $this->_critical(self::RESOURCE_INTERNAL_ERROR);
        }
    }

    /**
     * Prepare category ids
     *
     * @param Oggetto_News_Model_News $news News
     * @param array                   $data Object data, with category_ids
     *
     * @return void
     */
    private function _setCategoryIds($news, $data)
    {
        if (is_array($data['category_ids']) && !empty($data['category_ids'])) {
            $categoryIds = [];
            foreach ($data['category_ids'] as $categoryId) {
                $categoryIds[] = $categoryId['category_id'];
            }
            $news->setCategoriesData($categoryIds);
        }
    }

    /**
     * Create news
     *
     * @param array $data Request news data
     * @return string
     */
    public function _create(array $data)
    {
        $news = Mage::getModel('oggetto_news/news');
        unset($data[$news->getIdFieldName()]);
        $news->setData($data);

        try {
            $this->_setCategoryIds($news, $data);
            $news->save();
        } catch (Mage_Core_Exception $e) {
            $this->_critical($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        } catch (Exception $e) {
            $this->_critical(self::RESOURCE_INTERNAL_ERROR);
        }

        return Mage::getBaseUrl() . $news->getSpecialNewsUrl();
    }

    /**
     * Create news
     *
     * @param array $data Request news data
     * @return void
     */
    public function _update(array $data)
    {
        $news = Mage::getModel('oggetto_news/news');
        unset($data[$news->getIdFieldName()]);
        $news->setData($data);

        try {
            $news->load($this->getRequest()->getParam('entity_id'))
                ->addData($data);
            $this->_setCategoryIds($news, $data);
            $news->save();
        } catch (Mage_Core_Exception $e) {
            $this->_critical($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        } catch (Exception $e) {
            echo $e->getMessage();die;
            $this->_critical(self::RESOURCE_INTERNAL_ERROR);
        }
    }
}
