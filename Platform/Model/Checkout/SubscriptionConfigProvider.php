<?php

namespace Fortvision\Platform\Model\Checkout;

use Fortvision\Platform\Provider\GeneralSettings;
use Magento\Checkout\Model\ConfigProviderInterface;

/**
 * Class SubscriptionConfigProvider
 * @package Fortvision\Platform\Model\Checkout
 */
class SubscriptionConfigProvider implements ConfigProviderInterface
{
    /**
     * @var array
     */
    protected $checkoutPages = [
        'checkout',
        'both'
    ];

    /**
     * @var GeneralSettings
     */
    protected $generalSettings;

    /**
     * SubscriptionConfigProvider constructor.
     * @param GeneralSettings $generalSettings
     */
    public function __construct(
        GeneralSettings $generalSettings
    ) {
        $this->generalSettings = $generalSettings;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return [
            'fortvision' => [
                'isAvailable' => in_array($this->generalSettings->getPages(), $this->checkoutPages),
                'checkboxLocation' => $this->generalSettings->getCheckboxLocation(),
                'checkboxText' => $this->generalSettings->getCheckboxText(),
                'checkboxChecked' => $this->generalSettings->isDefaultChecked()
            ]
        ];
    }
}
