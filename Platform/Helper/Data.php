<?php

namespace Fortvision\Platform\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\FlagManager;

/**
 * Class Data
 * @package Fortvision\Platform\Helper
 */
class Data extends AbstractHelper
{
    /**
     * @var FlagManager
     */
    private $flagManager;

    /**
     * @param Context $context
     * @param FlagManager $flagManager
     */
    public function __construct(
        Context $context,
        FlagManager $flagManager
    ) {
        parent::__construct($context);
        $this->flagManager = $flagManager;
    }
}
