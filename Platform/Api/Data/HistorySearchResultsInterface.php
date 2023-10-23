<?php

namespace Fortvision\Platform\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface HistorySearchResultsInterface
 * @package Fortvision\Platform\Api\Data
 */
interface HistorySearchResultsInterface extends SearchResultsInterface
{
    /**
     * @return HistoryInterface[]
     */
    public function getItems();

    /**
     * @param HistoryInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
