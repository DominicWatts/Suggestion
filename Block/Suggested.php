<?php

namespace PixieMedia\Suggestion\Block;

use PixieMedia\Suggestion\Model\RelatedFactory;
use PixieMedia\Suggestion\Model\ResourceModel\Related\CollectionFactory as RelatedCollectionFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;

class Suggested extends Template
{
    protected $registry;
    protected $relatedCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $productVisibility;

    /**
     * @var \Magento\CatalogInventory\Helper\Stock
     */
    protected $stockHelper;

    /**
     * @var ProductCollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @param Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \PixieMedia\Suggestion\Model\ResourceModel\Related\CollectionFactory $relatedCollectionFactory
     * @param \Magento\Catalog\Model\Product\Visibility $productVisibility
     * @param \Magento\CatalogInventory\Helper\Stock $stockHelper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Registry $registry,
        RelatedCollectionFactory $relatedCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        \Magento\CatalogInventory\Helper\Stock $stockHelper,
        ProductCollectionFactory $productCollectionFactory,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->relatedCollectionFactory = $relatedCollectionFactory;
        $this->productVisibility = $productVisibility;
        $this->stockHelper = $stockHelper;
        $this->productCollectionFactory = $productCollectionFactory;
        parent::__construct($context, $data);
    }

    public function getSuggested()
    {
        $currentProduct = $this->registry->registry('current_product');
        if (!$currentProduct) {
            return []; // Return empty array if current product is not found
        }

        $relatedCollection = $this->relatedCollectionFactory
            ->create()
            ->addStatusFilter(1)
            ->addParentSkuFilter($currentProduct->getSku())
            ->addStoreFilter();

        return $relatedCollection;
    }

    /**
     * Get all suggested - so all products from all suggested rules
     * @return void
     */
    public function getAllSuggested()
    {
        $suggested = $this->getSuggested();
        $array = [];
        foreach ($suggested as $suggest) {
            foreach ($suggest->getProductsIds() as $id) {
                $array[$id] = $id;
            };
        }
        return $this->getVisibleProductsById($array);
    }

    /**
     * Get products collection
     * @return CollectionFactory
     */
    public function getVisibleProductsById($ids = [])
    {
        $collection = $this->productCollectionFactory
            ->create()
            ->addIdFilter($ids)
            ->addAttributeToFilter('status', ['eq' => 1])
            ->setVisibility($this->productVisibility->getVisibleInSiteIds());
        $this->stockHelper->addInStockFilterToCollection($collection);
        $collection->setPageSize(10);
        $collection->setCurPage(1);
        return $collection;
    }
}
