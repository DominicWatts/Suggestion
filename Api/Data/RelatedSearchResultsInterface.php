<?php
/**
 * Copyright © 2023 All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace PixieMedia\Suggestion\Api\Data;

interface RelatedSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Related list.
     * @return \PixieMedia\Suggestion\Api\Data\RelatedInterface[]
     */
    public function getItems();

    /**
     * Set parent_sku list.
     * @param \PixieMedia\Suggestion\Api\Data\RelatedInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

