<?php

namespace Fortvision\Platform\Model;

use Fortvision\Platform\Api\Data\HistoryLogInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Class HistoryLog
 * @package Fortvision\Platform\Model
 */
class HistoryLog extends AbstractModel implements HistoryLogInterface
{
    protected $_idFieldName = 'log_id';

    protected function _construct()
    {
        $this->_init(ResourceModel\HistoryLog::class);
    }

    /**
     * @return int
     */
    public function getLogId()
    {
        return $this->getData(self::LOG_ID);
    }

    /**
     * @param int $logId
     * @return HistoryLog
     */
    public function setLogId($logId)
    {
        return $this->setData(self::LOG_ID, $logId);
    }

    /**
     * @return string
     */
    public function getHistoryId()
    {
        return $this->getData(self::HISTORY_ID);
    }

    /**
     * @param int $historyId
     * @return HistoryLog
     */
    public function setHistoryId($historyId)
    {
        return $this->setData(self::HISTORY_ID, $historyId);
    }

    /**
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->getData(self::ERROR_MESSAGE);
    }

    /**
     * @param $errorMessage
     * @return mixed
     */
    public function setErrorMessage($errorMessage)
    {
        return $this->setData(self::ERROR_MESSAGE, $errorMessage);
    }

    /**
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @param string $createdAt
     * @return HistoryLog
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }
}
