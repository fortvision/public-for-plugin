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
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Api\Data\CartItemInterface;

/**
 * Class RemoveFromCart
 * @package Fortvision\Platform\Service
 */
class RemoveFromCart
{
    const REMOVE_FROM_CART_ENDPOINT = '/cart-management/product/remove';
    const ENDPOINT = self::REMOVE_FROM_CART_ENDPOINT;

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
     * RemoveFromCart constructor.
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
        CartDTO $cartDto,
        ProductDTO $productDto,
        CustomerDTO $customerDto,
        HistoryFactory $historyFactory,
        HistoryRepository $historyRepository,
        Json $json
    ) {
        $this->httpClient = $httpClient;
        $this->processHistory = $processHistory;
        $this->cartDto = $cartDto;
        $this->productDto = $productDto;
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
            self::REMOVE_FROM_CART_ENDPOINT,
            $params,
            Request::HTTP_METHOD_PUT
        );
        $result = $response->getBody()->getContents();
    }

    /**
     * @param CartInterface $quote
     * @param CartItemInterface $item
     */
    public function execute(CartInterface $quote, CartItemInterface $item)
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
            $cartData['kind'] = Action::REMOVE_FROM_CART;
            $cartData['endpoint'] = self::ENDPOINT;
            $cartData['hostURL'] = $_SERVER['HTTP_HOST'];
            $cartData['cart'] = $this->cartDto->getCartData($quote, 'remove');
            $cartData['product'] = $this->productDto->getProductData($item);
            $cartData['userInfo'] = $this->customerDto->getUserInfoData($customer);

            $modelData = $this->json->serialize($cartData);
            $history = $this->historyFactory->create();
            $history->setStatus(Status::PENDING)
                ->setAction(Action::REMOVE_FROM_CART)
                ->setServiceClass(RemoveFromCart::class)
                ->setEntityData($modelData);
            $history = $this->historyRepository->save($history);
            $this->processHistory->processById($history->getHistoryId());
        } catch (\Exception $e) {
            $e->getMessage();
        }
    }
}
