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
 * News - News category relation resource model collection
 *
 * @category    Oggetto
 * @package     Oggetto_News
 */
class Oggetto_News_Model_Resource_News_Category_Collection
    extends Oggetto_News_Model_Resource_Category_Collection
{

    /**
     * Remember if fields have been joined
     * @var bool
     */
    protected $_joinedFields = false;

    /**
     * Join the link table
     *
     * @return Oggetto_News_Model_Resource_News_Category_Collection
     */
    public function joinFields()
    {
        if (!$this->_joinedFields) {
            $this->getSelect()->join(
                array('related' => $this->getTable('oggetto_news/news_category')),
                'related.category_id = main_table.entity_id',
                array('position')
            );
            $this->_joinedFields = true;
        }
        return $this;
    }

    /**
     * Get categories ids by news
     *
     * @param Oggetto_News_Model_News|int $news News
     * @return array
     */
    public function getCategoriesIdsByNews($news)
    {
        if ($news instanceof Oggetto_News_Model_News) {
            $news = $news->getId();
        }

        $readAdapter = Mage::getSingleton('core/resource')->getConnection('core_read');
        $select = $readAdapter
            ->select()
            ->from($this->getTable('oggetto_news/news_category'), 'category_id')
            ->where('news_id = ?', $news);

        return $readAdapter->fetchAll($select);
    }

    /**
     * Add news filter
     *
     * @param Oggetto_News_Model_News | int $news News
     * @return Oggetto_News_Model_Resource_News_Category_Collection
     */
    public function addNewsFilter($news)
    {
        if ($news instanceof Oggetto_News_Model_News) {
            $news = $news->getId();
        }
        if (!$this->_joinedFields) {
            $this->joinFields();
        }
        $this->getSelect()->where('related.news_id = ?', $news);
        return $this;
    }
}
