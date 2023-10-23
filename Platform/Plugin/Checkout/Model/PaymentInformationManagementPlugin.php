<?php

namespace Fortvision\Platform\Plugin\Checkout\Model;

use Magento\Checkout\Api\PaymentInformationManagementInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\PaymentInterface;

/**
 * Class PaymentInformationManagementPlugin
 * @package Fortvision\Platform\Plugin\Checkout\Model
 */
class PaymentInformationManagementPlugin
{
    /**
     * @var CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * PaymentInformationManagementPlugin constructor.
     * @param CartRepositoryInterface $cartRepository
     */
    public function __construct(
        CartRepositoryInterface $cartRepository
    ) {
        $this->cartRepository = $cartRepository;
    }

    /**
     * @param PaymentInformationManagementInterface $subject
     * @param $cartId
     * @param PaymentInterface $paymentMethod
     * @param AddressInterface|null $billingAddress
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function beforeSavePaymentInformationAndPlaceOrder(
        PaymentInformationManagementInterface $subject,
        $cartId,
        PaymentInterface $paymentMethod,
        AddressInterface $billingAddress = null
    ) {
        $fortvision = $paymentMethod->getExtensionAttributes();
        $quote = $this->cartRepository->getActive($cartId);
        $fortvisionSubscription = $fortvision->getFortvisionSubscription();
        $quote->setFortvisionSubscription($fortvisionSubscription);
        $this->cartRepository->save($quote);
    }
}
