<?php

namespace Fortvision\Platform\Observer;

use Fortvision\Platform\Provider\GeneralSettings;
use Fortvision\Platform\Service\CartUpdate;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class UpdateCartObserver
 * @package Fortvision\Platform\Observer
 */
class UpdateCartObserver implements ObserverInterface
{
    /**
     * @var CartUpdate
     */
    protected $cartUpdate;

    /**
     * @var GeneralSettings
     */
    protected $generalSettings;

    /**
     * @var CategoryFactory
     */
    protected $categoryFactory;

    /**
     * UpdateCartObserver constructor.
     * @param CartUpdate $cartUpdate
     * @param GeneralSettings $generalSettings
     * @param CategoryFactory $categoryFactory
     */
    public function __construct(
        CartUpdate $cartUpdate,
        GeneralSettings $generalSettings,
        CategoryFactory $categoryFactory
    ) {
        $this->cartUpdate = $cartUpdate;
        $this->generalSettings = $generalSettings;
        $this->categoryFactory = $categoryFactory;
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

        $cart = $observer->getData('cart');
        $quote = $cart->getQuote();
        $this->cartUpdate->execute($quote);
        return $this;
    }
}
