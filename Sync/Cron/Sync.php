<?php

namespace Fortvision\Sync\Cron;

use Fortvision\Sync\Logger\Integration as LoggerIntegration;
use Fortvision\Sync\Service\Sync as SyncService;

/**
 * Class Sync
 * @package Fortvision\Sync\Cron
 */
class Sync
{
    /**
     * @var SyncService
     */
    protected $sync;

    /**
     * @var LoggerIntegration
     */
    protected $logger;

    /**
     * Sync constructor.
     * @param SyncService $sync
     * @param LoggerIntegration $logger
     */
    public function __construct(
        SyncService $sync,
        LoggerIntegration $logger
    ) {
        $this->sync = $sync;
        $this->logger = $logger;
    }

    public function execute()
    {
        try {
            $this->sync->process();
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }
    }
}
