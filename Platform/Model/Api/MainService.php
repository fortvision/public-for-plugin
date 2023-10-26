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
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Store\Model\StoreManager;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\ResourceConnection;

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




    /**
     * @return \Magento\Cms\Model\Block|null
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
