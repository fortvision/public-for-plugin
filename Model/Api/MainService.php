<?php

namespace Fortvision\Platform\Model\Api;

use Fortvision\Platform\Helper\Data;
use Fortvision\Platform\Model\Api\DTO\Product as ProductDTO;
use Fortvision\Platform\Model\Api\DTO\Customer as CustomerDTO;
use Fortvision\Platform\Model\Api\DTO\Cart as OrderDTO;
use Fortvision\Platform\Provider\GeneralSettings;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\FlagManager;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Store\Model\StoreManager;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\ResourceConnection;
use MailPoet\Exception;

/**
 * Class Product
 * @package Fortvision\Platform\Model\Api\DTO
 */
class MainService
{
    /**
     * @var GeneralSettings
     */
    protected $generalSettings;
    protected $_objectManager ;
    protected string $routUrl = 'https://smc2t4kcbb.execute-api.eu-west-1.amazonaws.com/1/plugin/aggregate';
    protected string $magentoHelperUrl = 'https://magentotools.fortvision.net';
    /**
     * @var DateTime
     */
    protected $date;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;
    private \Magento\Catalog\Model\Product\Visibility $productVisibility;
    private \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus;
    private \Magento\Framework\Api\FilterBuilder $filterBuilder;
    private ProductDTO $productDto;
    private ProductCollectionFactory $productCollectionFactory;
    private \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria;
    private \Magento\Framework\Api\Search\FilterGroup $filterGroup;
    private \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory;

    private \Magento\Customer\Model\CustomerFactory $_customerFactory;
    private \Magento\Customer\Model\Customer $_customer;
    private ResourceConnection $resourceConnection;
    private OrderDTO $orderDTO;
    private CustomerDTO $customerDto;
    private FlagManager $flagManager;

    /**
     * Product constructor.
     * @param GeneralSettings $generalSettings
     * @param DateTime $date
     * @param StoreManagerInterface $storeManager
     * @param Data $helper
     * @param CategoryRepositoryInterface $categoryRepository
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        GeneralSettings $generalSettings,
        FlagManager                                        $flagManager,

        DateTime $date,
        StoreManagerInterface $storeManager,
        Data $helper,
        CategoryRepositoryInterface $categoryRepository,
        ProductRepositoryInterface $productRepository,
        StoreManager $StoreManager,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\Customer $customers,
        ProductCollectionFactory $productCollectionFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Api\SearchCriteriaInterface $criteria,
        \Magento\Framework\Api\Search\FilterGroup $filterGroup,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,

        ProductDTO $productDto,
        CustomerDTO $customerDto,
        OrderDTO $orderDTO,

        ResourceConnection $resourceConnection

    ) {
        $this->resourceConnection = $resourceConnection;
        $this->flagManager = $flagManager;

        $this->date = $date;
        $this->helper = $helper;
        $this->categoryRepository = $categoryRepository;
        $this->_customerFactory = $customerFactory;
        $this->_customer = $customers;
        $this->generalSettings = $generalSettings;
        $this->storeManager = $StoreManager;
        $this->_objectManager = $objectManager;
        $this->productDto = $productDto;
        $this->customerDto = $customerDto;
        $this->orderDTO = $orderDTO;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->productRepository = $productRepository;
        $this->searchCriteria = $criteria;
        $this->filterGroup = $filterGroup;
        $this->filterBuilder = $filterBuilder;
        $this->productStatus = $productStatus;
        $this->productVisibility = $productVisibility;
    }


    /**
     * @return \Magento\Sales\Model\ResourceModel\Order\Collection
     */
    public function getAllOrders() {

        $connection = $this->resourceConnection->getConnection();
        // $table is table name

        //For Select query
        $query = "Select entity_id FROM " . $connection->getTableName('sales_order');
        $result = $connection->fetchAll($query);$ids=array_map(function ($data){
            return $data['entity_id'];
            },$result);
        $result=[];
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        foreach($ids as $id) {
            $order = $objectManager->create('\Magento\Sales\Model\OrderRepository')->get($id);
            $payload = $this->orderDTO->getHistData($order);

            $result[]=$payload;

        }
        return $result;

    }
    /**
     * Get customer collection
     */
    public function getCustomerCollection()
    {
        return $this->_customerFactory->create()->getCollection()
            ->addAttributeToSelect("*")
            ->load();
    }


    public function addUser($email) {
        $customer   = $this->_customerFactory->create();

        $customer->setWebsiteId(1);
        $customer->setEmail($email);
       // $this->logger->debug('addcustome3');
       // $this->logger->debug('addcustome3:');

        $customer->setFirstname("wdewewe");
        $customer->setLastname("ewrewqrw");
        $customer->setPassword("password");
        $customer->setConfirmation(null);
        $customer->setForceConfirmed(true);
        $customer->setForceConfirmed(true);
        $customer->save();
        $customer->sendNewAccountEmail();
//        echo('wew-9');

    }
    public function getAllCustomers()
    {

      //  echo('cs');
        $result = [];
        $customerCollection = $this->getCustomerCollection();
        // echo(json_encode($customerCollection));
      //  var_dump($customerCollection);

        foreach ($customerCollection as $customer) {
           // echo("w");
            $websites=$customer->getWebsiteIds();
            $websites=$customer->getData();
            $payload = $this->customerDto->getHistData($customer);

           // $payload=['email222'=>$customer->getEmail(),'websites'=>$websites];
          //  var_dump($payload);
            //echo $customer->getEmail();
            $result[]=$payload;
        }
        return $result;
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
        //    echo("EXPSPExceptionSP" . ($e->getMessage()));
            error_log("Expsend" . json_encode($e));
        }
        return false;
    }

 public function doSyncDataRegular()
 {
     $magentoId = $this->flagManager->getFlagData('fortvision_magento_id');

     try {
         $products = $this->getAllProducts();
         // echo(json_encode($products));
         $orders = $this->getAllOrders();
         $customers = $this->getAllCustomers();
         $source_data = $this->sendRequest('', ['kind' => 'get', 'id' => $magentoId], ['mode' => 'magento']);
         $source = json_decode($source_data?$source_data['Response']:'{"result":[]}');
         if (!isset($source)) {
        //     echo("CONN ERROR");
             return;
         }
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
                 //   echo($websiteCode.json_encode($line['websitesids']).in_array($websiteCode, $line['websitesids']));
                 return isset($line['websitesids']) && in_array($websiteCode, $line['websitesids']);
             }));
             if ($websiteProducts && count($websiteProducts) > 0) {
                 $resres1 = $this->sendRequest('products', $websiteProducts, ["mode" => 'base', 'publisherId' => $publisherId]);
                 //     echo("SYNC PRODUCTS HIST " . json_encode($resres1));
               //  $output->writeln('<info>Sync DB- PRODUCTS  ' . $websiteCode . ' ' . $publisherId . ' ' . count($websiteProducts) . 'has been finished</info>');

             }
             if ($websiteOrders && count($websiteOrders) > 0) {
                 $resres2 = $this->sendRequest('orders', $websiteOrders, ["mode" => 'base', 'publisherId' => $publisherId]);
                 //   echo("SYNC ORDERS RES " . json_encode($resres2));
               //  $output->writeln('<info>Sync DB- ORDERS ' . $websiteCode . ' ' . $publisherId . ' ' . count($websiteOrders) . ' has been finished</info>');

             }
             if ($websiteCustomers && count($websiteCustomers) > 0) {

                 //  echo("!!!".json_encode($websiteCustomers));
                 $resres3 = $this->sendRequest('customers', $websiteCustomers, ["mode" => 'base', 'publisherId' => $publisherId]);
                 //   echo("SYNC CUSTOMERS RES " . json_encode($resres3));
               //  $output->writeln('<info>Sync DB- CUSTOMERS ' . $websiteCode . ' ' . $publisherId . ' ' . count($websiteCustomers) . '  has been finished</info>');

             }
             $this->sendRequest('customers', $websiteCustomers, ["mode" => 'base', 'publisherId' => $publisherId]);
             //  body: JSON.stringify({migrateHistoricalData: true, plugin: 'shopify', publisherId})
             $this->sendRequest(false, false, ["mode" => 'base', 'migrateHistoricalData' => true, 'publisherId' => $publisherId]);

             return ["customers"=>count($websiteCustomers), 'orders'=>count($websiteOrders),'products'=>count($websiteProducts)];


         }
     } catch (Exception $e) {
     }

 }

    /**
     * @return @mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getAllProducts()
    {

        $this->filterGroup->setFilters([
            $this->filterBuilder
                ->setField('status')
                ->setConditionType('in')
                ->setValue($this->productStatus->getVisibleStatusIds())
                ->create(),
            $this->filterBuilder
                ->setField('visibility')
                ->setConditionType('in')
                ->setValue($this->productVisibility->getVisibleInSiteIds())
                ->create(),
        ]);

        $this->searchCriteria->setFilterGroups([$this->filterGroup]);
        $products = $this->productRepository->getList($this->searchCriteria);
        $productItems = $products->getItems();
        $result=[];
        foreach ($productItems as $item) {
            $result[] = $this->productDto->getProductDataFromItem($item);
        }
        return $result;
    }


    public function getMagentoWebsites()
    {
      //  $stores = $this->storeManager->getStores();
        $websites = $this->storeManager->getWebsites();
        $storeManager =  $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface');

        $datadata=[];
        foreach($websites as $website){
            foreach($website->getStores() as $store){
                $wedsiteId = $website->getId();
                $wedsiteName = $website->getName();
                $storeObj = $storeManager->getStore($store);
                $storeId = $storeObj->getId();
                $storeName = $storeObj->getName();
                $url = $storeObj->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
                $datadata[]=['siteId'=>$wedsiteId,'sitename'=>$wedsiteName,'storeName'=>$storeName,'storeId'=>$storeId,"url"=>$url];
            }
        }
        return $datadata;

    }

}
