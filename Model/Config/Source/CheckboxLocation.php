<?php

namespace Fortvision\Platform\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class CheckboxLocation
 * @package Fortvision\Platform\Model\Config\Source
 */
class CheckboxLocation implements OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'after_order_record', 'label' => __('After order record')],
            ['value' => 'preview_the_order','label' => __('Preview the order (before sending)')]
        ];
    }
}
