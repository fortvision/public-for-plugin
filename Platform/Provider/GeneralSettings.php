<?php

namespace Fortvision\Platform\Provider;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\ResourceModel\Website\CollectionFactory as WebsiteCollectionFactory;
use Magento\Framework\FlagManager;

/**
 * Class GeneralSettings
 * @package Fortvision\Platform\Provider
 */
class GeneralSettings
{
    // const PRODUCTION_URL = 'https://fb.fortvision.com/fb';
    const PRODUCTION_URL = 'https://magentotools.fortvision.net';
    const DEVELOP_URL = 'https://fbdev.fortvision.com:4443';

    const XML_PATH_GENERAL_MODULE_ENABLE = 'fortvision_platform/general/is_enabled';
    const XML_PATH_GENERAL_PUBLISHER = 'fortvision_platform/general/publisher';
    const XML_PATH_GENERAL_MAGENTO_ID = 'fortvision_platform/general/magento_id';
    const XML_PATH_GENERAL_DEVELOP_MODE = 'fortvision_platform/general/develop_mode';
    const XML_PATH_GENERAL_SSL_VERIFY = 'fortvision_platform/general/ssl_verify';
    const XML_PATH_GENERAL_USER = 'fortvision_platform/general/user';
    const XML_PATH_GENERAL_PASSWORD = 'fortvision_platform/general/password';
    const XML_PATH_GENERAL_DEBUG_MODE = 'fortvision_platform/general/debug_mode';

    const XML_PATH_EVENTS_CUSTOMER_LOGIN_ENABLE = 'fortvision_platform/events/customer_login';
    const XML_PATH_EVENTS_CART_MANAGEMENT_ENABLE = 'fortvision_platform/events/cart_management';

    const XML_PATH_MARKETING_PAGES = 'fortvision_platform/marketing/pages';
    const XML_PATH_MARKETING_CHECKBOX_TEXT = 'fortvision_platform/marketing/checkbox_text';
    const XML_PATH_MARKETING_CHECKBOX_LOCATION = 'fortvision_platform/marketing/checkbox_location';
    const XML_PATH_MARKETING_DEFAULT_CHECKED = 'fortvision_platform/marketing/default_checked';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
    protected $websiteCollectionFactory;
    private FlagManager $flagManager;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface     $scopeConfig,
        WebsiteCollectionFactory $websiteCollectionFactory,
        FlagManager                       $flagManager,

    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->websiteCollectionFactory = $websiteCollectionFactory;
        $this->flagManager = $flagManager;

    }

    /**
     * @return bool
     */
    public function isModuleEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_GENERAL_MODULE_ENABLE,
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * @return mixed
     */
    public function getPublisher()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GENERAL_PUBLISHER,
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * @return mixed
     */
    public function getMagentoId()
    {
        $current='';
        try {
            $flagCode = 'fortvision_magento_id';

            $current = $this->flagManager->getFlagData($flagCode);
        }

        catch(Exception $e) {
        }
        return $current;

    }

    /**
     * @return mixed
     */
    public function getProductionUrl()
    {
        return self::PRODUCTION_URL;
    }

    /**
     * @return mixed
     */
    public function getDevelopUrl()
    {
        return self::DEVELOP_URL;
    }


    public function toOptionArray()
    {
        return [['value' => 1, 'label' => __('Yes')], ['value' => 0, 'label' => __('No')]];
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->isDevelopMode() ? $this->getDevelopUrl() : $this->getProductionUrl();
    }

    /**
     * @return bool
     */
    public function isDevelopMode()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_GENERAL_DEVELOP_MODE,
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * @return bool
     */
    public function useSslVerify()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_GENERAL_SSL_VERIFY,
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GENERAL_USER,
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GENERAL_PASSWORD,
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * @return bool
     */
    public function isDebugMode()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_GENERAL_DEBUG_MODE,
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * @return bool
     */
    public function customerLoginEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_EVENTS_CUSTOMER_LOGIN_ENABLE,
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * @return bool
     */
    public function cartManagementEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_EVENTS_CART_MANAGEMENT_ENABLE,
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * @return mixed
     */
    public function getPages()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_MARKETING_PAGES,
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * @return mixed
     */
    public function getCheckboxText()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_MARKETING_CHECKBOX_TEXT,
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * @return mixed
     */
    public function getCheckboxLocation()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_MARKETING_CHECKBOX_LOCATION,
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * @return bool
     */
    public function isDefaultChecked()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_MARKETING_DEFAULT_CHECKED,
            ScopeInterface::SCOPE_WEBSITE
        );
    }
}
