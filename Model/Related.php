<?php
/**
 * Copyright © 2023 All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace PixieMedia\Suggestion\Model;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\Model\AbstractModel;
use PixieMedia\Suggestion\Api\Data\RelatedInterface;

class Related extends AbstractModel implements RelatedInterface
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $dateTime;

    /**
     * @var ProductCollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $productVisibility;

    /**
     * @var \Magento\CatalogInventory\Helper\Stock
     */
    protected $stockHelper;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \PixieMedia\Suggestion\Model\ResourceModel\Related $resource
     * @param \PixieMedia\Suggestion\Model\ResourceModel\Related\Collection $resourceCollection
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\Product\Visibility $productVisibility
     * @param \Magento\CatalogInventory\Helper\Stock $stockHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \PixieMedia\Suggestion\Model\ResourceModel\Related $resource,
        \PixieMedia\Suggestion\Model\ResourceModel\Related\Collection $resourceCollection,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        ProductCollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        \Magento\CatalogInventory\Helper\Stock $stockHelper,
        array $data = []
    ) {
        $this->dateTime = $dateTime;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productVisibility = $productVisibility;
        $this->stockHelper = $stockHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Before save
     */
    public function beforeSave()
    {
        $this->setUpdatedAt($this->dateTime->gmtDate());
        if ($this->isObjectNew()) {
            $this->setCreatedAt($this->dateTime->gmtDate());
        }
        return parent::beforeSave();
    }

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\PixieMedia\Suggestion\Model\ResourceModel\Related::class);
    }

    /**
     * @inheritDoc
     */
    public function getRelatedId()
    {
        return $this->getData(self::RELATED_ID);
    }

    /**
     * @inheritDoc
     */
    public function setRelatedId($relatedId)
    {
        return $this->setData(self::RELATED_ID, $relatedId);
    }

    /**
     * @inheritDoc
     */
    public function getParentProductId()
    {
        return $this->getData(self::PARENT_SKU);
    }

    /**
     * @inheritDoc
     */
    public function setParentProductId($parentProductId)
    {
        return $this->setData(self::PARENT_SKU, $parentProductId);
    }

    /**
     * @inheritDoc
     */
    public function getRelatedIds()
    {
        return $this->getData(self::RELATED_IDS);
    }

    /**
     * @inheritDoc
     */
    public function setRelatedIds($relatedIds)
    {
        return $this->setData(self::RELATED_IDS, $relatedIds);
    }

    /**
     * @inheritDoc
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @inheritDoc
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * @inheritDoc
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }

    /**
     * @inheritDoc
     */
    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * @inheritDoc
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * Get products collection
     * @return CollectionFactory
     */
    public function getProductsCollection()
    {
        return $this->productCollectionFactory->create()
            ->addIdFilter(explode('&', (string) $this->getRelatedIds()));
    }

    /**
     * Get products collection
     * @return CollectionFactory
     */
    public function getProductsIds()
    {
        $collection = $this->getProductsCollection();
        return $collection->getAllIds();
    }

    /**
     * Get products collection
     * @return CollectionFactory
     */
    public function getVisibleProducts()
    {
        $collection = $this->getProductsCollection();
        $collection->addAttributeToFilter('status', ['eq' => 1]);
        $collection->setVisibility($this->productVisibility->getVisibleInSiteIds());
        $this->stockHelper->addInStockFilterToCollection($collection);
        return $collection;
    }
}
