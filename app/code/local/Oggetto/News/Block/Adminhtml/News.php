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
 * News admin block
 *
 * @category    Oggetto
 * @package     Oggetto_News
 */
class Oggetto_News_Block_Adminhtml_News
    extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    /**
     * Constructor
     *
     * @return Oggetto_News_Block_Adminhtml_News
     */
    public function __construct()
    {
        $this->_controller = 'adminhtml_news';
        $this->_blockGroup = 'oggetto_news';
        parent::__construct();
        $this->_headerText = Mage::helper('oggetto_news')->__('News');
        $this->_updateButton('add', 'label', Mage::helper('oggetto_news')->__('Add News'));

    }
}
