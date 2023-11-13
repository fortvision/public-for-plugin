<?php

namespace Fortvision\Platform\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Interface HistoryLogRepositoryInterface
 * @package Fortvision\Platform\Api
 */
interface HistoryLogRepositoryInterface
{
    /**
     * @param int $logId
     * @return Data\HistoryLogInterface
     * @throws NoSuchEntityException
     */
    public function getById($logId);

    /**
     * @param Data\HistoryLogInterface $log
     * @return Data\HistoryInterface
     */
    public function save(Data\HistoryLogInterface $log);

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return Data\HistoryLogSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * @param Data\HistoryLogInterface $log
     * @return bool
     */
    public function delete(Data\HistoryLogInterface $log);

    /**
     * @param Data\HistoryLogInterface $logId
     * @return bool
     */
    public function deleteById($logId);
}
