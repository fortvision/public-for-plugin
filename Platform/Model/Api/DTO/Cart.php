<?php

namespace Fortvision\Platform\Model\Api\DTO;

use Fortvision\Platform\Helper\Data;
use Fortvision\Platform\Provider\GeneralSettings;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Cart
 * @package Fortvision\Platform\Model\Api\DTO
 */
class Cart
{
    /**
     * @var Customer
     */
    protected $customerDto;

    /**
     * @var Product
     */
    protected $productDto;

    /**
     * @var GeneralSettings
     */
    protected $generalSettings;

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
     * Order constructor.
     * @param Customer $customerDto
     * @param Product $productDto
     * @param GeneralSettings $generalSettings
     * @param DateTime $date
     * @param StoreManagerInterface $storeManager
     * @param Data $helper
     */
    public function __construct(
        Customer $customerDto,
        Product $productDto,
        GeneralSettings $generalSettings,
        DateTime $date,
        StoreManagerInterface $storeManager,
        Data $helper
    ) {
        $this->customerDto = $customerDto;
        $this->productDto = $productDto;
        $this->generalSettings = $generalSettings;
        $this->date = $date;
        $this->storeManager = $storeManager;
        $this->helper = $helper;
    }

    /**
     * @param CartInterface $quote
     * @param string $status
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getHistData(OrderInterface $order)
    {

        $pids=$order->getItems();
        $products=[];
        $totalVolume=0;
        foreach ($pids as $pid) {
            $payload= $this->productDto->getProductDataById($pid->getProductId());
            $payload['volume']=$pid->getQtyOrdered();
            $totalVolume+= $payload['volume'];
//echo("PP".json_encode($payload));
            $products[]=$payload;

  //         echo("P)))-1:".$pid->getProductId()."\n");
           //echo("P)))-2:".."\n");
        }
     //   echo("PIDS".json_encode($pids)."\n");
       $siteid= $order->getStore()->getWebsiteId();
        $payload=['status'=>$order->getStatus(),'websitesids'=>[$siteid]];
        $payload['products'] = $products;
        $payload['discountedValue'] = (float) $order->getBaseGrandTotal();
        $payload['volume'] = (int) $totalVolume;
        $payload['customer_id'] = (int) $order->getCustomerId();
        $payload['couponId'] = (string) $order->getCouponCode();
      //  $payload['product'] =  $order->getItems();
        $payload['discountValue'] = (float) abs($order->getBaseSubtotalWithDiscount() - $order->getBaseSubtotal());
        $payload['cmsStatus'] = (string) $order->getStatus();
     //   $cartItems = $order->getAllVisibleItems() ?? [];
       // $payload['products'] = [];
    //    foreach ($cartItems as $cartItem) {
      //      $payload['products'][] ='';// $this->productDto->getProductDataFromItem($cartItem);
      //  }
       // var_dump($order->getItems());
        echo(json_encode($payload)."\n\n");
        return $payload;

    }

    /**
     * @param CartInterface $quote
     * @param string $status
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCartData(CartInterface $quote, $status = '')
    {
        $quote->collectTotals()->setTotalsCollectedFlag(false);
        $cartData = [];
        $cartItems = $quote->getAllVisibleItems() ?? [];
        $cartData['products'] = [];
        foreach ($cartItems as $cartItem) {
            $cartData['products'][] = $this->productDto->getProductData($cartItem);
        }
        $quote->collectTotals();
        $cartData['discountedValue'] = (float) $quote->getBaseGrandTotal();
        $cartData['volume'] = (int) $quote->getItemsQty();
        $cartData['couponId'] = (string) $quote->getCouponCode();
        $cartData['discountValue'] = (float) abs($quote->getBaseSubtotalWithDiscount() - $quote->getBaseSubtotal());
        $cartData['cmsStatus'] = (string) $status;

        return $cartData;
    }
}
