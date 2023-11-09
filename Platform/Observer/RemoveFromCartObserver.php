<?php

namespace Fortvision\Platform\Observer;

use Fortvision\Platform\Provider\GeneralSettings;
use Fortvision\Platform\Service\RemoveFromCart;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Fortvision\Platform\Service\AddToCart;

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
    private AddToCart $addToCart;

    /**
     * RemoveFromCartObserver constructor.
     * @param RemoveFromCart $removeFromCart
     * @param GeneralSettings $generalSettings
     */
    public function __construct(
        RemoveFromCart $removeFromCart,
        AddToCart                $addToCart,

        GeneralSettings $generalSettings
    ) {
        $this->removeFromCart = $removeFromCart;
        $this->addToCart = $addToCart;
        $this->generalSettings = $generalSettings;
    }

    /**
     * @param Observer $observer
     * @return $this|void
     */
    public function execute(Observer $observer)
    {
      //  echo('deldel');
        if (!$this->generalSettings->cartManagementEnabled()) {
         //   return $this;
        }

        $item = $observer->getData('quote_item');
        $quote = $item->getQuote();
       // $this->removeFromCart->execute($quote, $item);
        $this->addToCart->removeFromCart($quote, $item);
        return $this;
    }
}
