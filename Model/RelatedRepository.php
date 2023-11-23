<?php
/**
 * Copyright © 2023 All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace PixieMedia\Suggestion\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use PixieMedia\Suggestion\Api\Data\RelatedInterface;
use PixieMedia\Suggestion\Api\Data\RelatedInterfaceFactory;
use PixieMedia\Suggestion\Api\Data\RelatedSearchResultsInterfaceFactory;
use PixieMedia\Suggestion\Api\RelatedRepositoryInterface;
use PixieMedia\Suggestion\Model\ResourceModel\Related as ResourceRelated;
use PixieMedia\Suggestion\Model\ResourceModel\Related\CollectionFactory as RelatedCollectionFactory;

class RelatedRepository implements RelatedRepositoryInterface
{
    /**
     * @var Related
     */
    protected $searchResultsFactory;

    /**
     * @var ResourceRelated
     */
    protected $resource;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var RelatedInterfaceFactory
     */
    protected $relatedFactory;

    /**
     * @var RelatedCollectionFactory
     */
    protected $relatedCollectionFactory;


    /**
     * @param ResourceRelated $resource
     * @param RelatedInterfaceFactory $relatedFactory
     * @param RelatedCollectionFactory $relatedCollectionFactory
     * @param RelatedSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourceRelated $resource,
        RelatedInterfaceFactory $relatedFactory,
        RelatedCollectionFactory $relatedCollectionFactory,
        RelatedSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->relatedFactory = $relatedFactory;
        $this->relatedCollectionFactory = $relatedCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function save(RelatedInterface $related)
    {
        try {
            $this->resource->save($related);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the related: %1',
                $exception->getMessage()
            ));
        }
        return $related;
    }

    /**
     * @inheritDoc
     */
    public function get($relatedId)
    {
        $related = $this->relatedFactory->create();
        $this->resource->load($related, $relatedId);
        if (!$related->getId()) {
            throw new NoSuchEntityException(__('Related with id "%1" does not exist.', $relatedId));
        }
        return $related;
    }

    /**
     * @inheritDoc
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->relatedCollectionFactory->create();

        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $items = [];
        foreach ($collection as $model) {
            $items[] = $model;
        }

        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function delete(RelatedInterface $related)
    {
        try {
            $relatedModel = $this->relatedFactory->create();
            $this->resource->load($relatedModel, $related->getRelatedId());
            $this->resource->delete($relatedModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Related: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($relatedId)
    {
        return $this->delete($this->get($relatedId));
    }
}
