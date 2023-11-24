<?php
/**
 * Copyright © 2023 All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace PixieMedia\Suggestion\Controller\Adminhtml\Related;

use Magento\Framework\Exception\LocalizedException;

class Save extends \Magento\Backend\App\Action
{
    protected $dataPersistor;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
    ) {
        $this->dataPersistor = $dataPersistor;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue('general');
        if ($data) {
            $data['product'] = $this->getRequest()->getPostValue('product');
            $id = $this->getRequest()->getParam('related_id');

            $model = $this->_objectManager->create(\PixieMedia\Suggestion\Model\Related::class)->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This OOS Suggestion no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }

            if (isset($data['store_id'])) {
                $data['store_id'] = implode(",", $data['store_id']);
            }

            if (isset($data['product'])) {
                $data['related_ids'] = $data['product']['list'];
                /* @phpstan-ignore-next-line */
                unset($data['product']);
            }

            $model->setData($data);

            try {
                $model->save();
                $this->messageManager->addSuccessMessage(__('You saved the OOS Suggestion.'));
                $this->dataPersistor->clear('pixiemedia_suggestion_related');

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['related_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __(
                        'Something went wrong while saving the OOS Suggestion.'
                    )
                );
            }

            $this->dataPersistor->set('pixiemedia_suggestion_related', $data);
            return $resultRedirect->setPath('*/*/edit', ['related_id' => $this->getRequest()->getParam('related_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
