<?php

namespace Fortvision\Platform\Cron;

// use Fortvision\Sync\Logger\Integration as LoggerIntegration;
use Fortvision\Platform\Provider\GeneralSettings;
use Fortvision\Platform\Service\ExportHistorical as SyncService;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;

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
    protected $generalSettings;
    protected $categoryRepository;
    protected $productRepository;
    protected $storeManager;



    /**
     * Sync constructor.
     * @param SyncService $sync
     */
    public function __construct(
        SyncService $sync,
        GeneralSettings $generalSettings,
        CategoryRepositoryInterface $categoryRepository,
        ProductRepositoryInterface $productRepository,
        StoreManagerInterface $storeManager,
      //  LoggerIntegration $logger
    ) {
        $this->generalSettings = $generalSettings;
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
        $this->storeManager = $storeManager;

        $data = new SyncService($generalSettings,$categoryRepository,$productRepository,$storeManager  );
        $this->sync = $data;

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
