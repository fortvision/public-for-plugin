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
    public function getProductDataById($id) {
        $product = $this->productRepository->getById($id);
        return $this->getProductDataFromItem($product);
    }
    public function getProductDataFromItem(ProductInterface $product)
    {
        $skipName=['entity_id', 'quantity_and_stock_status','attribute_set_id', 'type_id', 'status', 'old_id', 'name', 'url_path', 'required_options', 'has_options', 'image_label', 'small_image_label', 'thumbnail_label', 'created_at', 'sku', 'updated_at', 'sku_type', 'price', 'price_type', 'tax_class_id',  'weight', 'weight_type', 'visibility', 'category_ids', 'news_from_date', 'news_to_date', 'country_of_manufacture', 'links_purchased_separately', 'samples_title', 'links_title', 'links_exist',  'short_description', 'description', 'shipment_type', 'image', 'small_image', 'thumbnail', 'swatch_image', 'media_gallery', 'gallery', 'url_key', 'meta_title', 'meta_keyword', 'meta_description', 'special_price', 'special_from_date', 'special_to_date', 'cost', 'tier_price', 'minimal_price', 'msrp', 'msrp_display_actual_price_type', 'price_view', 'page_layout', 'options_container', 'custom_layout_update', 'custom_layout_update_file', 'custom_design_from', 'custom_design_to', 'custom_design', 'custom_layout', 'gift_message_available', 'attachment'];

        $categoryIds = $product->getCategoryIds();
        $categories=[];
        $categoryString = '';
        $first=true;
        foreach ($categoryIds as $category) {
            $categoryInstance = $this->categoryRepository->get($category);
            $categories[]=["id"=>$category, "name"=>$categoryInstance->getName()];
            $categoryString=$categoryString.($first?'':', ').$categoryInstance->getName();
            $first=false;
        }


        $attributes = $product->getAttributes();
        $attributesValues=[];
        foreach($attributes as $a)
        {
           $aname = $a->getName();
           if (!in_array($aname, $skipName)) {
               $avalue = $product->getData($aname);
               $attributesValues[$aname]=$avalue;
           }
        }

        $data = array(
            'id' => (int) $product->getId(),
            'customerProductId' => (int) $product->getId(),
            'websitesids'=>$product->getWebsiteIds() ,
            'category' => $categoryString,
            'name' => (string) $product->getName(),
            'description' =>(string) $product->getData('description'),
            'description_short' => $product->getData('short_description'), // )!_ CHECK
            'date_modified' => $product->getData('updated_at'), // )!_ CHECK
            'date_created' => $product->getData('created_at'), // )!_ CHECK ->date('Y-m-d H:i:s')->date('Y-m-d H:i:s')
           // 'sale_price' => (float)$product->getData('price'),
            // with discount
            'regular_price' => (float)$product->getData('price'),
            // base price
            'brand' => (string) $product->getData('brand'), // ok
            'discountedValue' => (float) $product->getFinalPrice(),
//
       //     'currency' => get_woocommerce_currency(),
         //   'tags' => $this->getTaxonomy($productId, 'product_tag'),
            'sku' => (string) $product->getData('sku'),
            'attributes' => $attributesValues,
            'imageUrl' =>  $product->getData('image'),
            'productUrl' => (string) stripslashes($product->getProductUrl()),
            'dimensions' => array(
                'weight' => $product->getData('weight'),
               'length' => $product->getData('length'),
               'width' => $product->getData('width'),
               'height' => $product->getData('height'),
            )
        );
        if (!isset($data['description']) || strlen($data['description'])===0) $data['description']=' ';
        if (!isset($data['brand']) || strlen($data['brand'])===0) $data['brand']=' ';
        if (!isset($data['imageUrl']) || strlen($data['imageUrl'])===0) $data['imageUrl']=' ';

        return $data;
    }

    /**
     * @param $item
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProductData(CartItemInterface $item)
    {
        $skipName=['entity_id', 'quantity_and_stock_status','attribute_set_id', 'type_id', 'status', 'old_id', 'name', 'url_path', 'required_options', 'has_options', 'image_label', 'small_image_label', 'thumbnail_label', 'created_at', 'sku', 'updated_at', 'sku_type', 'price', 'price_type', 'tax_class_id',  'weight', 'weight_type', 'visibility', 'category_ids', 'news_from_date', 'news_to_date', 'country_of_manufacture', 'links_purchased_separately', 'samples_title', 'links_title', 'links_exist',  'short_description', 'description', 'shipment_type', 'image', 'small_image', 'thumbnail', 'swatch_image', 'media_gallery', 'gallery', 'url_key', 'meta_title', 'meta_keyword', 'meta_description', 'special_price', 'special_from_date', 'special_to_date', 'cost', 'tier_price', 'minimal_price', 'msrp', 'msrp_display_actual_price_type', 'price_view', 'page_layout', 'options_container', 'custom_layout_update', 'custom_layout_update_file', 'custom_design_from', 'custom_design_to', 'custom_design', 'custom_layout', 'gift_message_available', 'attachment'];

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
        $attributes = $product->getAttributes();
        $attributesValues=[];
        foreach($attributes as $a)
        {
            $aname = $a->getName();
            if (!in_array($aname, $skipName)) {
                $avalue = $product->getData($aname);
                $attributesValues[$aname]=$avalue;
            }
        }

        $productData = [
            'customerProductId' => (int) $product->getId(),
            'name' => (string) $product->getName(),
            'description' => (string) $product->getDescription(),
            'category' => (string) $categoryName,
            'brand' => (string) $product->getBrand(),
            'discountedValue' => (float) $product->getFinalPrice(),
            'currency' => (string) $this->storeManager->getStore()->getCurrentCurrency()->getCurrencyCode(),
            'discountName' => ' ',
            'discountValue' => (float) $item->getBaseDiscountAmount(),
            'sku' => (string) $product->getData('sku'),
            'attributes' => $attributesValues,
            'imageUrl' =>  $product->getData('image'),
      //      'imageUrl' => (string) stripslashes($product->getData('small_image')),
            'productUrl' => (string) stripslashes($product->getProductUrl()),
            'volume' => $itemQty
        ];
        if (!isset($productData['description']) || strlen($productData['description'])===0) $productData['description']=' ';
        if (!isset($productData['brand']) || strlen($productData['brand'])===0) $productData['brand']=' ';
        if (!isset($productData['imageUrl']) || strlen($productData['imageUrl'])===0) $productData['imageUrl']=' ';

        return $productData;
    }
}
