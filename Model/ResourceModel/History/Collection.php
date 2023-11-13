<?php

namespace Fortvision\Platform\Model\ResourceModel\History;

use Fortvision\Platform\Model\History\Model;
use Fortvision\Platform\Model\History\Status;
use Fortvision\Platform\Api\Data\HistoryInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Psr\Log\LoggerInterface;

/**
 * Class Collection
 * @package Fortvision\Platform\Model\ResourceModel\History
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'history_id';

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(
            \Fortvision\Platform\Model\History::class,
            \Fortvision\Platform\Model\ResourceModel\History::class
        );
    }

    /**
     * Collection constructor.
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param AdapterInterface|null $connection
     * @param AbstractDb|null $resource
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * @param int|null $count
     * @return Collection
     */
    public function getNotCompletedItems(int $count = null): Collection
    {
        $this->addFieldToFilter(
            HistoryInterface::STATUS,
            ['neq' => Status::COMPLETED]
        )->addFieldToFilter(
            HistoryInterface::ENTITY_DATA,
            ['neq' => 'NULL']
        );

        if ($count) {
            $this->setOrder(
                HistoryInterface::HISTORY_ID,
                self::SORT_ORDER_DESC
            )
            ->setPageSize($count);
        }

        return $this;
    }

    /**
     * @param $lastDate
     * @return Collection
     */
    public function getExpiredItems($lastDate): Collection
    {
        $this->addFieldToFilter(
            'updated_at',
            ['lteq' => $lastDate]
        )->addFieldToFilter(
            HistoryInterface::ACTION,
            ['eq' => Model::PRODUCT]
        );
        return $this;
    }
}
