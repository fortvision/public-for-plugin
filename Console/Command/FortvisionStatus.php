<?php

namespace Fortvision\Platform\Console\Command;

use Fortvision\Platform\Logger\Integration as LoggerIntegration;
use Fortvision\Platform\Model\Rest\HttpClient;

// use Fortvision\Platform\Service\ExportHistorical as SyncService;
// use Fortvision\Platform\Model\Api\DTO\Product as ProductDTO;
use Fortvision\Platform\Model\Api\MainService as MainVisionService;
use Fortvision\Platform\Service\MainVision as MainVision;

use Fortvision\Platform\Provider\GeneralSettings;
// use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
// use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\State;
use Magento\Framework\Console\Cli;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\FlagManager;
use Magento\Store\Model\StoreManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class FortvisionStatus
 * @package Fortvision\Sync\Console\Command
 */
class FortvisionStatus extends Command
{
    protected $state;
    protected $sync;
    protected string $routUrl = 'https://smc2t4kcbb.execute-api.eu-west-1.amazonaws.com/1/plugin/aggregate';
    protected string $magentoHelperUrl = 'https://magentotools.fortvision.net';
    protected $logger;
    protected $mainVisionService;
    protected $mainVision;
    protected $magentoId;
    protected $flagManager;
    protected $objectManager;
    private HttpClient $httpClient;
    private GeneralSettings $generalSettings;

    /**
     * Sync constructor.
     * @param State $state
     * @param LoggerIntegration $logger
     * @param string|null $name
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface         $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        //  State $state,
        FlagManager                                        $flagManager,
        HttpClient                                         $httpClient,
        //  SyncService $sync,
        LoggerIntegration                                  $logger,
        StoreManager                                       $StoreManager,
        \Magento\Framework\ObjectManagerInterface          $objectManager,
        GeneralSettings                                    $generalSettings,
        MainVisionService                                  $mainVisionService,
       // MainVision                                         $mainVision,

      //  \Fortvision\Platform\Service\MainVision $mainVision,
        // string $name = null
    )
    {
        $this->flagManager = $flagManager;
        $flagCode = 'fortvision_magento_id';
        $this->objectManager = $objectManager;
        $this->httpClient = $httpClient;
        $this->generalSettings = $generalSettings;

        $this->magentoId = $this->flagManager->getFlagData($flagCode);


        $this->mainVisionService = $mainVisionService;
      //  $this->mainVision = $mainVision;
        //   $this->sync = $sync;
        $this->logger = $logger;
        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('fortvision:status');
        $this->setDescription('Check status');
        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void
     * @throws LocalizedException
     */


    protected function execute(InputInterface $input, OutputInterface $output)
    {
      //  $output->writeln('Hello world!');
      //  $output->writeln($this->magentoId);
      //  $output->writeln($this->magentoId);
        $output->writeln(json_encode($this->mainVisionService->getDataRegular(2)));

        //   $this->sendLog();
        return Cli::RETURN_SUCCESS;

    }
}
