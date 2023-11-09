<?php

namespace Fortvision\Platform\Service;
use Fortvision\Platform\Logger\Integration as LoggerIntegration;
use Fortvision\Platform\Provider\GeneralSettings;

use Fortvision\Platform\Model\Api\DTO\Customer as CustomerDTO;
use Fortvision\Platform\Model\History\Action;
use Fortvision\Platform\Model\History\Status;
use Fortvision\Platform\Model\HistoryFactory;
use Fortvision\Platform\Model\HistoryProcess;
use Fortvision\Platform\Model\HistoryRepository;
use Fortvision\Platform\Model\Rest\HttpClient;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Webapi\Rest\Request;

/**
 * Class CustomerLogin
 * @package Fortvision\Platform\Service
 */
class CustomerLogin
{
    const USER_LOGIN_ENDPOINT = '/users/login';
    const SUBSCRIPTION_ENDPOINT = '/users/register';
    const SUBSCRIPTION_TYPE = 1;
    /**
     * @var HttpClient
     */
    protected $httpClient;

    /**
     * @var CustomerDTO
     */
    protected $customerDto;

    /**
     * @var HistoryProcess
     */
    protected $processHistory;

    /**
     * @var HistoryFactory
     */
    protected $historyFactory;

    /**
     * @var HistoryRepository
     */
    protected $historyRepository;

    /**
     * @var Json
     */
    protected $json;
    private LoggerIntegration $_logger;
    private GeneralSettings $generalSettings;

    /**
     * CustomerLogin constructor.
     * @param HttpClient $httpClient
     * @param HistoryProcess $processHistory
     * @param CustomerDTO $customerDto
     * @param HistoryFactory $historyFactory
     * @param HistoryRepository $historyRepository
     * @param Json $json
     */
    public function __construct(
        HttpClient $httpClient,
        HistoryProcess $processHistory,
        CustomerDTO $customerDto,
        LoggerIntegration $logger,
        GeneralSettings $generalSettings,

        HistoryFactory $historyFactory,
        HistoryRepository $historyRepository,
        Json $json
    ) {
        $this->_logger = $logger;
        $this->generalSettings = $generalSettings;

        $this->httpClient = $httpClient;
        $this->customerDto = $customerDto;
        $this->processHistory = $processHistory;
        $this->historyFactory = $historyFactory;
        $this->historyRepository = $historyRepository;
        $this->json = $json;
    }

    /**
     * @param $data
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function process($data)
    {
        $params['body'] = $data;
        $response = $this->httpClient->doRequest(
            self::USER_LOGIN_ENDPOINT,
            $params,
            Request::HTTP_METHOD_PUT
        );
        $result = $response->getBody()->getContents();
    }

    /**
     * @param CustomerInterface $customer
     */
    public function customerLogin(CustomerInterface $customer)
    {
        $this->_logger->debug('loginaction');

        try {
          //  $url = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\UrlInterface');
            $cartData['endpoint'] = self::USER_LOGIN_ENDPOINT;
            $cartData['kind'] = Action::USER_LOGIN;
            $cartData['magento_id'] = $this->generalSettings->getMagentoId();
            $cartData['hostURL'] = $_SERVER['HTTP_HOST'];
            $cartData['userInfo'] = $this->customerDto->getUserInfoData($customer);
            $this->_logger->debug('loginaction'.json_encode($cartData));
            // echo('-----'.json_encode($cartData).'---');
            $this->process(json_encode($cartData));

        } catch (\Exception $e) {
            $this->_logger->debug("exsubs".$e->getMessage());
        }
    }

    public function addSubscription(CustomerInterface $customer)
    {
        $this->_logger->debug('add subscription');

        try {
            $subscriber = [
                'userInfo' => $this->customerDto->getUserInfoData($customer),
                'subscriptionType' => self::SUBSCRIPTION_TYPE
            ];

            //$modelData = $this->json->serialize($subscriber);
            $cartData['endpoint'] = self::SUBSCRIPTION_ENDPOINT;
            $cartData['kind'] = Action::ADD_SUBSCRIPTION;
            $cartData['magento_id'] = $this->generalSettings->getMagentoId();
            $cartData['hostURL'] = $_SERVER['HTTP_HOST'];

            $cartData['userInfo'] = $this->customerDto->getUserInfoData($customer);
            $cartData['subscriptionType'] = self::SUBSCRIPTION_TYPE;

            $this->_logger->debug('SUBSCRIBE!'.json_encode($cartData));
            // echo('-----'.json_encode($cartData).'---');
            $this->process(json_encode($cartData));

            /*
            $history = $this->historyFactory->create();
            $history->setStatus(Status::PENDING)
                ->setAction(Action::ADD_SUBSCRIPTION)
                ->setServiceClass(AddSubscription::class)
                ->setEntityData($modelData);
            $history = $this->historyRepository->save($history);
            $this->processHistory->processById($history->getHistoryId()); */
        } catch (\Exception $e) {
            $this->_logger->debug("exsubs".$e->getMessage());
        }
    }
}
