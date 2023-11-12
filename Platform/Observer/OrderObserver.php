<?php

namespace Fortvision\Platform\Observer;

use Fortvision\Platform\Provider\GeneralSettings;
use Magento\Framework\Event\ObserverInterface;
use Fortvision\Platform\Service\AddToCart;
use Fortvision\Platform\Logger\Integration as LoggerIntegration;
use Magento\Quote\Api\CartRepositoryInterface;

class OrderObserver implements ObserverInterface
{

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory
     */
    protected $_invoiceCollectionFactory;

    /**
     * @var \Magento\Sales\Api\InvoiceRepositoryInterface
     */
    protected $_invoiceRepository;

    /**
     * @var \Magento\Sales\Model\Service\InvoiceService
     */
    protected $_invoiceService;

    /**
     * @var \Magento\Framework\DB\TransactionFactory
     */
    protected $_transactionFactory;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $_orderRepository;
    private AddToCart $addToCart;
    private GeneralSettings $generalSettings;
    private LoggerIntegration $_logger;
    private CartRepositoryInterface $cartRepository;

    /**
     * @param \Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory $invoiceCollectionFactory
     * @param \Magento\Sales\Model\Service\InvoiceService $invoiceService
     * @param \Magento\Framework\DB\TransactionFactory $transactionFactory
     * @param \Magento\Sales\Api\InvoiceRepositoryInterface $invoiceRepository
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        AddToCart                                                          $addToCart,
        GeneralSettings                                                    $generalSettings,
        LoggerIntegration                                                  $logger,
        CartRepositoryInterface $cartRepository,

        \Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory $invoiceCollectionFactory,
        \Magento\Sales\Model\Service\InvoiceService                        $invoiceService,
        \Magento\Framework\DB\TransactionFactory                           $transactionFactory,
        \Magento\Sales\Api\InvoiceRepositoryInterface                      $invoiceRepository,
        \Magento\Sales\Api\OrderRepositoryInterface                        $orderRepository
    )
    {
        $this->_logger = $logger;
        $this->cartRepository = $cartRepository;

        $this->_invoiceCollectionFactory = $invoiceCollectionFactory;
        $this->_invoiceService = $invoiceService;
        $this->_transactionFactory = $transactionFactory;
        $this->_invoiceRepository = $invoiceRepository;
        $this->_orderRepository = $orderRepository;
        $this->addToCart = $addToCart;
        $this->generalSettings = $generalSettings;
    }


    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /*
        $order = $observer->getEvent()->getOrder();
        $orderId = $order->getId();
        $customer= $order->getCustomer();
        $this->addToCart->parseCheckoutOrder($orderId, $customer); */


        $order = $observer->getData('order');
        $quoteId = $order->getQuoteId();
        $quote = $this->cartRepository->get($quoteId);
        $this->addToCart->parseCheckoutOrder($quote, $order->getStatus());

    }
    /*
        protected function createInvoice($orderId)
        {
            try
            {
                $order = $this->_orderRepository->get($orderId);
                if ($order)
                {
                    $invoices = $this->_invoiceCollectionFactory->create()
                      ->addAttributeToFilter('order_id', array('eq' => $order->getId()));

                    $invoices->getSelect()->limit(1);

                    if ((int)$invoices->count() !== 0) {
                      $invoices = $invoices->getFirstItem();
                      $invoice = $this->_invoiceRepository->get($invoices->getId());
                      return $invoice;
                    }

                    if(!$order->canInvoice()) {
                        return null;
                    }

                    $invoice = $this->_invoiceService->prepareInvoice($order);
                    $invoice->setRequestedCaptureCase(\Magento\Sales\Model\Order\Invoice::CAPTURE_ONLINE);
                    $invoice->register();
                    $invoice->getOrder()->setCustomerNoteNotify(false);
                    $invoice->getOrder()->setIsInProcess(true);
                    $order->addStatusHistoryComment(__('Automatically INVOICED'), false);
                    $transactionSave = $this->_transactionFactory->create()->addObject($invoice)->addObject($invoice->getOrder());
                    $transactionSave->save();

                    return $invoice;
                }
            } catch (\Exception $e) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __($e->getMessage())
                );
            }
        }*/
}
