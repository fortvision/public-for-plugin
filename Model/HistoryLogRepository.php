<?php

namespace Fortvision\Platform\Model;

use Fortvision\Platform\Api\Data\HistoryInterface;
use Fortvision\Platform\Api\Data\HistoryLogInterface;
use Fortvision\Platform\Api\Data\HistoryLogSearchResultsInterface;
use Fortvision\Platform\Api\Data\HistoryLogSearchResultsInterfaceFactory;
use Fortvision\Platform\Api\HistoryLogRepositoryInterface;
use Fortvision\Platform\Model\ResourceModel\HistoryLog;
use Fortvision\Platform\Model\ResourceModel\HistoryLog\Collection;
use Fortvision\Platform\Model\ResourceModel\HistoryLog\CollectionFactory;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class HistoryLogRepository
 * @package Fortvision\Platform\Model
 */
class HistoryLogRepository implements HistoryLogRepositoryInterface
{
    /**
     * @var HistoryLog
     */
    private $historyLogResource;

    /**
     * @var HistoryLogFactory
     */
    private $historyLogFactory;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var HistoryLogSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var FilterGroupBuilder
     */
    private $filterGroupBuilder;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    private $searchCriteriaBuilderFactory;

    /**
     * HistoryLogRepository constructor.
     * @param HistoryLog $historyLogResource
     * @param HistoryLogFactory $historyLogFactory
     * @param CollectionFactory $collectionFactory
     * @param HistoryLogSearchResultsInterfaceFactory $searchResultsFactory
     * @param FilterBuilder $filterBuilder
     * @param FilterGroupBuilder $filterGroupBuilder
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     */
    public function __construct(
        ResourceModel\HistoryLog $historyLogResource,
        HistoryLogFactory $historyLogFactory,
        CollectionFactory $collectionFactory,
        HistoryLogSearchResultsInterfaceFactory $searchResultsFactory,
        FilterBuilder $filterBuilder,
        FilterGroupBuilder $filterGroupBuilder,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
    ) {
        $this->historyLogResource = $historyLogResource;
        $this->historyLogFactory = $historyLogFactory;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->filterBuilder = $filterBuilder;
        $this->filterGroupBuilder = $filterGroupBuilder;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
    }

    /**
     * @param HistoryLogInterface $historyLog
     * @return HistoryInterface|HistoryLogInterface
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function save(HistoryLogInterface $historyLog)
    {
        $this->historyLogResource->save($historyLog);

        return $historyLog;
    }

    /**
     * @param HistoryLogInterface $historyLog
     * @return bool|HistoryLog
     * @throws \Exception
     */
    public function delete(HistoryLogInterface $historyLog)
    {
        return $this->deleteById($historyLog->getLogId());
    }

    /**
     * @param HistoryLogInterface $logId
     * @return bool|HistoryLog
     * @throws \Exception
     */
    public function deleteById($logId)
    {
        $historyLog = $this->historyLogFactory->create();
        $historyLog->setLogId($logId);

        return $this->historyLogResource->delete($historyLog);
    }

    /**
     * @param int $logId
     * @return HistoryLogInterface
     * @throws NoSuchEntityException
     */
    public function getById($logId)
    {
        $historyLog = $this->historyLogFactory->create();
        $this->historyLogResource->load($historyLog, $logId);
        if (empty($historyLog->getLogId())) {
            throw new NoSuchEntityException(
                __('Unable to find History Log Data with ID "%1"', $logId)
            );
        }

        return $historyLog;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return HistoryLogSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $collection);
        }
        /** @var SortOrder $sortOrder */
        foreach ((array) $searchCriteria->getSortOrders() as $sortOrder) {
            $field = $sortOrder->getField();
            $direction = $sortOrder->getDirection() == SortOrder::SORT_ASC ? SortOrder::SORT_DESC : SortOrder::SORT_DESC;
            $collection->addOrder($field, $direction);
        }

        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());
        $collection->load();

        /** @var HistoryLogSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        $contracts = [];
        foreach ($collection as $contract) {
            $contracts[] = $contract;
        }
        $searchResults->setItems($contracts);
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    /**
     * @param FilterGroup $group
     * @param Collection $collection
     */
    private function addFilterGroupToCollection(FilterGroup $group, Collection $collection)
    {
        $fields = [];
        $conditions = [];
        foreach ($group->getFilters() as $filter) {
            $condition = $filter->getConditionType() ?: 'eq';
            $fields[] = $filter->getField();
            $conditions[] = [$condition => $filter->getValue()];
        }

        if ($fields) {
            $collection->addFieldToFilter($fields, $conditions);
        }
    }
}
