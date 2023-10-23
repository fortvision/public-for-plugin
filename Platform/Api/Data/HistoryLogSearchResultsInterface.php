<?php

namespace Fortvision\Platform\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface HistoryLogSearchResultsInterface
 * @package Fortvision\Platform\Api\Data
 */
interface HistoryLogSearchResultsInterface extends SearchResultsInterface
{
    /**
     * @return HistoryLogInterface[]
     */
    public function getItems();

    /**
     * @param HistoryLogInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
