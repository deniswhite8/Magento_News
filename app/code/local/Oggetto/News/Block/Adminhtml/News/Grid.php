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
 * News admin grid block
 *
 * @category    Oggetto
 * @package     Oggetto_News
 */
class Oggetto_News_Block_Adminhtml_News_Grid
    extends Mage_Adminhtml_Block_Widget_Grid
{

    /**
     * Constructor
     *
     * @return Oggetto_News_Block_Adminhtml_News_Grid
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('newsGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * Prepare collection
     *
     * @return Oggetto_News_Block_Adminhtml_News_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('oggetto_news/news')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare grid collection
     *
     * @return Oggetto_News_Block_Adminhtml_News_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header' => Mage::helper('oggetto_news')->__('Id'),
            'index' => 'entity_id',
            'type' => 'number'
        ));
        $this->addColumn('name', array(
            'header' => Mage::helper('oggetto_news')->__('Name'),
            'align' => 'left',
            'index' => 'name',
        ));
        $this->addColumn('status', array(
            'header' => Mage::helper('oggetto_news')->__('Status'),
            'index' => 'status',
            'type' => 'options',
            'options' => array(
                '1' => Mage::helper('oggetto_news')->__('Enabled'),
                '0' => Mage::helper('oggetto_news')->__('Disabled'),
            )
        ));
        $this->addColumn('url_key', array(
            'header' => Mage::helper('oggetto_news')->__('URL key'),
            'index' => 'url_key',
        ));
        $this->addColumn('created_at', array(
            'header' => Mage::helper('oggetto_news')->__('Created at'),
            'index' => 'created_at',
            'width' => '120px',
            'type' => 'datetime',
        ));
        $this->addColumn('action',
            array(
                'header' => Mage::helper('oggetto_news')->__('Action'),
                'width' => '100',
                'type' => 'action',
                'getter' => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('oggetto_news')->__('Edit'),
                        'url' => array('base' => '*/*/edit'),
                        'field' => 'id'
                    )
                ),
                'filter' => false,
                'is_system' => true,
                'sortable' => false,
            ));
        $this->addExportType('*/*/exportCsv', Mage::helper('oggetto_news')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('oggetto_news')->__('Excel'));
        $this->addExportType('*/*/exportXml', Mage::helper('oggetto_news')->__('XML'));
        return parent::_prepareColumns();
    }

    /**
     * Prepare mass action
     *
     * @return Oggetto_News_Block_Adminhtml_News_Grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('news');
        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('oggetto_news')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('oggetto_news')->__('Are you sure?')
        ));
        $this->getMassactionBlock()->addItem('status', array(
            'label' => Mage::helper('oggetto_news')->__('Change status'),
            'url' => $this->getUrl('*/*/massStatus', array('_current' => true)),
            'additional' => array(
                'status' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('oggetto_news')->__('Status'),
                    'values' => array(
                        '1' => Mage::helper('oggetto_news')->__('Enabled'),
                        '0' => Mage::helper('oggetto_news')->__('Disabled'),
                    )
                )
            )
        ));
        return $this;
    }

    /**
     * Get the row url
     *
     * @param Oggetto_News_Model_News $row Row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    /**
     * Get the grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

    /**
     * After collection load
     *
     * @return Oggetto_News_Block_Adminhtml_News_Grid
     */
    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }
}
