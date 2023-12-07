<?php

namespace Fortvision\Platform\Setup\Patch\Data;

use Fortvision\Platform\Model\Api\MainService as MainVisionService;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\FlagManager;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Store\Model\ScopeInterface;

class Initial implements DataPatchInterface
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

    private $scopeConfig;
    private $configWriter;
    private MainVisionService $mainVisionService;
    private ModuleDataSetupInterface $moduleDataSetup;

    public function __construct(
        \Magento\Cms\Model\PageFactory    $pageFactory,
        \Magento\Store\Model\StoreFactory $storeFactory,
        FlagManager                       $flagManager,
        WriterInterface $configWriter,
        ModuleDataSetupInterface $moduleDataSetup,

        ScopeConfigInterface     $scopeConfig,
        MainVisionService                                  $mainVisionService,

        \Psr\Log\LoggerInterface          $logger
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->mainVisionService = $mainVisionService;
        $this->moduleDataSetup = $moduleDataSetup;

        $this->logger = $logger;
        $this->flagManager = $flagManager;
        $this->configWriter = $configWriter;


        if ($logger) {
            $logger->debug('FV INSTALL UPGRADE');
        }
    }
/*
    public
    function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
       //  echo('TEST');
        $setup->endSetup();
    }
*/
    public static function getDependencies()
    {
        // TODO: Implement getDependencies() method.
        return [];
    }

    public function getAliases()
    {
        return [];

        // TODO: Implement getAliases() method.
    }

    public function apply()
    {

        $flagCode = 'fortvision_magento_id';
        $current = false;
        $currentScope = false;
        try {
            $current = $this->flagManager->getFlagData($flagCode);
            $currentScope = $this->scopeConfig->getValue(self::XML_PATH_GENERAL_DB_MAGENTO, ScopeInterface::SCOPE_WEBSITES);

        }
        catch(\Fortvision\Platform\Setup\Exception $e) {
        }
        if (!$current) {
            $current=hash_hmac('sha256',uniqid($more_entropy = true) . uniqid($more_entropy = true) . uniqid($more_entropy = true), 'secret');
            $this->flagManager->saveFlag($flagCode,$current );
        }
        $this->configWriter->save(self::XML_PATH_GENERAL_DB_MAGENTO, $current);
        $res = $this->mainVisionService->doSyncDataRegular();

        // TODO: Implement apply() method.
    }
}
