<?php

namespace Fortvision\Platform\Block;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Store\Model\StoreManager;
use ReflectionClass;
use ReflectionProperty;

class MainVision // extends Action
{

    private $resultJsonFactory;
    protected $response;
    protected $storeManager;
    private $_objectManager;

    public function __construct(JsonFactory $resultJsonFactory, \Magento\Framework\Webapi\Rest\Response $response,
                                StoreManager $StoreManager,
                                \Magento\Framework\ObjectManagerInterface $objectManager   ,


                                Context $context)
    {
        $this->resultJsonFactory = $resultJsonFactory;

        $this->response = $response;
        $this->storeManager = $StoreManager;
        $this->_objectManager = $objectManager;

    }


    /**
     * @return string
     */
    public function forceExport()
    {


    }

    /**
     * @return string
     */
    public function getList()
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
        $this->response
           ->setHeader('Content-Transfer-Encoding', 'binary', true)
            ->setBody(json_encode(["result"=>"ok",'websites'=>$datadata]))
            ->setHeader('Content-Type', 'application/json', true)
         //   ->setHeader('Content-Typ2e', 'application/json', true)
            ->sendResponse();
        die();
    }
}
