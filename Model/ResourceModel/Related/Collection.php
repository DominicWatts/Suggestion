<?php
/**
 * Copyright © 2023 All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace PixieMedia\Suggestion\Model\ResourceModel\Related;

use Magento\Eav\Model\Config;
use Magento\Eav\Model\Entity\Type;
use Magento\Eav\Model\ResourceModel\Entity\Attribute;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Psr\Log\LoggerInterface;
use PixieMedia\Suggestion\Api\Data\RelatedInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class Collection extends AbstractCollection
{
    /**
     * @inheritDoc
     */
    protected $_idFieldName = 'related_id';

    /**
     * @var int
     */
    protected $_storeId;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var TimezoneInterface
     */
    protected $localeDate;

    /**
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param Config $eavConfig
     * @param AdapterInterface $connection
     * @param AbstractDb $resource
     * @codeCoverageIgnore
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        StoreManagerInterface $storeManager,
        TimezoneInterface $localeDate,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        $this->_storeManager = $storeManager;
        $this->localeDate = $localeDate;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            \PixieMedia\Suggestion\Model\Related::class,
            \PixieMedia\Suggestion\Model\ResourceModel\Related::class
        );
    }

    /**
     * Set random groups order
     * @return $this
     */
    public function setRandomOrder()
    {
        $this->getConnection()->orderRand($this->getSelect());
        return $this;
    }

    /**
     * Filter collection by status
     * @param string $status
     * @return $this
     */
    public function addStatusFilter($status = null)
    {
        if (empty($status)) {
            $this->addFieldToFilter(RelatedInterface::STATUS, ['null' => true]);
        } else {
            $this->addFieldToFilter(RelatedInterface::STATUS, $status);
        }
        return $this;
    }

    /**
     * Filter collection by sku
     * @param string $sku
     * @return $this
     */
    public function addParentSkuFilter($sku = null)
    {
        if (!empty($sku)) {
            $this->addFieldToFilter(
                [RelatedInterface::PARENT_SKU, RelatedInterface::PARENT_SKU],
                [
                    ['eq' => $sku],
                    ['null' => true],
                ]
            );
        }
        return $this;
    }

    /**
     * Add store availability filter. Include availability product for store website.
     *
     * @param null|string|bool|int|Store $store
     * @return $this
     */
    public function addStoreFilter($store = null)
    {
        if ($store === null) {
            $store = $this->getStoreId();
        }

        try {
            $store = $this->_storeManager->getStore($store);
        } catch (NoSuchEntityException $e) {
            return $this;
        }

        if ($store->getId()) {
            $this->addFieldToFilter(
                RelatedInterface::STORE_ID,
                [
                    'or' => [
                        0 => ['finset' => Store::DEFAULT_STORE_ID],
                        1 => ['finset' => $store->getId()],
                    ]
                ],
                'left'
            );
            return $this;
        }
        return $this;
    }

    /**
     * Return current store id
     * @return int
     */
    public function getStoreId()
    {
        if ($this->_storeId === null) {
            $this->setStoreId($this->_storeManager->getStore()->getId());
        }
        return $this->_storeId;
    }

    /**
     * Set store scope ID
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->_storeId = $storeId;
        return $this;
    }
}
