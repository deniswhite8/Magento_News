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
 * News - Category relation indexer model
 *
 * @category    Oggetto
 * @package     Oggetto_News
 */
class Oggetto_News_Model_Indexer_Relation
    extends Mage_Index_Model_Indexer_Abstract
{
    /**
     * @var array $_matchedEntities Matched entities
     */
    protected $_matchedEntities = [
        Oggetto_News_Model_News::ENTITY => [
            Mage_Index_Model_Event::TYPE_REINDEX,
            Mage_Index_Model_Event::TYPE_SAVE,
            Mage_Index_Model_Event::TYPE_MASS_ACTION
        ],
        Oggetto_News_Model_Category::ENTITY => [
            Mage_Index_Model_Event::TYPE_REINDEX,
            Mage_Index_Model_Event::TYPE_SAVE,
            Mage_Index_Model_Event::TYPE_MASS_ACTION
        ]
    ];

    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('oggetto_news/indexer_relation');
    }

    /**
     * Get Indexer name
     *
     * @return string
     */
    public function getName()
    {
        return Mage::helper('oggetto_news')->__('News - Category relation');
    }


    /**
     * Get Indexer description
     *
     * @return string
     */
    public function getDescription()
    {
        return Mage::helper('oggetto_news')->__('News - Category news relation indexer');
    }

    /**
     * Register indexer required data inside event object
     *
     * @param Mage_Index_Model_Event $event Index event
     * @return void
     */
    protected function _registerEvent(Mage_Index_Model_Event $event)
    {
        /* @var Mage_Core_Model_Abstract $entity */
        $entity = $event->getDataObject();
        if ($entity->getId()) {
            if ($entity instanceof Oggetto_News_Model_News) {
                $event->setData('news_id', $entity->getId());
            } elseif ($entity instanceof Oggetto_News_Model_Category) {
                $event->setData('category_id', $entity->getId());
            }
        }
    }

    /**
     * Process event based on event state data
     *
     * @param Mage_Index_Model_Event $event Index event
     * @return void
     */
    protected function _processEvent(Mage_Index_Model_Event $event)
    {
        if ($event->getData('news_id') || $event->getData('category_id')) {
            $this->callEventHandler($event);
        }
    }

    /**
     * Index is available
     *
     * @return bool
     */
    public function indexIsAvailable()
    {
        return Mage::getModel('index/indexer')
            ->getProcessByCode('oggetto_news')
            ->getStatus() == Mage_Index_Model_Process::STATUS_PENDING;
    }



    /**
     * Get news id by path from index
     *
     * @param string $path News url path
     * @return int
     */
    public function getNewsIdByPathFromIndex($path)
    {
        return $this->getResource()->getNewsIdByPathFromIndex($path);
    }

    /**
     * Get category id by path from index
     *
     * @param string $path News url path
     * @return int
     */
    public function getCategoryIdByPathFromIndex($path)
    {
        return $this->getResource()->getCategoryIdByPathFromIndex($path);
    }
}
