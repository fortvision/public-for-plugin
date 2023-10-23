<?php

namespace Fortvision\Platform\Model\ResourceModel\HistoryLog;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package Fortvision\Platform\Model\ResourceModel\HistoryLog
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'log_id';

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(
            \Fortvision\Platform\Model\HistoryLog::class,
            \Fortvision\Platform\Model\ResourceModel\HistoryLog::class
        );
    }
}
