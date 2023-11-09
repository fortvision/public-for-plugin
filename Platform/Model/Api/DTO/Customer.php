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
    protected $addressRepository;
    private \Magento\Framework\ObjectManagerInterface $_objectManager;
    private \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository;

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
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        Session $customerSession
    ) {
        $this->generalSettings = $generalSettings;
        $this->date = $date;
        $this->storeManager = $storeManager;
        $this->helper = $helper;
        $this->cookieManager = $cookieManager;
        $this->customerSession = $customerSession;
        $this->_objectManager = $objectManager;

        $this->addressRepository = $addressRepository;
        $this->customerRepository = $customerRepository;
//            $this->_objectManager->create(
//            \Magento\Customer\Api\AddressRepositoryInterface::class
//        );
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
        $websitesids=$customer->getStore()->getWebsiteId();

        $customerData = $this->customerRepository->getById( $customer->getId());
        $billingAddressId = $customerData->getDefaultBilling();
        $shippingAddressId = $customerData->getDefaultShipping();
       /// echo("CID".$customer->getId()." ".$billingAddressId." ".$shippingAddressId);

        $billingAddress = isset($billingAddressId) && $billingAddressId>0? $this->addressRepository->getById($billingAddressId):false;

        $telephone =$billingAddress? $billingAddress->getTelephone():'';

      //  echo("!!!!!: CUSTOMER".$websitesids);
        $payload = ['email' => $customer->getEmail(),
            'phone'=>$telephone,
            // 'phone'=>$customer->getPhone(),
            'websitesids' => [$websitesids],
        'id'=>(string) $customer->getId(),
        'firstName'=>(string) $customer->getFirstname(),
        'lastName'=>(string) $customer->getLastname(),
        ];
      //  echo("PPP:".json_encode($payload));
      //  $payload['firstName'] => ;
     //   $payload['lastName'] => (string) $customer->getFirstname();

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
