<?php

namespace Fortvision\Platform\Block;

use Fortvision\Platform\Provider\GeneralSettings;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Element\Template\Context;
use \Magento\Framework\View\Element\Template;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;

use Magento\Store\Model\ResourceModel\Website\CollectionFactory as WebsiteCollectionFactory;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManager;

use    Magento\Framework\HTTP\PhpEnvironment\Response;



/**
 * Class Fortvision
 * @package Fortvision\Platform\Block
 */
class Fortvision extends Template
{
    const PRODUCTION_JS = 'https://fortcdn.com/staticfiles/fb-web/js/fortvision-fb-web.js';
    const DEVELOP_JS = 'https://s3.eu-west-1.amazonaws.com/resources-dev.fortvision.com/staticfiles/fb-web/js/fortvision-fb-web.js';

    /**
     * @var GeneralSettings
     */
    protected $generalSettings;
    protected $websiteCollectionFactory;
    protected $storeRepository;
    protected $storeManager;
    protected $resultPageFactory;
    protected $jsonResultFactory;

    /**
     * Fortvision constructor.
     * @param Context $context
     * @param GeneralSettings $generalSettings
     * @param array $data
     */
    public function __construct(
        Context $context,
        GeneralSettings $generalSettings,
        WebsiteCollectionFactory $websiteCollectionFactory,
        StoreRepositoryInterface $storeRepository,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory,
        StoreManager $StoreManager,
        array $data = []
    ) {
        $this->generalSettings = $generalSettings;
        $this->websiteCollectionFactory = $websiteCollectionFactory;
        $this->storeRepository = $storeRepository;
        $this->storeManager = $StoreManager;
        $this->resultPageFactory = $resultPageFactory;
        $this->jsonResultFactory = $jsonResultFactory;

        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getJsUrl()
    {
        return $this->generalSettings->isDevelopMode() ? self::DEVELOP_JS : self::PRODUCTION_JS;
    }

    /**
     * @return mixed
     */
    public function getPublisherId()
    {
        return $this->generalSettings->getPublisher();
    }

     /**
     * @return mixed
     */
    public function getMagentoId()
    {
        return $this->generalSettings->getMagentoId();
    }


  /**
     * @return mixed
     */
    public function getList2()
    {
        return $this->generalSettings->getPublisher();
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function getList() {
     //   $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
     //   $resultJson->setData($result);
     //   return $resultJson;
        // return $this->generalSettings->getPublisher();
        $data = ['firstname' => 'Amit', 'lastname' => 'bera'];
        $result = $this->jsonResultFactory->create($data);
      //  $result->setJsonData(json_encode($data));
        $result->setJsonData('{"gs":"23"}');
        return $result;
        /*


        $resultPage = $this->resultPageFactory->create();

        $resultPage->setStatusHeader(200, '1.1', 'OK');
        $resultPage->setHeader('Status', 'OK');

        return $resultPage;

        $list=$this->storeRepository->getList();
       // $stores = $this->storeManager->getStores();

        $websiteCollection = $this->websiteCollectionFactory->create();
        $stores = $this->storeManager->getStores();

        // return []
        //  var_dump($list);
        echo(gettype($stores));
        echo(json_encode(array_keys($stores)));
        echo("\n");

        echo(json_encode(get_object_vars($stores[array_keys($stores)[0]])));
        echo("\n");

        echo(json_encode(get_object_vars($stores[array_keys($stores)[1]])));
     //   echo(gettype($websiteCollection));
        echo("\n");

        // echo(($websiteCollection));
       //  die();

        echo(json_encode(array_keys($list)));
        echo("\n");

        //  echo("CAAAAC");
     //   echo("CAAAAC2");

        echo(json_encode(get_object_vars($list[array_keys($list)[0]])));
        echo(json_encode(($list[array_keys($list)[0]])));
        echo("\n");

        echo(json_encode(get_object_vars($list[array_keys($list)[1]])));

        echo("\n");

        echo(json_encode(get_object_vars($list[array_keys($list)[2]])));
        echo("AAAA");

        return '+';/*
*/
      //  var_dump($list[0]);

       // var_dump($websiteCollection);
      //  return '';*/
    }
}
