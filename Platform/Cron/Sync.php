<?php

namespace Fortvision\Platform\Cron;

// use Fortvision\Sync\Logger\Integration as LoggerIntegration;
use Fortvision\Platform\Service\ExportHistorical as SyncService;

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
     * Sync constructor.
     * @param SyncService $sync
     */
    public function __construct(
        SyncService $sync,
      //  LoggerIntegration $logger
    ) {
        $this->sync = $sync;
     //   $this->logger = $logger;
    }

    public function execute()
    {
        try {
            $this->sync->process();
        } catch (\Exception $e) {
         //   $this->logger->critical($e->getMessage());
        }
    }
}
