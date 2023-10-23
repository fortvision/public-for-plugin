<?php

namespace Fortvision\Platform\Service;

use Fortvision\Platform\Model\Api\DTO\Cart as CartDTO;
use Fortvision\Platform\Model\Api\DTO\Customer as CustomerDTO;
use Fortvision\Platform\Model\Api\DTO\Product as ProductDTO;
use Fortvision\Platform\Model\History\Action;
use Fortvision\Platform\Model\History\Status;
use Fortvision\Platform\Model\HistoryFactory;
use Fortvision\Platform\Model\HistoryProcess;
use Fortvision\Platform\Model\HistoryRepository;
use Fortvision\Platform\Model\Rest\HttpClient;
use Fortvision\Platform\Provider\GeneralSettings;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Api\Data\CartItemInterface;

/**
 * Class AddToCart
 * @package Fortvision\Platform\Service
 */

class AddToCart
{
    const ADD_TO_CART_ENDPOINT = '/cart-management/product/add';
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
     * @var ProductDTO
     */
    protected $productDto;

    /**
     * @var CustomerDTO
     */
    protected $customerDto;

    /**
     * @var _logger
     */
    protected $_logger;

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
    protected $generalSettings;

    /**
     * AddToCart constructor.
     * @param HttpClient $httpClient
     * @param HistoryProcess $processHistory
     * @param CartDTO $cartDto
     * @param ProductDTO $productDto
     * @param CustomerDTO $customerDto
     * @param HistoryFactory $historyFactory
     * @param HistoryRepository $historyRepository
     * @param Json $json
     */
    public function __construct(
        HttpClient $httpClient,
        HistoryProcess $processHistory,
        GeneralSettings $generalSettings,

        CartDTO $cartDto,
        ProductDTO $productDto,
        CustomerDTO $customerDto,
        HistoryFactory $historyFactory,
        \Psr\Log\LoggerInterface $logger,

        HistoryRepository $historyRepository,
        Json $json
    ) {
        $this->httpClient = $httpClient;
        $this->generalSettings = $generalSettings;

        $this->processHistory = $processHistory;
        $this->cartDto = $cartDto;
        $this->productDto = $productDto;
        $this->customerDto = $customerDto;
        $this->historyFactory = $historyFactory;
        $this->historyRepository = $historyRepository;
        $this->json = $json;
        $this->_logger = $logger;
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
     * @param $quote
     * @param $item
     */
    public function execute(CartInterface $quote, CartItemInterface $item)
    {

        try {
            $customer = $quote->getCustomer();
            if (!$customer->getId()) {
                $customer->setEmail($quote->getCustomerEmail());
                $customer->setFirstname($quote->getCustomerFirstname());
                $customer->setLastname($quote->getCustomerLastname());
            }

            $url = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\UrlInterface');
            $cartData['kind'] = Action::ADD_TO_CART;
            $cartData['endpoint'] = self::ENDPOINT;
            $cartData['magento_id'] = $this->generalSettings->getMagentoId();
            $cartData['hostURL'] = $_SERVER['HTTP_HOST'];
            $cartData['cart'] = $this->cartDto->getCartData($quote, 'add');
            $cartData['product'] = $this->productDto->getProductData($item);
            $cartData['userInfo'] = $this->customerDto->getUserInfoData($customer);
            $cartData['volume'] = $quote->getItemsQty();
           // var_dump($cartData);
            $modelData = $this->json->serialize($cartData);
            $history = $this->historyFactory->create();
            $history->setStatus(Status::PENDING)
                ->setAction(Action::ADD_TO_CART)
                ->setServiceClass(AddToCart::class)
                ->setEntityData($modelData);
            $history = $this->historyRepository->save($history);
            $this->processHistory->processById($history->getHistoryId());
        } catch (\Exception $e) {
            echo($e->getMessage());
        }
    }
}
