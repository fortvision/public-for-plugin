<?php

namespace Fortvision\Platform\Logger;

/**
 * Class Handler
 * @package Fortvision\Platform\Logger
 */
class Handler extends \Magento\Framework\Logger\Handler\Base
{
    /**
     * @var string
     */
    protected $fileName = '/var/log/fortvision-integration.log';

    /**
     * @var int
     */
    protected $loggerType = \Monolog\Logger::DEBUG;
}
