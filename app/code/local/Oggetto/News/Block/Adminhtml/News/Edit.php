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
 * News admin edit form
 *
 * @category    Oggetto
 * @package     Oggetto_News
 */
class Oggetto_News_Block_Adminhtml_News_Edit
    extends Mage_Adminhtml_Block_Widget_Form_Container
{

    /**
     * Constructor
     *
     * @return Oggetto_News_Block_Adminhtml_News_Edit
     */
    public function __construct()
    {
        parent::__construct();
        $this->_blockGroup = 'oggetto_news';
        $this->_controller = 'adminhtml_news';
        $this->_updateButton('save', 'label', Mage::helper('oggetto_news')->__('Save News'));
        $this->_updateButton('delete', 'label', Mage::helper('oggetto_news')->__('Delete News'));
        $this->_addButton('saveandcontinue', array(
            'label' => Mage::helper('oggetto_news')->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class' => 'save',
        ), -100);
        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    /**
     * Get the edit form header
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('current_news') && Mage::registry('current_news')->getId()) {
            return Mage::helper('oggetto_news')->__("Edit News '%s'", $this->escapeHtml(Mage::registry('current_news')->getName()));
        } else {
            return Mage::helper('oggetto_news')->__('Add News');
        }
    }
}
