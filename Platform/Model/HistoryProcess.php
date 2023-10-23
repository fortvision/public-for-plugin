<?php

namespace Fortvision\Platform\Model;

use Fortvision\Platform\Api\Data\HistoryInterface;
use Fortvision\Platform\Model\HistoryLogFactory;
use Fortvision\Platform\Model\HistoryLogRepository;
use Fortvision\Platform\Model\HistoryRepository;
use Fortvision\Platform\Model\History\Status;
use Fortvision\Platform\Logger\Integration;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class HistoryProcess
 * @package Fortvision\Platform\Model
 */
class HistoryProcess
{
    /**
     * @var HistoryRepository
     */
    protected $historyRepository;

    /**
     * @var HistoryLogRepository
     */
    protected $historyLogRepository;

    /**
     * @var HistoryLogFactory
     */
    protected $historyLogFactory;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var Integration
     */
    protected $logger;

    /**
     * Process constructor.
     * @param HistoryRepository $historyRepository
     * @param HistoryLogRepository $historyLogRepository
     * @param HistoryLogFactory $historyLogFactory
     * @param ObjectManagerInterface $objectManager
     * @param Integration $logger
     */
    public function __construct(
        HistoryRepository $historyRepository,
        HistoryLogRepository  $historyLogRepository,
        HistoryLogFactory $historyLogFactory,
        ObjectManagerInterface $objectManager,
        Integration $logger
    ) {
        $this->historyRepository = $historyRepository;
        $this->historyLogRepository = $historyLogRepository;
        $this->historyLogFactory = $historyLogFactory;
        $this->objectManager = $objectManager;
        $this->logger = $logger;
    }

    /**
     * @param int $historyId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function processById(int $historyId)
    {
        $historyItem = $this->historyRepository->getById($historyId);
        return $this->runItem($historyItem);
    }

    /**
     * @param HistoryInterface $item
     * @return bool
     */
    protected function runItem(HistoryInterface $item)
    {
        $result = false;
        try {
            $this->runModelProcess($item);
            $item->setStatus(Status::COMPLETED);
            $item->addHistoryLog(__('Process completed'));
            $result = true;
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
            $item->addHistoryLog($e->getMessage());
            $item->setStatus(Status::FAILED);
        }

        try {
            $this->historyRepository->save($item);
        } catch (\Exception $e) {
            $error = __('Unable to save history item %1: %2', $item->getId(), $e->getMessage());
            $this->logger->critical($error);
        }
        return $result;
    }

    /**
     * @param HistoryInterface $item
     */
    protected function runModelProcess(HistoryInterface $item)
    {
        $service = $this->objectManager->get($item->getServiceClass());
        if ($service) {
            $service->process($item->getEntityData());
        }
    }
}
