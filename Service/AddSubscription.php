<?php

namespace Fortvision\Platform\Service;

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
use Fortvision\Platform\Logger\Integration as LoggerIntegration;

/**
 * Class AddSubscription
 * @package Fortvision\Platform\Service
 */
class AddSubscription
{
    const SUBSCRIPTION_ENDPOINT = '/users/subscription';
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

    /**
     * AddSubscription constructor.
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
        HistoryFactory $historyFactory,
        HistoryRepository $historyRepository,
        Json $json
    ) {
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
            self::SUBSCRIPTION_ENDPOINT,
            $params,
            Request::HTTP_METHOD_POST
        );
        $result = $response->getBody()->getContents();
    }

    /**
     * @param CustomerInterface $customer
     */
    public function execute(CustomerInterface $customer)
    {
        try {
            $subscriber = [
                'userInfo' => $this->customerDto->getUserInfoData($customer),
                'subscriptionType' => self::SUBSCRIPTION_TYPE
            ];

            $modelData = $this->json->serialize($subscriber);
            $history = $this->historyFactory->create();
            $history->setStatus(Status::PENDING)
                ->setAction(Action::ADD_SUBSCRIPTION)
                ->setServiceClass(AddSubscription::class)
                ->setEntityData($modelData);
            $history = $this->historyRepository->save($history);
            $this->processHistory->processById($history->getHistoryId());
        } catch (\Exception $e) {
            $e->getMessage();
        }
    }
}
