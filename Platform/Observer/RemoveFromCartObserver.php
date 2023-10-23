<?php

namespace Fortvision\Platform\Observer;

use Fortvision\Platform\Provider\GeneralSettings;
use Fortvision\Platform\Service\RemoveFromCart;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class RemoveFromCartObserver
 * @package Fortvision\Platform\Observer
 */
class RemoveFromCartObserver implements ObserverInterface
{
    /**
     * @var RemoveFromCart
     */
    protected $removeFromCart;

    /**
     * @var GeneralSettings
     */
    protected $generalSettings;

    /**
     * RemoveFromCartObserver constructor.
     * @param RemoveFromCart $removeFromCart
     * @param GeneralSettings $generalSettings
     */
    public function __construct(
        RemoveFromCart $removeFromCart,
        GeneralSettings $generalSettings
    ) {
        $this->removeFromCart = $removeFromCart;
        $this->generalSettings = $generalSettings;
    }

    /**
     * @param Observer $observer
     * @return $this|void
     */
    public function execute(Observer $observer)
    {
        if (!$this->generalSettings->cartManagementEnabled()) {
            return $this;
        }

        $item = $observer->getData('quote_item');
        $quote = $item->getQuote();
        $this->removeFromCart->execute($quote, $item);
        return $this;
    }
}
