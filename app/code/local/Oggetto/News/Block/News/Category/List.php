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
 * News News categories list block
 *
 * @category    Oggetto
 * @package     Oggetto_News
 */
class Oggetto_News_Block_News_Category_List
    extends Oggetto_News_Block_Category_List
{

    /**
     * Initialize
     *
     * @return Oggetto_News_Block_News_Category_List
     */
    public function __construct()
    {
        parent::__construct();
        $news = $this->getNews();
        if ($news) {
            $this->getCategories()->addNewsFilter($news->getId());
            $this->getCategories()->unshiftOrder('related_news.position', 'ASC');
        }
    }

//    /**
//     * Prepare the layout
//     *
//     * @return Oggetto_News_Block_News_Category_List
//     */
//    protected function _prepareLayout(){
//        return $this;
//    }

    /**
     * Get the current news
     *
     * @return Oggetto_News_Model_News|null
     */
    public function getNews()
    {
        return Mage::registry('current_news');
    }
}
