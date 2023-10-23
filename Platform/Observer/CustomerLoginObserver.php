<?php

namespace Fortvision\Platform\Observer;

use Fortvision\Platform\Provider\GeneralSettings;
use Fortvision\Platform\Service\CustomerLogin;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class CustomerLoginObserver
 * @package Fortvision\Platform\Observer
 */
class CustomerLoginObserver implements ObserverInterface
{
    /**
     * @var CustomerLogin
     */
    protected $customerLogin;

    /**
     * @var GeneralSettings
     */
    protected $generalSettings;

    /**
     * CustomerLoginObserver constructor.
     * @param CustomerLogin $customerLogin
     * @param GeneralSettings $generalSettings
     */
    public function __construct(
        CustomerLogin $customerLogin,
        GeneralSettings $generalSettings
    ) {
        $this->customerLogin = $customerLogin;
        $this->generalSettings = $generalSettings;
    }

    /**
     * @param Observer $observer
     * @return $this|void
     */
    public function execute(Observer $observer)
    {
        if (!$this->generalSettings->customerLoginEnabled()) {
            return $this;
        }
        $customer = $observer->getEvent()->getCustomer()->getDataModel();
        $this->customerLogin->execute($customer);
        return $this;
    }
}
