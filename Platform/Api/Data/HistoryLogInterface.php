<?php

namespace Fortvision\Platform\Api\Data;

/**
 * Interface HistoryLogInterface
 * @package Fortvision\Platform\Api\Data
 */
interface HistoryLogInterface
{
    const LOG_ID = 'log_id';
    const HISTORY_ID = 'history_id';
    const ERROR_MESSAGE = 'error_message';
    const CREATED_AT = 'created_at';

    /**
     * @return int
     */
    public function getLogId();

    /**
     * @param int $id
     * @return $this
     */
    public function setLogId(int $id);

    /**
     * @return int
     */
    public function getHistoryId();

    /**
     * @param int $historyId
     * @return $this
     */
    public function setHistoryId(int $historyId);

    /**
     * @return string
     */
    public function getErrorMessage();

    /**
     * @param string $errorMessage
     * @return mixed
     */
    public function setErrorMessage(string $errorMessage);

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt(string $createdAt);
}
