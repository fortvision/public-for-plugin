<?php

namespace Fortvision\Platform\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\FlagManager;
use Magento\Store\Model\ResourceModel\Website\CollectionFactory as WebsiteCollectionFactory;

class MagentoId extends \Magento\Framework\App\Config\Value
{
    protected $flagManager;
    protected $scopeConfig;
/*
    public function __construct(
        ScopeConfigInterface     $scopeConfig,
    //    WebsiteCollectionFactory $websiteCollectionFactory,
     //   FlagManager                       $flagManager,

    )
    {
        $this->scopeConfig = $scopeConfig;
     //   $this->websiteCollectionFactory = $websiteCollectionFactory;
       // $this->flagManager = $flagManager;

    } */
    public function getValue()
    {
        return '---';

    }

    public function beforeSave()
    {

        parent::beforeSave();
    }
}
