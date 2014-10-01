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
 * News list block
 *
 * @category    Oggetto
 * @package     Oggetto_News
 */
class Oggetto_News_Block_News_List
    extends Mage_Core_Block_Template
{
    /**
     * Initialize
     *
     * @return Oggetto_News_Block_News_List
     */
    public function __construct()
    {
        parent::__construct();
        $news = Mage::getResourceModel('oggetto_news/news_collection')
            ->addFieldToFilter('status', 1);
        $news->setOrder('name', 'asc');
        $this->setNews($news);
    }

    /**
     * Prepare the layout
     *
     * @return Oggetto_News_Block_News_List
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $pager = $this->getLayout()->createBlock('page/html_pager', 'oggetto_news.news.html.pager');
        $pager->setAvailableLimit(array(10 => 10));
        $pager->setCollection($this->getNews());
        $this->setChild('pager', $pager);
        $this->getNews()->load();
        return $this;
    }

    /**
     * Get the pager html
     *
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
}
