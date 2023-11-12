<?php

namespace Fortvision\Platform\Service;

use Fortvision\Platform\Model\Api\DTO\Cart as CartDTO;
use Fortvision\Platform\Model\Api\DTO\Customer as CustomerDTO;
use Fortvision\Platform\Model\History\Action;
use Fortvision\Platform\Model\History\Status;
use Fortvision\Platform\Model\HistoryFactory;
use Fortvision\Platform\Model\HistoryProcess;
use Fortvision\Platform\Model\HistoryRepository;
use Fortvision\Platform\Model\Rest\HttpClient;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Quote\Api\Data\CartInterface;
use Fortvision\Platform\Provider\GeneralSettings;

/**
 * Class CartCheckout
 * @package Fortvision\Platform\Service
 */
class CartCheckout
{
    const ADD_TO_CART_ENDPOINT = '/cart-management/cart/checkout';
    const ENDPOINT = self::ADD_TO_CART_ENDPOINT;

    /**
     * @var HttpClient
     */
    protected $httpClient;

    /**
     * @var CartDTO
     */
    protected $cartDto;

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
    private GeneralSettings $generalSettings;

    /**
     * AddToCart constructor.
     * @param HttpClient $httpClient
     * @param HistoryProcess $processHistory
     * @param CartDTO $cartDto
     * @param CustomerDTO $customerDto
     * @param HistoryFactory $historyFactory
     * @param HistoryRepository $historyRepository
     * @param Json $json
     */
    public function __construct(
        HttpClient $httpClient,
        HistoryProcess $processHistory,
        CartDTO $cartDto,
        GeneralSettings $generalSettings,

        CustomerDTO $customerDto,
        HistoryFactory $historyFactory,
        HistoryRepository $historyRepository,
        Json $json
    ) {
        $this->httpClient = $httpClient;
        $this->generalSettings = $generalSettings;

        $this->processHistory = $processHistory;
        $this->cartDto = $cartDto;
        $this->customerDto = $customerDto;
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
            self::ADD_TO_CART_ENDPOINT,
            $params,
            Request::HTTP_METHOD_PUT
        );
        $result = $response->getBody()->getContents();
    }

    /**
     * @param CartInterface $quote
     * @param string $status
     */
    public function execute(CartInterface $quote, $status = '')
    {
        try {
            $quote->collectTotals();
            $customer = $quote->getCustomer();

            if (!$customer->getId()) {
                $customer->setEmail($quote->getCustomerEmail());
                $customer->setFirstname($quote->getCustomerFirstname());
                $customer->setLastname($quote->getCustomerLastname());
            }

            $url = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\UrlInterface');
            $cartData['kind'] = Action::CART_CHECKOUT;
            $cartData['endpoint'] = self::ENDPOINT;
            $cartData['magento_id'] = $this->generalSettings->getMagentoId();

            $cartData['hostURL'] = $_SERVER['HTTP_HOST'];
            $cartData['cart'] = $this->cartDto->getCartData($quote, $status);
            $cartData['userInfo'] = $this->customerDto->getUserInfoData($customer);

            $modelData = $this->json->serialize($cartData);
            $history = $this->historyFactory->create();
            $history->setStatus(Status::PENDING)
                ->setAction(Action::CART_CHECKOUT)
                ->setServiceClass(CartCheckout::class)
                ->setEntityData($modelData);
            $history = $this->historyRepository->save($history);
            $this->processHistory->processById($history->getHistoryId());
        } catch (\Exception $e) {
            $e->getMessage();
        }
    }
}
