<?php

namespace Fortvision\Platform\Model\Api\DTO;

use Fortvision\Platform\Helper\Data;
use Fortvision\Platform\Provider\GeneralSettings;
use Magento\Catalog\Api\CategoryRepositoryInterface;
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
