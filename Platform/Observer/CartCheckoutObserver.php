<?php

namespace Fortvision\Platform\Observer;

use Fortvision\Platform\Provider\GeneralSettings;
use Fortvision\Platform\Service\AddSubscription;
use Fortvision\Platform\Service\CartCheckout;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Api\CartRepositoryInterface;

/**
 * Class CartCheckoutObserver
 * @package Fortvision\Platform\Observer
 */
class CartCheckoutObserver implements ObserverInterface
{
    /**
     * @var CartCheckout
     */
    protected $cartCheckout;

    /**
     * @var AddSubscription
     */
    protected $addSubscription;

    /**
     * @var CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * @var GeneralSettings
     */
    protected $generalSettings;

    /**
     * CartCheckoutObserver constructor.
     * @param CartCheckout $cartCheckout
     * @param AddSubscription $addSubscription
     * @param CartRepositoryInterface $cartRepository
     * @param GeneralSettings $generalSettings
     */
    public function __construct(
        CartCheckout $cartCheckout,
        AddSubscription $addSubscription,
        CartRepositoryInterface $cartRepository,
        GeneralSettings $generalSettings
    ) {
        $this->cartCheckout = $cartCheckout;
        $this->addSubscription = $addSubscription;
        $this->cartRepository = $cartRepository;
        $this->generalSettings = $generalSettings;
    }

    /**
     * @param Observer $observer
     * @return $this|void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        if (!$this->generalSettings->cartManagementEnabled()) {
            return $this;
        }

        $order = $observer->getData('order');
        $quoteId = $order->getQuoteId();
        $quote = $this->cartRepository->get($quoteId);
        $this->cartCheckout->execute($quote, $order->getStatus());
        if ($quote->getFortvisionSubscription()) {
            $customer = $quote->getCustomer();
            if (!$customer->getId()) {
                $customer->setEmail($quote->getCustomerEmail());
                $customer->setFirstname($quote->getCustomerFirstname());
                $customer->setLastname($quote->getCustomerLastname());
            }

            $this->addSubscription->execute($customer);
        }
        return $this;
    }
}
