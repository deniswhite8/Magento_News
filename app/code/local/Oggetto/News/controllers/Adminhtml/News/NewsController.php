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
 * News admin controller
 *
 * @category    Oggetto
 * @package     Oggetto_News
 */
class Oggetto_News_Adminhtml_News_NewsController
    extends Oggetto_News_Controller_Adminhtml_News
{

    /**
     * Init the news
     *
     * @return Oggetto_News_Model_News
     */
    protected function _initNews()
    {
        $newsId = (int)$this->getRequest()->getParam('id');
        $news = Mage::getModel('oggetto_news/news');
        if ($newsId) {
            $news->load($newsId);
        }
        Mage::register('current_news', $news);
        return $news;
    }

    /**
     * Default action
     *
     * @return void
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_title(Mage::helper('oggetto_news')->__('News'))
            ->_title(Mage::helper('oggetto_news')->__('News'));
        $this->renderLayout();
    }

    /**
     * Grid action
     *
     * @return void
     */
    public function gridAction()
    {
        $this->loadLayout()->renderLayout();
    }

    /**
     * Edit news - action
     *
     * @return void
     */
    public function editAction()
    {
        $newsId = $this->getRequest()->getParam('id');
        $news = $this->_initNews();
        if ($newsId && !$news->getId()) {
            $this->_getSession()->addError(Mage::helper('oggetto_news')->__('This news no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }
        $data = Mage::getSingleton('adminhtml/session')->getNewsData(true);
        if (!empty($data)) {
            $news->setData($data);
        }
        Mage::register('news_data', $news);
        $this->loadLayout();
        $this->_title(Mage::helper('oggetto_news')->__('News'))
            ->_title(Mage::helper('oggetto_news')->__('News'));
        if ($news->getId()) {
            $this->_title($news->getName());
        } else {
            $this->_title(Mage::helper('oggetto_news')->__('Add news'));
        }
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        $this->renderLayout();
    }

    /**
     * New news action
     *
     * @return void
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Save news - action
     *
     * @return void
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost('news')) {
            try {
                $news = $this->_initNews();
                $news->addData($data);
                $categories = $this->getRequest()->getPost('category_ids', -1);
                if ($categories != -1) {
                    $categories = explode(',', $categories);
                    $categories = array_unique($categories);
                    $news->setCategoriesData($categories);
                }
                $news->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('oggetto_news')->__('News was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $news->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setNewsData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('oggetto_news')->__('There was a problem saving the news.'));
                Mage::getSingleton('adminhtml/session')->setNewsData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('oggetto_news')->__('Unable to find news to save.'));
        $this->_redirect('*/*/');
    }

    /**
     * Delete news - action
     *
     * @return void
     */
    public function deleteAction()
    {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $news = Mage::getModel('oggetto_news/news');
                $news->setId($this->getRequest()->getParam('id'))->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('oggetto_news')->__('News was successfully deleted.'));
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('oggetto_news')->__('There was an error deleting news.'));
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                Mage::logException($e);
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('oggetto_news')->__('Could not find news to delete.'));
        $this->_redirect('*/*/');
    }

    /**
     * Mass delete news - action
     *
     * @return void
     */
    public function massDeleteAction()
    {
        $newsIds = $this->getRequest()->getParam('news');
        if (!is_array($newsIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('oggetto_news')->__('Please select news to delete.'));
        } else {
            try {
                foreach ($newsIds as $newsId) {
                    $news = Mage::getModel('oggetto_news/news');
                    $news->setId($newsId)->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('oggetto_news')->__('Total of %d news were successfully deleted.', count($newsIds)));
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('oggetto_news')->__('There was an error deleting news.'));
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Mass status change - action
     *
     * @return void
     */
    public function massStatusAction()
    {
        $newsIds = $this->getRequest()->getParam('news');
        if (!is_array($newsIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('oggetto_news')->__('Please select news.'));
        } else {
            try {
                foreach ($newsIds as $newsId) {
                    $news = Mage::getSingleton('oggetto_news/news')->load($newsId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess($this->__('Total of %d news were successfully updated.', count($newsIds)));
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('oggetto_news')->__('There was an error updating news.'));
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Get categories action
     *
     * @return void
     */
    public function categoriesAction()
    {
        $this->_initNews();
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Get child categories  action
     *
     * @return void
     */
    public function categoriesJsonAction()
    {
        $this->_initNews();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('oggetto_news/adminhtml_news_edit_tab_category')
                ->getCategoryChildrenJson($this->getRequest()->getParam('category'))
        );
    }

    /**
     * Export as csv - action
     *
     * @return void
     */
    public function exportCsvAction()
    {
        $fileName = 'news.csv';
        $content = $this->getLayout()->createBlock('oggetto_news/adminhtml_news_grid')->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export as MsExcel - action
     *
     * @return void
     */
    public function exportExcelAction()
    {
        $fileName = 'news.xls';
        $content = $this->getLayout()->createBlock('oggetto_news/adminhtml_news_grid')->getExcelFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export as xml - action
     *
     * @return void
     */
    public function exportXmlAction()
    {
        $fileName = 'news.xml';
        $content = $this->getLayout()->createBlock('oggetto_news/adminhtml_news_grid')->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Check if admin has permissions to visit related pages
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/oggetto_news/news');
    }
}
