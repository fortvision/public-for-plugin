<?php

namespace Fortvision\Platform\Console\Command;

use Fortvision\Platform\Logger\Integration as LoggerIntegration;

// use Fortvision\Platform\Service\ExportHistorical as SyncService;
use Fortvision\Platform\Model\Api\DTO\Product as ProductDTO;
use Fortvision\Platform\Model\Api\MainService as MainVisionService;
use Fortvision\Platform\Provider\GeneralSettings;
use Fortvision\Platform\Service\MainVision;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\State;
use Magento\Framework\Console\Cli;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\FlagManager;
use Magento\Store\Model\StoreManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Sync
 * @package Fortvision\Sync\Console\Command
 */
class Sync extends Command
{
    protected $state;
    protected $sync;
    protected string $routUrl = 'https://smc2t4kcbb.execute-api.eu-west-1.amazonaws.com/1/plugin/aggregate';
    protected string $magentoHelperUrl = 'https://magentotools.fortvision.net';

    protected $logger;
    protected $mainVisionService;
    protected $magentoId;
    protected $flagManager;

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
        //  SyncService $sync,
        LoggerIntegration                                  $logger,
        StoreManager                                       $StoreManager,
        GeneralSettings                                    $generalSettings,/*
        ProductCollectionFactory $productCollectionFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Framework\Api\SearchCriteriaInterface $criteria,
        \Magento\Framework\Api\Search\FilterGroup $filterGroup,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        ProductDTO $productDto, */
        MainVisionService                                  $mainVisionService,

        //    \Fortvision\Platform\Service\MainVision $mainVision,
        // string $name = null
    )
    {
        $this->flagManager = $flagManager;
        $flagCode = 'fortvision_magento_id';

        $this->magentoId = $this->flagManager->getFlagData($flagCode);


        $this->mainVisionService = $mainVisionService;
        //   $this->sync = $sync;
        $this->logger = $logger;
        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('fortvision:export');
        $this->setDescription('Fortvision Export');
        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void
     * @throws LocalizedException
     */
    protected function exec1ute(InputInterface $input, OutputInterface $output)
    {

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // $this->state->setAreaCode(Area::AREA_ADMINHTML);

        $output->writeln('<info>Start sync DB process</info>');
        try {
            $products = $this->mainVisionService->getAllProducts();
            $orders = $this->mainVisionService->getAllOrders();
            $customers = $this->mainVisionService->getAllCustomers();

            $websites = $this->mainVisionService->getMagentoWebsites();

            $output->writeln(json_encode($customers));
            $output->writeln("\n");
            $source_data = $this->sendRequest('', ['kind' => 'get', 'id' => $this->magentoId], ['mode' => 'magento']);
            $source = json_decode($source_data['Response']);
            $sourceArr = (array)$source->result;
            $keysall = array_map(function ($dat) {
                return str_replace("publisher_", '', $dat);
            }, array_filter(array_keys($sourceArr), function ($data) {
                return strpos($data, 'publisher') !== false;
            }));

            foreach ($keysall as $websiteCode) {
                $publisherId = $sourceArr['publisher_' . $websiteCode];
                $websiteProducts = array_values(array_filter($products, function ($line) use ($websiteCode) {
                    return in_array($websiteCode, $line['websitesids']);
                }));
                $websiteOrders = array_values(array_filter($orders, function ($line) use ($websiteCode) {
                    return isset($line['websitesids']) && in_array($websiteCode, $line['websitesids']);
                }));
                $websiteCustomers = array_values(array_filter($customers, function ($line) use ($websiteCode) {
                    return isset($line['websitesids']) && in_array($websiteCode, $line['websitesids']);
                }));
                $resres1 = $this->sendRequest('products', $websiteProducts, ["mode" => 'base', 'publisherId' => $publisherId]);
                if ($websiteOrders && count($websiteOrders) > 0) {

                    $resres2 = $this->sendRequest('orders', $websiteOrders, ["mode" => 'base', 'publisherId' => $publisherId]);
                    echo("SYNC O RES " . json_encode($resres2));
                }
                $resres3 = $this->sendRequest('customers', $websiteCustomers, ["mode" => 'base', 'publisherId' => $publisherId]);
                //   echo("SYNC P RES ".json_encode($resres1));

                //   echo("SYNC C RES ".json_encode($resres3));
            }

            $output->writeln('<info>Sync DB has been finished</info>');
            return Cli::RETURN_SUCCESS;
        } catch (\Exception $e) {
            echo('s' . $e->getMessage());
            $output->writeln('<error>Sync DB has been failed</error>');
            $this->logger->critical($e->getMessage());
            return Cli::RETURN_FAILURE;
        }
    }


    public function sendRequest($action, $data, $syncOptions)
    {


        try {

            if ($syncOptions && $syncOptions['mode'] === 'magento') {
                $curlUrl = $this->magentoHelperUrl;
            } else
                if ($syncOptions && $syncOptions['mode'] === 'base') {
                    $curlUrl = $this->routUrl;
                    $data = array(
                        'plugin' => 'magento',
                        'publisherId' => $syncOptions['publisherId'], // (int)$this->options['publisherId'],
                        $action => $data
                    );
                }
            //    echo("!!!!".$curlUrl);
            $ch = curl_init($curlUrl);
            curl_setopt($ch, CURLOPT_TIMEOUT, 3);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $verbose = fopen('php://temp', 'w+');
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            curl_setopt($ch, CURLOPT_STDERR, $verbose);
            curl_setopt($ch, CURLINFO_HEADER_OUT, 1);

            $curlResponse = curl_exec($ch);
            //      error_log('REQUEST:'. time()." ".json_encode($data)." ".$curlUrl);
            //    error_log('RESPONSE:'. time()." ".json_encode($curlResponse)." ".$curlUrl);
            // $this->logger->clog(['action' => 'REQUEST', 'value' => json_encode($data)." ".$curlUrl]);
            // $this->logger->clog(['action' => 'RESPONSE', 'value' => json_encode($curlResponse)]);

            $curlError = curl_error($ch);
            $curlCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if ($curlCode != 200) {
                rewind($verbose);
                $verboseLog = stream_get_contents($verbose);
            } else {
                $verboseLog = '-';
            }

            $logData = array(
                'Time' => date('Y-m-d H:i:s'),
                'Action' => 'aggregate|' . $action,
                'Method' => 'POST',
                'Rout' => $curlUrl,
                'Data' => $data,
                'SyncOptions' => var_export($syncOptions, true),
                'Response' => $curlResponse,
                'Code' => $curlCode,
                'Error' => $curlError,
                'Verbose' => $verboseLog

            );
            // if ($this->options['debug_mode_enabled']) {
            //    FortvisionEventTracker::instance()->_log($logData);
            //  }
            curl_close($ch);
            return $logData;
        } catch (\Exception $e) {
            echo("EXPSPExceptionSP" . ($e->getMessage()));
            error_log("Expsend" . json_encode($e));
        }
        return false;
    }
}
