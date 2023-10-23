<?php
namespace Fortvision\Platform\Plugin\Checkout;

use Fortvision\Platform\Provider\GeneralSettings;

/**
 * Class LayoutProcessor
 * @package Fortvision\Platform\Plugin\Checkout
 */
class LayoutProcessor
{
    const SHIPPIN_STEP = 'preview_the_order';
    const BILLING_STEP = 'after_order_record';

    /**
     * @var GeneralSettings
     */
    protected $generalSettings;

    /**
     * LayoutProcessor constructor.
     * @param GeneralSettings $generalSettings
     */
    public function __construct(
        GeneralSettings $generalSettings
    ) {
        $this->generalSettings = $generalSettings;
    }

    /**
     * @param \Magento\Checkout\Block\Checkout\LayoutProcessor $subject
     * @param array $jsLayout
     * @return array
     */
    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        array  $jsLayout
    ) {
        if ($this->generalSettings->getCheckboxLocation() == self::SHIPPIN_STEP) {
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['before-form']['children']['fortvision_subscription'] = [
                'component' => 'Fortvision_Platform/js/checkout/view/subscription'
            ];
        }

        if ($this->generalSettings->getCheckboxLocation() == self::BILLING_STEP) {
            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
            ['payment']['children']['afterMethods']['children']['fortvision_subscription'] = [
                'component' => 'Fortvision_Platform/js/checkout/view/subscription'
            ];
        }

        return $jsLayout;
    }
}
