<?php

namespace Fortvision\Platform\Plugin\Checkout\Model;

use Magento\Checkout\Api\GuestPaymentInformationManagementInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Quote\Api\GuestCartRepositoryInterface;

/**
 * Class GuestPaymentInformationManagementPlugin
 * @package Fortvision\Platform\Plugin\Checkout\Model
 */
class GuestPaymentInformationManagementPlugin
{
    /**
     * @var CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * @var GuestCartRepositoryInterface
     */
    protected $guestCartRepository;

    /**
     * GuestPaymentInformationManagementPlugin constructor.
     * @param CartRepositoryInterface $cartRepository
     * @param GuestCartRepositoryInterface $guestCartRepository
     */
    public function __construct(
        CartRepositoryInterface $cartRepository,
        GuestCartRepositoryInterface $guestCartRepository
    ) {
        $this->cartRepository = $cartRepository;
        $this->guestCartRepository = $guestCartRepository;
    }

    /**
     * @param GuestPaymentInformationManagementInterface $subject
     * @param $cartId
     * @param $email
     * @param PaymentInterface $paymentMethod
     * @param AddressInterface|null $billingAddress
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function beforeSavePaymentInformationAndPlaceOrder(
        GuestPaymentInformationManagementInterface $subject,
        $cartId,
        $email,
        PaymentInterface $paymentMethod,
        AddressInterface $billingAddress = null
    ) {
        $fortvision = $paymentMethod->getExtensionAttributes();
        $quote = $this->guestCartRepository->get($cartId);
        $fortvisionSubscription = $fortvision->getFortvisionSubscription();
        $quote->setFortvisionSubscription($fortvisionSubscription);
        $this->cartRepository->save($quote);
    }
}
