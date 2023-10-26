<?php

namespace Fortvision\Platform\Model\Api\DTO;

use Fortvision\Platform\Provider\GeneralSettings;
use Fortvision\Platform\Helper\Data;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Customer
 * @package Fortvision\Platform\Model\Api\DTO
 */
class Customer
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
     * @var CookieManagerInterface
     */
    protected $cookieManager;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * Customer constructor.
     * @param GeneralSettings $generalSettings
     * @param DateTime $date
     * @param StoreManagerInterface $storeManager
     * @param Data $helper
     * @param CookieManagerInterface $cookieManager
     * @param Session $customerSession
     */
    public function __construct(
        GeneralSettings $generalSettings,
        DateTime $date,
        StoreManagerInterface $storeManager,
        Data $helper,
        CookieManagerInterface $cookieManager,
        Session $customerSession
    ) {
        $this->generalSettings = $generalSettings;
        $this->date = $date;
        $this->storeManager = $storeManager;
        $this->helper = $helper;
        $this->cookieManager = $cookieManager;
        $this->customerSession = $customerSession;
    }

    /**
     * @param CustomerInterface $customer
     * @return array
     */
    public function getHistInfoData(CustomerInterface $customer) {
        $userInfo = [
           /// 'publisherId' => (string) $this->generalSettings->getPublisher(),
           // 'userId' => (string) $this->cookieManager->getCookie('fortvision_uuid'),
            'firstName' => (string) $customer->getFirstname(),
            'lastName' => (string) $customer->getLastname(),
            'email' => (string) $customer->getEmail(),
          //  'isLoggedIn' => (int) $this->customerSession->isLoggedIn()

        ];

        return $userInfo;
    }

    public function getHistData($customer) {
        $websitesids=$customer->getWebsiteIds();
        $payload=['email'=>$customer->getEmail(),'$websitesids'=>$websitesids];
        return $payload;
    }
    public function getUserInfoData(CustomerInterface $customer)
    {
        $userInfo = [
            'publisherId' => (string) $this->generalSettings->getPublisher(),
            'userId' => (string) $this->cookieManager->getCookie('fortvision_uuid'),
            'firstName' => (string) $customer->getFirstname(),
            'lastName' => (string) $customer->getLastname(),
            'email' => (string) $customer->getEmail(),
            'isLoggedIn' => (int) $this->customerSession->isLoggedIn()

        ];

        return $userInfo;
    }
}
