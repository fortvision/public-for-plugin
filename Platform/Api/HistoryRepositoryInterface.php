<?php

namespace Fortvision\Platform\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Interface HistoryRepositoryInterface
 * @package Fortvision\Platform\Api
 */
interface HistoryRepositoryInterface
{
    /**
     * @param int $historyId
     * @return Data\HistoryInterface
     * @throws NoSuchEntityException
     */
    public function getById($historyId);

    /**
     * @param Data\HistoryInterface $history
     * @return Data\HistoryInterface
     */
    public function save(Data\HistoryInterface $history);

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return Data\HistorySearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * @param Data\HistoryInterface $history
     * @return bool
     */
    public function delete(Data\HistoryInterface $history);

    /**
     * @param Data\HistoryInterface $historyId
     * @return bool
     */
    public function deleteById($historyId);
}
