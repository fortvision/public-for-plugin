<?php

namespace Fortvision\Platform\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Pages
 * @package Fortvision\Platform\Model\Config\Source
 */
class Pages implements OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'disable','label' => __('Disable')],
            ['value' => 'checkout', 'label' => __('On checkout page')],
            ['value' => 'signup','label' => __('On signup page')],
            ['value' => 'both','label' => __('Both')]
        ];
    }
}
