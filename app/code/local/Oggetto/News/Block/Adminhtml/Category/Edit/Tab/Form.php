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
 * News category edit form tab
 *
 * @category    Oggetto
 * @package     Oggetto_News
 */
class Oggetto_News_Block_Adminhtml_Category_Edit_Tab_Form
    extends Mage_Adminhtml_Block_Widget_Form
{

    /**
     * Prepare the form
     *
     * @return Oggetto_News_Block_Adminhtml_Category_Edit_Tab_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('category_');
        $form->setFieldNameSuffix('category');
        $this->setForm($form);
        $fieldset = $form->addFieldset('category_form', array('legend' => Mage::helper('oggetto_news')->__('News category')));
        if (!$this->getCategory()->getId()) {
            $parentId = $this->getRequest()->getParam('parent');
            if (!$parentId) {
                $parentId = Mage::helper('oggetto_news/category')->getRootCategoryId();
            }
            $fieldset->addField('path', 'hidden', array(
                'name' => 'path',
                'value' => $parentId
            ));
        } else {
            $fieldset->addField('id', 'hidden', array(
                'name' => 'id',
                'value' => $this->getCategory()->getId()
            ));
            $fieldset->addField('path', 'hidden', array(
                'name' => 'path',
                'value' => $this->getCategory()->getPath()
            ));
        }

        $fieldset->addField('name', 'text', array(
            'label' => Mage::helper('oggetto_news')->__('Name'),
            'name' => 'name',
            'required' => true,
            'class' => 'required-entry',

        ));
        $fieldset->addField('url_key', 'text', array(
            'label' => Mage::helper('oggetto_news')->__('Url key'),
            'name' => 'url_key',
            'note' => Mage::helper('oggetto_news')->__('Relative to Website Base URL')
        ));
        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('oggetto_news')->__('Status'),
            'name' => 'status',
            'values' => array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('oggetto_news')->__('Enabled'),
                ),
                array(
                    'value' => 0,
                    'label' => Mage::helper('oggetto_news')->__('Disabled'),
                ),
            ),
        ));
        $form->addValues($this->getCategory()->getData());
        return parent::_prepareForm();
    }

    /**
     * Get the current news category
     *
     * @return Oggetto_News_Model_Category
     */
    public function getCategory()
    {
        return Mage::registry('category');
    }
}
