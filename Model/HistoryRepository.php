<?php

namespace Fortvision\Platform\Model;

use Fortvision\Platform\Api\Data\HistoryInterface;
use Fortvision\Platform\Api\Data\HistorySearchResultsInterface;
use Fortvision\Platform\Api\Data\HistorySearchResultsInterfaceFactory;
use Fortvision\Platform\Api\HistoryRepositoryInterface;
use Fortvision\Platform\Model\ResourceModel\History\Collection;
use Fortvision\Platform\Model\ResourceModel\History\CollectionFactory;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class HistoryRepository
 * @package Fortvision\Platform\Model
 */
class HistoryRepository implements HistoryRepositoryInterface
{
    /**
     * @var History
     */
    private $historyResource;

    /**
     * @var HistoryFactory
     */
    private $historyFactory;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var HistorySearchResultsInterfaceFactory
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
     * HistoryRepository constructor.
     * @param ResourceModel\History $historyResource
     * @param HistoryFactory $historyFactory
     * @param CollectionFactory $collectionFactory
     * @param HistorySearchResultsInterfaceFactory $searchResultsFactory
     * @param FilterBuilder $filterBuilder
     * @param FilterGroupBuilder $filterGroupBuilder
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     */
    public function __construct(
        ResourceModel\History $historyResource,
        HistoryFactory $historyFactory,
        CollectionFactory $collectionFactory,
        HistorySearchResultsInterfaceFactory $searchResultsFactory,
        FilterBuilder $filterBuilder,
        FilterGroupBuilder $filterGroupBuilder,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
    ) {
        $this->historyResource = $historyResource;
        $this->historyFactory = $historyFactory;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->filterBuilder = $filterBuilder;
        $this->filterGroupBuilder = $filterGroupBuilder;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
    }

    /**
     * @param HistoryInterface $history
     * @return HistoryInterface
     * @throws \Exception
     */
    public function save(HistoryInterface $history)
    {
        $this->historyResource->save($history);

        return $history;
    }

    /**
     * @param HistoryInterface $history
     * @return bool|History
     * @throws \Exception
     */
    public function delete(HistoryInterface $history)
    {
        return $this->deleteById($history->getHistoryId());
    }

    /**
     * @param HistoryInterface $historyId
     * @return bool|History
     * @throws \Exception
     */
    public function deleteById($historyId)
    {
        $history = $this->historyFactory->create();
        $history->getHistoryId($historyId);

        return $this->historyResource->delete($history);
    }

    /**
     * @param int $historyId
     * @return HistoryInterface
     * @throws NoSuchEntityException
     */
    public function getById($historyId)
    {
        $history = $this->historyFactory->create();
        $this->historyResource->load($history, $historyId);
        if (empty($history->getHistoryId())) {
            throw new NoSuchEntityException(
                __('Unable to find History Data with ID "%1"', $historyId)
            );
        }

        return $history;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return HistorySearchResultsInterface
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

        /** @var HistorySearchResultsInterface $searchResults */
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
