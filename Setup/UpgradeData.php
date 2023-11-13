<?php

namespace Fortvision\Platform\Setup;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\FlagManager;
use Magento\Store\Model\ScopeInterface;

use Magento\Framework\App\Config\Storage\WriterInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var \Magento\Cms\Model\PageFactory
     */
  //  private $pageFactory;
    protected $logger;
    private FlagManager $flagManager;
    const XML_PATH_GENERAL_DB_MAGENTO = 'fortvision_platform/general/magento_id';

    /**
     * @var \Magento\Store\Model\StoreFactory
     */
   // private $storeFactory;
    private $scopeConfig;
    private $configWriter;

    public function __construct(
        \Magento\Cms\Model\PageFactory    $pageFactory,
        \Magento\Store\Model\StoreFactory $storeFactory,
        FlagManager                       $flagManager,
        WriterInterface $configWriter,

        ScopeConfigInterface     $scopeConfig,

        \Psr\Log\LoggerInterface          $logger
    )
    {
      //  $this->pageFactory = $pageFactory;
      //  $this->storeFactory = $storeFactory;
        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;
        $this->flagManager = $flagManager;
        $this->configWriter = $configWriter;
        $flagCode = 'fortvision_magento_id';
        $current = false;
        $currentScope = false;
        try {
            $current = $this->flagManager->getFlagData($flagCode);
            $currentScope = $this->scopeConfig->getValue(self::XML_PATH_GENERAL_DB_MAGENTO, ScopeInterface::SCOPE_WEBSITES);

        }
        catch(Exception $e) {
        }
         if (!$current) {
             $current=hash_hmac('sha256',uniqid($more_entropy = true) . uniqid($more_entropy = true) . uniqid($more_entropy = true), 'secret');
             $this->flagManager->saveFlag($flagCode,$current );
         }
        $this->configWriter->save(self::XML_PATH_GENERAL_DB_MAGENTO, $current);


        // echo('TESTTESTTEST UPGRADE');
        if ($logger) {
            $logger->debug('FV UPGRADE');
        }
    }

    public
    function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        echo('TEST');
        $setup->endSetup();
    }
}
