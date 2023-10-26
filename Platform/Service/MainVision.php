<?php
namespace Fortvision\Platform\Service;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;

use Fortvision\Platform\Service\ExportHistorical;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Store\Model\StoreManager;
use ReflectionClass;
use ReflectionProperty;
use Fortvision\Platform\Provider\GeneralSettings;
use Fortvision\Platform\Model\Api\DTO\Product as ProductDTO;

class MainVision // extends Action
{
    const PRODUCT_URL_SUFFIX = 'catalog/seo/product_url_suffix';

    private $resultJsonFactory;
    protected $response;
    protected $storeManager;
    protected $productRepository;
    protected $searchCriteria;
    protected $filterBuilder;
    protected $productStatus;
    protected $generalSettings;
    protected $productVisibility;
    protected $filterGroup;
    protected $productCollectionFactory;
    protected $_orderCollectionFactory;
    protected $productDto;
    private $_objectManager;
    /**
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @param \Magento\Framework\Api\Search\FilterGroup $filterGroup
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     * @param \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus
     * @param \Magento\Catalog\Model\Product\Visibility $productVisibility
     */
    public function __construct(JsonFactory $resultJsonFactory, \Magento\Framework\Webapi\Rest\Response $response,
                                StoreManager $StoreManager,
                                GeneralSettings $generalSettings,

                                ProductCollectionFactory $productCollectionFactory,
                                \Magento\Framework\ObjectManagerInterface $objectManager,
                                \Magento\Catalog\Model\ProductRepository $productRepository,
                                \Magento\Framework\Api\SearchCriteriaInterface $criteria,
                                \Magento\Framework\Api\Search\FilterGroup $filterGroup,
                                \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,

                                \Magento\Framework\Api\FilterBuilder $filterBuilder,
                                \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus,
                                \Magento\Catalog\Model\Product\Visibility $productVisibility,
                                ProductDTO $productDto,


                                Context $context)
    {
        $this->resultJsonFactory = $resultJsonFactory;

        $this->response = $response;
        $this->generalSettings = $generalSettings;
        $this->storeManager = $StoreManager;
        $this->_objectManager = $objectManager;
        $this->productDto = $productDto;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->_orderCollectionFactory = $orderCollectionFactory;

        $this->productRepository = $productRepository;
        $this->searchCriteria = $criteria;
        $this->filterGroup = $filterGroup;
        $this->filterBuilder = $filterBuilder;
        $this->productStatus = $productStatus;
        $this->productVisibility = $productVisibility;

    }



    /**
     * @return string
     */
    public function forceExport()
    {
        $result = $this->getProductData();
        $websites= $this->getMagentoWebsites();
        $this->response
            ->setHeader('Content-Transfer-Encoding', 'binary', true)
            ->setBody(json_encode(["result"=>$result,'list'=>$websites]))
            ->setHeader('Content-Type', 'application/json', true)
            //   ->setHeader('Content-Typ2e', 'application/json', true)
            ->sendResponse();
        die();

    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getMagentoWebsites()
    {
        $stores = $this->storeManager->getStores();
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

    /**
     * @return string
     */
    public function getList()
    {
        $datadata=$this->getMagentoWebsites();
        $this->response
            ->setHeader('Content-Transfer-Encoding', 'binary', true)
            ->setBody(json_encode(["result"=>"ok",'websites'=>$datadata]))
            ->setHeader('Content-Type', 'application/json', true)
            //   ->setHeader('Content-Typ2e', 'application/json', true)
            ->sendResponse();
        die();
    }

    /**
     * @return \Magento\Cms\Model\Block|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getProductData()
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
        //  var_dump($products);
        $productItems = $products->getItems();
        $result=[];
        foreach ($productItems as $item) {
            if ((int)$item->getId()===4) {
                //  var_dump($item);
            }

            $result[] = $this->productDto->getProductDataFromItem($item);

        }
        // var_dump($productItems);

        return $result;
    }

    /**
     * @return mixed|string
     */
    public function getMagentoId()
    {
        return $this->generalSettings->getMagentoId();
    }


}
