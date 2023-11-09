<?php

namespace Fortvision\Platform\Observer;

use Fortvision\Platform\Provider\GeneralSettings;
//use Fortvision\Platform\Service\AddSubscription;
use Fortvision\Platform\Service\CustomerLogin;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class AddSubscriptionObserver
 * @package Fortvision\Platform\Observer
 */
class AddSubscriptionObserver implements ObserverInterface
{

  //  protected $addSubscription;

    /**
     * @var GeneralSettings
     */
    protected $generalSettings;

    /**
     * @var RequestInterface
     */
    protected $request;
    private CustomerLogin $customerLogin;

    /**
     * AddSubscriptionObserver constructor.
     * @param GeneralSettings $generalSettings
     * @param RequestInterface $request
     */
    public function __construct(
        CustomerLogin $customerLogin,

      //  AddSubscription $addSubscription,
        GeneralSettings $generalSettings,
        RequestInterface $request
    ) {
       // $this->addSubscription = $addSubscription;
        $this->customerLogin = $customerLogin;

        $this->generalSettings = $generalSettings;
        $this->request = $request;
    }

    /**
     * @param Observer $observer
     * @return $this|void
     */
    public function execute(Observer $observer)
    {
        if (!$this->generalSettings->customerLoginEnabled()) {
           // return $this;
        }

       // if ($this->request->getParam('fortvision_subscription')) {
            $customer = $observer->getEvent()->getCustomer();

          //  $this->addSubscription->execute($customer);
            $this->customerLogin->addSubscription($customer);
      //  }
        return $this;
    }
}
