<?php

namespace PixieMedia\Suggestion\Block;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\Registry;
use PixieMedia\Suggestion\Model\ResourceModel\Related\CollectionFactory as RelatedCollectionFactory;

class Suggested extends \Magento\Catalog\Block\Product\ListProduct
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * PixieMedia\Suggestion\Model\ResourceModel\Related\CollectionFactory
     **/
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
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\Data\Helper\PostHelper $postDataHelper
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Framework\Url\Helper\Data $urlHelper
     * @param \Magento\Framework\Registry $registry
     * @param \PixieMedia\Suggestion\Model\ResourceModel\Related\CollectionFactory $relatedCollectionFactory
     * @param \Magento\Catalog\Model\Product\Visibility $productVisibility
     * @param \Magento\CatalogInventory\Helper\Stock $stockHelper
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\Url\Helper\Data $urlHelper,
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
        parent::__construct(
            $context,
            $postDataHelper,
            $layerResolver,
            $categoryRepository,
            $urlHelper,
            $data
        );
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
            }
        }
        if (empty($array)) {
            return [];
        }

        // remove current product
        $currentProduct = $this->registry->registry('current_product');
        unset($array[$currentProduct->getId()]);

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
            ->addAttributeToSelect('*')
            ->addIdFilter($ids)
            ->addAttributeToFilter('status', ['eq' => 1])
            ->setVisibility($this->productVisibility->getVisibleInSiteIds());
        $this->stockHelper->addInStockFilterToCollection($collection);
        $collection->setPageSize(10);
        $collection->setCurPage(1);
        return $collection;
    }
}
