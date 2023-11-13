<?php

namespace Fortvision\Platform\Model\History;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

/**
 * Class Status
 * @package Fortvision\Platform\Model\History
 */
class Status extends AbstractSource
{
    const PENDING = 'pending';
    const FAILED = 'failed';
    const COMPLETED = 'completed';

    /**
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = [
                ['value' => self::PENDING, 'label' => __('Pending')],
                ['value' => self::FAILED, 'label' => __('Failed')],
                ['value' => self::COMPLETED, 'label' => __('Completed')],
            ];
        }
        return $this->_options;
    }
}
