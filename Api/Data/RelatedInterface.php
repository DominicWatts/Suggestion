<?php
/**
 * Copyright © 2023 All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace PixieMedia\Suggestion\Api\Data;

interface RelatedInterface
{

    const UPDATED_AT = 'updated_at';
    const RELATED_IDS = 'related_ids';
    const STORE_ID = 'store_id';
    const CREATED_AT = 'created_at';
    const STATUS = 'status';
    const RELATED_ID = 'related_id';
    const PARENT_SKU = 'parent_sku';

    /**
     * Get related_id
     * @return string|null
     */
    public function getRelatedId();

    /**
     * Set related_id
     * @param string $relatedId
     * @return \PixieMedia\Suggestion\Related\Api\Data\RelatedInterface
     */
    public function setRelatedId($relatedId);

    /**
     * Get parent_sku
     * @return string|null
     */
    public function getParentProductId();

    /**
     * Set parent_sku
     * @param string $parentProductId
     * @return \PixieMedia\Suggestion\Related\Api\Data\RelatedInterface
     */
    public function setParentProductId($parentProductId);

    /**
     * Get related_ids
     * @return string|null
     */
    public function getRelatedIds();

    /**
     * Set related_ids
     * @param string $relatedIds
     * @return \PixieMedia\Suggestion\Related\Api\Data\RelatedInterface
     */
    public function setRelatedIds($relatedIds);

    /**
     * Get status
     * @return string|null
     */
    public function getStatus();

    /**
     * Set status
     * @param string $status
     * @return \PixieMedia\Suggestion\Related\Api\Data\RelatedInterface
     */
    public function setStatus($status);

    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created_at
     * @param string $createdAt
     * @return \PixieMedia\Suggestion\Related\Api\Data\RelatedInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * Get updated_at
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * Set updated_at
     * @param string $updatedAt
     * @return \PixieMedia\Suggestion\Related\Api\Data\RelatedInterface
     */
    public function setUpdatedAt($updatedAt);

    /**
     * Get store_id
     * @return string|null
     */
    public function getStoreId();

    /**
     * Set store_id
     * @param string $storeId
     * @return \PixieMedia\Suggestion\Related\Api\Data\RelatedInterface
     */
    public function setStoreId($storeId);
}

