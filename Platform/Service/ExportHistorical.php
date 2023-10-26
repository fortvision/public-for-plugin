<?php

namespace Fortvision\Platform\Service;


/**
 * Class Sync
 * @package Fortvision\Platform\Service
 */


use Fortvision\Platform\Helper\Data;
use Fortvision\Platform\Provider\GeneralSettings;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Store\Model\StoreManagerInterface;

class ExportHistorical
{

    protected $generalSettings;
    protected $categoryRepository;
    protected $productRepository;
    protected $storeManager;

    public function __construct(
    //    GeneralSettings $generalSettings,
    //    CategoryRepositoryInterface $categoryRepository,
     //   ProductRepositoryInterface $productRepository,
     //   StoreManagerInterface $storeManager,

    )
    {
      //  $this->generalSettings = $generalSettings;
       /// $this->categoryRepository = $categoryRepository;
       // $this->productRepository = $productRepository;
       // $this->storeManager = $storeManager;
    }

    public function process()
    {

    }


}
