<?php

namespace Fortvision\Platform\Model;

use Fortvision\Platform\Api\Data\HistoryInterface;
use Fortvision\Platform\Logger\Integration;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

/**
 * Class History
 * @package Fortvision\Platform\Model
 */
class History extends AbstractModel implements HistoryInterface
{
    /**
     * @var string
     */
    protected $_idFieldName = 'history_id';

    /**
     * @var HistoryLogRepository
     */
    protected $historyLogRepository;

    /**
     * @var HistoryLogFactory
     */
    protected $historyLogFactory;

    /**
     * @var Integration
     */
    protected $logger;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param HistoryLogRepository $historyLogRepository
     * @param HistoryLogFactory $historyLogFactory
     * @param Integration $logger
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        HistoryLogRepository  $historyLogRepository,
        HistoryLogFactory $historyLogFactory,
        Integration $logger,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->historyLogRepository = $historyLogRepository;
        $this->historyLogFactory = $historyLogFactory;
        $this->logger = $logger;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    protected function _construct()
    {
        $this->_init(ResourceModel\History::class);
    }

    /**
     * @return int|null
     */
    public function getHistoryId():? int
    {
        return (int) $this->getData(self::HISTORY_ID);
    }

    /**
     * @param int $historyId
     * @return HistoryInterface
     */
    public function setHistoryId(int $historyId): HistoryInterface
    {
        return $this->setData(self::HISTORY_ID, $historyId);
    }

    /**
     * @return string|null
     */
    public function getAction():? string
    {
        return (string) $this->getData(self::ACTION);
    }

    /**
     * @param string $action
     * @return HistoryInterface
     */
    public function setAction(string $action): HistoryInterface
    {
        return $this->setData(self::ACTION, $action);
    }

    /**
     * @return string|null
     */
    public function getServiceClass():? string
    {
        return (string) $this->getData(self::SERVICE_CLASS);
    }

    /**
     * @param string $serviceClass
     * @return History
     */
    public function setServiceClass(string $serviceClass): HistoryInterface
    {
        return $this->setData(self::SERVICE_CLASS, $serviceClass);
    }

    /**
     * @return string|null
     */
    public function getEntityData():? string
    {
        return (string) $this->getData(self::ENTITY_DATA);
    }

    /**
     * @param string|null $entityData
     * @return HistoryInterface
     */
    public function setEntityData(string $entityData = null): HistoryInterface
    {
        return $this->setData(self::ENTITY_DATA, $entityData);
    }

    /**
     * @return string|null
     */
    public function getStatus():? string
    {
        return (string) $this->getData(self::STATUS);
    }

    /**
     * @param string $status
     * @return History
     */
    public function setStatus(string $status): HistoryInterface
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * @return string|null
     */
    public function getCreatedAt():? string
    {
        return (string) $this->getData(self::CREATED_AT);
    }

    /**
     * @param string $createdAt
     * @return HistoryInterface
     */
    public function setCreatedAt(string $createdAt): HistoryInterface
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * @return string|null
     */
    public function getUpdatedAt():? string
    {
        return (string) $this->getData(self::CREATED_AT);
    }

    /**
     * @param string $updatedAt
     * @return HistoryInterface
     */
    public function setUpdatedAt(string $updatedAt): HistoryInterface
    {
        return $this->setData(self::CREATED_AT, $updatedAt);
    }

    /**
     * @param string $message
     */
    public function addHistoryLog(string $message = '')
    {
        try {
            $historyLog = $this->historyLogFactory->create();
            $historyLog->setHistoryId($this->getHistoryId())
                ->setErrorMessage($message);
            $this->historyLogRepository->save($historyLog);
        } catch (\Exception $e) {
            $error = __('Unable to add history log for %1 history: %2', $this->getHistoryId(), $e->getMessage());
            $this->logger->critical($error);
        }
    }
}
