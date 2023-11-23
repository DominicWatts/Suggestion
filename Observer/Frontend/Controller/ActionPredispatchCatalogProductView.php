<?php
/**
 * Copyright © 2023 All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace PixieMedia\Suggestion\Observer\Frontend\Controller;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;

class ActionPredispatchCatalogProductView implements ObserverInterface
{
    protected $_layout;
    protected $_scopeConfig;
    protected $_productRepository;

    public function __construct(
        \Magento\Framework\View\LayoutInterface $layout,
        ScopeConfigInterface $scopeConfig,
        ProductRepositoryInterface $productRepository
    ) {
        $this->_layout = $layout;
        $this->_scopeConfig = $scopeConfig;
        $this->_productRepository = $productRepository;
    }

    public function execute(EventObserver $observer)
    {
        $isEnabled = $this->_scopeConfig->getValue('pixie_suggested/options/enabled', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if ($isEnabled) {
            $productId = $observer->getRequest()->getParam('id');
            try {
                $product = $this->_productRepository->getById($productId);
                if (!$product->isSalable()) {
                    $this->_layout->getUpdate()->addHandle('catalog_product_view_out_of_stock');
                }
            } catch (\Exception $e) {
                // Nothing
            }
        }
    }
}
