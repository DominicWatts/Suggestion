<?php

declare(strict_types=1);

namespace PixieMedia\Suggestion\Controller\Adminhtml\Related;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\UrlRewrite\Controller\Adminhtml\Url\Rewrite as RewriteAction;
use PixieMedia\Suggestion\Block\Adminhtml\Related\Edit\Tab\Product;

class ProductGrid extends RewriteAction implements HttpPostActionInterface, HttpGetActionInterface
{
    /**
     * Ajax messages grid action
     *
     * @return void
     */
    public function execute()
    {
        $this->getResponse()->setBody(
            $this->_view->getLayout()->createBlock(Product::class)->toHtml()
        );
    }
}
