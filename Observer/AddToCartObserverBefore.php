<?php

namespace Fortvision\Platform\Observer;

use Fortvision\Platform\Provider\GeneralSettings;
use Fortvision\Platform\Service\AddToCart;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\Product\Type;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class AddToCartObserver
 * @package Fortvision\Platform\Observer
 */
class AddToCartObserverBefore implements ObserverInterface
{
    /**
     * @var AddToCart
     */
    protected $addToCart;
    protected $_logger;

    /**
     * @var GeneralSettings
     */
    protected $generalSettings;

    /**
     * @var CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var
     */
    protected $addedItem;

    /**
     * AddToCartObserver constructor.
     * @param AddToCart $addToCart
     * @param GeneralSettings $generalSettings
     * @param CategoryFactory $categoryFactory
     */
    public function __construct(
        AddToCart                $addToCart,
        GeneralSettings          $generalSettings,
        \Psr\Log\LoggerInterface $logger,

        CategoryFactory          $categoryFactory
    )
    {
        $this->addToCart = $addToCart;
        $this->generalSettings = $generalSettings;
        $this->categoryFactory = $categoryFactory;
        $this->_logger = $logger;
    }

    /**
     * @param Observer $observer
     * @return $this|void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $item = $observer->getEvent()->getItem();
            $quote = $item->getQuote();
            if (isset($quote) && $item) {
                $this->addToCart->changeAmount($quote, $item);
            }
         //   return $this;
        } catch (Exception $ex) {
            // $this->_logger->debug('EXEC  EX');
            // $this->_logger->debug('EXEC ' . json_encode($ex));

        }


    }
}
