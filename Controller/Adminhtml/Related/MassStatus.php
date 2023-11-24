<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace PixieMedia\Suggestion\Controller\Adminhtml\Related;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use PixieMedia\Suggestion\Controller\Adminhtml\Related;
use PixieMedia\Suggestion\Model\ResourceModel\Related\CollectionFactory as CollectionFactory;
use Magento\Framework\Registry;

/**
 * Updates status for a batch of products.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class MassStatus extends Related implements HttpPostActionInterface
{
    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        Registry $coreRegistry
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * Update suggestion(s) status action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute(): Redirect
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $status = (int) $this->getRequest()->getParam('status');

        $notProcessedCount = $processedCount = 0;
        try {
            foreach ($collection as $suggestion) {
                $suggestion->setStatus($status);
                $suggestion->save();
                $processedCount++;
            }
        } catch (Exception $e) {
            $this->getMessageManager()->addExceptionMessage($e);
            $notProcessedCount++;
        }

        $this->getMessageManager()->addSuccessMessage(
            __('A total of %1 record(s) have been deleted.', $processedCount)
        );

        if ($notProcessedCount > 0) {
            $this->getMessageManager()->addErrorMessage(
                __('A total of %1 record(s) haven\'t been deleted.', $notProcessedCount)
            );
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/');
    }
}
