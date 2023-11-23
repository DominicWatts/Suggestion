<?php
/**
 * Copyright © 2023 All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace PixieMedia\Suggestion\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface RelatedRepositoryInterface
{
    /**
     * Save Related
     * @param \PixieMedia\Suggestion\Api\Data\RelatedInterface $related
     * @return \PixieMedia\Suggestion\Api\Data\RelatedInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \PixieMedia\Suggestion\Api\Data\RelatedInterface $related
    );

    /**
     * Retrieve Related
     * @param string $relatedId
     * @return \PixieMedia\Suggestion\Api\Data\RelatedInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($relatedId);

    /**
     * Retrieve Related matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \PixieMedia\Suggestion\Api\Data\RelatedSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Related
     * @param \PixieMedia\Suggestion\Api\Data\RelatedInterface $related
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \PixieMedia\Suggestion\Api\Data\RelatedInterface $related
    );

    /**
     * Delete Related by ID
     * @param string $relatedId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($relatedId);
}
