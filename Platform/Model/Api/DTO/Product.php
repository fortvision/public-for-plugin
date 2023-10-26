<?php

namespace Fortvision\Platform\Model\Api\DTO;

use Fortvision\Platform\Helper\Data;
use Fortvision\Platform\Provider\GeneralSettings;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Product
 * @package Fortvision\Platform\Model\Api\DTO
 */
class Product
{
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
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * Product constructor.
     * @param GeneralSettings $generalSettings
     * @param DateTime $date
     * @param StoreManagerInterface $storeManager
     * @param Data $helper
     * @param CategoryRepositoryInterface $categoryRepository
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        GeneralSettings $generalSettings,
        DateTime $date,
        StoreManagerInterface $storeManager,
        Data $helper,
        CategoryRepositoryInterface $categoryRepository,
        ProductRepositoryInterface $productRepository
    ) {
        $this->generalSettings = $generalSettings;
        $this->date = $date;
        $this->storeManager = $storeManager;
        $this->helper = $helper;
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
    }

    //'\Magento\Catalog\Api\Data\ProductInterface'
    public function getProductDataFromItem(ProductInterface $product)
    {
        $productData = [
            'customerProductId' => (int) $product->getId(),
            'name' => (string) $product->getName(),
            'description' => (string) $product->getDescription(),
        //    'category' => (string) $categoryName,
            'brand' => (string) $product->getBrand(),
            'websitesids'=>$product->getWebsiteIds() ,

            'discountedValue' => (float) $product->getFinalPrice(),
            'currency' => (string) $this->storeManager->getStore()->getCurrentCurrency()->getCurrencyCode(),
            'discountName' => '',
         //   'discountValue' => (float) $item->getBaseDiscountAmount(),
            //      'imageUrl' => (string) stripslashes($product->getData('small_image')),
            'productUrl' => (string) stripslashes($product->getProductUrl()),
           'url_key'=> $product->getData('url_key')
         //
            //  'volume' => $itemQty
        ];


        $data = array(
            'id' => (int) $product->getId(),
            'websitesids'=>$product->getWebsiteIds() ,

            'name' => (string) $product->getName(),
            'description' =>(string) $product->getDescription(),
         ///   'description_short' => $product->get_short_description(), // )!_ CHECK
          //  'date_created' => $product->get_date_created()->date('Y-m-d H:i:s'),   // )!_ CHECK
         //   'date_modified' => $product->get_date_modified()->date('Y-m-d H:i:s'), // )!_ CHECK
            'sale_price' => (float)$product->get_price(),
            // with discount
            'regular_price' => (float)$product->get_regular_price(),
            // base price
         //   'sku' => $product->get_sku(),
            'brand' => (string) $product->getData('brand'), // ok
         //   'stock' => $product->get_stock_quantity(),
            'discountedValue' => (float) $product->getFinalPrice(), // ok
//
       //     'currency' => get_woocommerce_currency(),
         //   'categories' => $this->getTaxonomy($productId, 'product_cat'),
         //   'tags' => $this->getTaxonomy($productId, 'product_tag'),
         //   'attributes' => $this->getAttributes($productId),
            'imageUrl' =>  $product->getData('image'),
            'productUrl' => (string) stripslashes($product->getProductUrl()),
            'dimensions' => array(
              //  'weight' => $product->get_weight(),
              //  'length' => $product->get_length(),
              //  'width' => $product->get_width(),
               // 'height' => $product->get_height(),
            )
        );

        /*




         *
         * */

        return $data;
    }

    /**
     * @param $item
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProductData(CartItemInterface $item)
    {
        $itemQty = $item->getQty();
        $item = $item->getHasChildren() ? $item->getChildren()[0] : $item;
        $itemProduct = $item->getProduct();
        $product = $this->productRepository->getById($itemProduct->getId());
        $categoryName = '';

        if ($categoryIds = $product->getCategoryIds()) {
            $categoryId = array_shift($categoryIds);
            $category = $this->categoryRepository->get($categoryId);
            $categoryName = $category->getName();
        }

        $productData = [
            'customerProductId' => (int) $product->getId(),
            'name' => (string) $product->getName(),
            'description' => (string) $product->getDescription(),
            'category' => (string) $categoryName,
            'brand' => (string) $product->getBrand(),
            'discountedValue' => (float) $product->getFinalPrice(),
            'currency' => (string) $this->storeManager->getStore()->getCurrentCurrency()->getCurrencyCode(),
            'discountName' => '',
            'discountValue' => (float) $item->getBaseDiscountAmount(),
      //      'imageUrl' => (string) stripslashes($product->getData('small_image')),
            'productUrl' => (string) stripslashes($product->getProductUrl()),
            'volume' => $itemQty
        ];

        return $productData;
    }
}
