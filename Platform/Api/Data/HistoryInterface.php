<?php

namespace Fortvision\Platform\Api\Data;

/**
 * Interface HistoryInterface
 * @package Fortvision\Platform\Api\Data
 */
interface HistoryInterface
{
    const HISTORY_ID = 'history_id';
    const ACTION = 'action';
    const SERVICE_CLASS = 'service_class';
    const ENTITY_DATA = 'entity_data';
    const STATUS = 'status';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    /**
     * @return int|null
     */
    public function getHistoryId():? int;

    /**
     * @param int $historyId
     * @return HistoryInterface
     */
    public function setHistoryId(int $historyId): HistoryInterface;

    /**
     * @return string|null
     */
    public function getAction():? string;

    /**
     * @param string $action
     * @return HistoryInterface
     */
    public function setAction(string $action): HistoryInterface;

    /**
     * @return string|null
     */
    public function getServiceClass():? string;

    /**
     * @param string $serviceClass
     * @return HistoryInterface
     */
    public function setServiceClass(string $serviceClass): HistoryInterface;

    /**
     * @return string|null
     */
    public function getEntityData():? string;

    /**
     * @param string|null $entityData
     * @return HistoryInterface
     */
    public function setEntityData(string $entityData = null): HistoryInterface;

    /**
     * @return string|null
     */
    public function getStatus():? string;

    /**
     * @param string $status
     * @return HistoryInterface
     */
    public function setStatus(string $status): HistoryInterface;

    /**
     * @return string|null
     */
    public function getCreatedAt():? string;

    /**
     * @param string $createdAt
     * @return HistoryInterface
     */
    public function setCreatedAt(string $createdAt): HistoryInterface;

    /**
     * @return string|null
     */
    public function getUpdatedAt():? string;

    /**
     * @param string $updatedAt
     * @return HistoryInterface
     */
    public function setUpdatedAt(string $updatedAt): HistoryInterface;
}
