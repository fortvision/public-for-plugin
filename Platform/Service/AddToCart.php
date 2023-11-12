<?php

namespace Fortvision\Platform\Service;

use Fortvision\Platform\Model\Api\DTO\Cart as OrderDTO;
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
use Fortvision\Platform\Logger\Integration as LoggerIntegration;


/**
 * Class AddToCart
 * @package Fortvision\Platform\Service
 */

class AddToCart
{
    const ADD_TO_CART_ENDPOINT = '/cart-management/product/add';
    const ENDPOINT = self::ADD_TO_CART_ENDPOINT;
    const ENDPOINT_REMOVE = '/cart-management/product/remove';
    const ENDPOINT_CHECKOUT = '/cart-management/cart/checkout';
    const ENDPOINT_CART_STATUS = '/cart-management/cart/status';


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
    private CartDTO $orderDTO;

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
        OrderDTO $orderDTO,

        CartDTO $cartDto,
        ProductDTO $productDto,
        CustomerDTO $customerDto,
        HistoryFactory $historyFactory,
        LoggerIntegration $logger,

        HistoryRepository $historyRepository,
        Json $json
    ) {
        $this->httpClient = $httpClient;
        $this->generalSettings = $generalSettings;
        $this->orderDTO = $orderDTO;

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
        return $result;
    }

    /**
     * @param $quote
     * @param $item
     */
    public function changeAmount(CartInterface $quote, CartItemInterface $item)
    {
        /**
         * @param $quote
         * @param $item
         */
        // $this->_logger->debug("changeAmount");

        $customer = $quote->getCustomer();
        if (!$customer->getId()) {
            $customer->setEmail($quote->getCustomerEmail());
            $customer->setFirstname($quote->getCustomerFirstname());
            $customer->setLastname($quote->getCustomerLastname());
        }

        $action=false;
        $prevQtyData = $item->getOrigData();
        $prevQty=false;
        $new = $item->getData()['qty'] ;

        if ($prevQtyData) {
            $prevQty = $prevQtyData['qty'];
            if (intval($prevQty)>intval($new)) $action='remove';
            if (intval($prevQty)<intval($new)) $action='add';
        }
        if (!$action) return;
        $this->_logger->debug("changeAm ".$new." ".$prevQty." ".$action);


        $url = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\UrlInterface');
        $cartData['endpoint'] = $action==='add'?self::ENDPOINT:self::ENDPOINT_REMOVE;

        $cartData['kind'] = $action==='add'?Action::ADD_TO_CART:Action::REMOVE_FROM_CART;
      //  echo( $cartData['endpoint']." ".$cartData['kind']);

        $cartData['magento_id'] = $this->generalSettings->getMagentoId();
        $cartData['hostURL'] = $_SERVER['HTTP_HOST'];
        $cartData['cart'] = $this->cartDto->getCartData($quote, 'updated');
        $cartData['product'] = $this->productDto->getProductData($item);
        $cartData['userInfo'] = $this->customerDto->getUserInfoData($customer);
        $cartData['volume'] = $quote->getItemsQty();
        $this->_logger->debug(json_encode($cartData));
       // echo('-----'.json_encode($cartData).'---');
        $this->process(json_encode($cartData));

    }


    /**
     * @param CartInterface $quote
     * @param CartItemInterface $item
     */
    public function updateCart(CartInterface $quote)
    {
        $this->_logger->debug("updateCart");

       // echo('1111updateCart1');
        /**
         * @param CartInterface $quote
         * @param CartItemInterface $item
         */
    }

 //   public function parseCheckoutOrder($orderId) {
    public function parseCheckoutOrder(CartInterface $quote, $status = '') {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $quote->collectTotals();
        $customer = $quote->getCustomer();

        if (!$customer->getId()) {
            $customer->setEmail($quote->getCustomerEmail());
            $customer->setFirstname($quote->getCustomerFirstname());
            $customer->setLastname($quote->getCustomerLastname());
        }


        // $order = $objectManager->create('\Magento\Sales\Model\OrderRepository')->get($orderId);

       // $payload = $this->orderDTO->getHistData($order);
        $cartData['kind'] = Action::CART_CHECKOUT;
        $cartData['magento_id'] = $this->generalSettings->getMagentoId();

        $cartData['endpoint'] = self::ENDPOINT_CHECKOUT;
        $cartData['hostURL'] = $_SERVER['HTTP_HOST'];
        $cartData['cart'] = $this->cartDto->getCartData($quote, $status);
        $cartData['userInfo'] = $this->customerDto->getUserInfoData($customer);
        $cartData['product'] = $cartData['cart']['products'][0];

      //  $cartData['userInfo'] = $this->customerDto->getUserInfoDataByOrder($order);
        $cartData['validation'] = true;

        //  $cartData['userInfo'] = [];
        $this->process(json_encode($cartData));

    }

    public function removeFromCart(CartInterface $quote, CartItemInterface $item)
    {
        $this->_logger->debug("removeFromCart");

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

            $cartData['magento_id'] = $this->generalSettings->getMagentoId();

            $cartData['endpoint'] = self::ENDPOINT_REMOVE;
            $cartData['hostURL'] = $_SERVER['HTTP_HOST'];
            $cartData['cart'] = $this->cartDto->getCartData($quote, 'remove');
            $cartData['product'] = $this->productDto->getProductData($item);
            $cartData['userInfo'] = $this->customerDto->getUserInfoData($customer);
            $this->process(json_encode($cartData));


        } catch (\Exception $e) {
            $this->_logger->debug("removeFromCart".$e->getMessage());
        }
    }

    public function addToCart(CartInterface $quote, CartItemInterface $item)
    {
     //   echo('addtocart')
        $this->_logger->debug('addToCart');
        try {
            $customer = $quote->getCustomer();
            if (!$customer->getId()) {
                $customer->setEmail($quote->getCustomerEmail());
                $customer->setFirstname($quote->getCustomerFirstname());
                $customer->setLastname($quote->getCustomerLastname());
            }

            $url = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\UrlInterface');
            $cartData['endpoint'] = self::ENDPOINT;

            $cartData['kind'] = Action::ADD_TO_CART;

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
            $this->_logger->debug("addToCartException".$e->getMessage());

        }
    }
}
