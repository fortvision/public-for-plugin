<?php

namespace Fortvision\Sync\Provider;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\View\Element\BlockInterface;

/**
 * Class GeneralSettings
 * @package Fortvision\Sync\Provider
 */
class GeneralSettings extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    const XML_PATH_GENERAL_MODULE_ENABLE = 'fortvision_sync/general/is_enabled';
    const XML_PATH_GENERAL_DB_HOST = 'fortvision_sync/general/db_host';
    const XML_PATH_GENERAL_DB_NAME = 'fortvision_sync/general/db_name';
    const XML_PATH_GENERAL_DB_USERNAME = 'fortvision_sync/general/db_username';
    const XML_PATH_GENERAL_DB_PASSWORD = 'fortvision_sync/general/db_password';
    const XML_PATH_GENERAL_DB_MAGENTO = 'fortvision_sync/general/magento_id';
    const XML_PATH_GENERAL_DEBUG_MODE = 'fortvision_sync/general/debug_mode';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return bool
     */
    public function isModuleEnabled()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_GENERAL_MODULE_ENABLE, ScopeInterface::SCOPE_WEBSITE);
    }

    /**
     * @return mixed
     */
    public function getHost()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_GENERAL_DB_HOST, ScopeInterface::SCOPE_WEBSITE);
    }

    /**
     * @return mixed
     */
    public function getDbName()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_GENERAL_DB_NAME, ScopeInterface::SCOPE_WEBSITE);
    }

    /**
     * @return mixed
     */
    public function getDbUsername()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_GENERAL_DB_USERNAME, ScopeInterface::SCOPE_WEBSITE);
    }

    /**
     * @return mixed
     */
    public function getDbPassword()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_GENERAL_DB_PASSWORD, ScopeInterface::SCOPE_WEBSITE);
    }

    /**
     * @return bool
     */
    public function isDebugMode()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_GENERAL_DEBUG_MODE, ScopeInterface::SCOPE_WEBSITE);
    }

    public function toHtml()
    {
        return "<div>TEST</div>";
        // TODO: Implement toHtml() method.
    }
}
