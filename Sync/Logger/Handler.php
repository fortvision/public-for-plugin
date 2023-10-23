<?php

namespace Fortvision\Sync\Logger;

/**
 * Class Handler
 * @package Fortvision\Sync\Logger
 */
class Handler extends \Magento\Framework\Logger\Handler\Base
{
    /**
     * @var string
     */
    protected $fileName = '/var/log/fortvision-sync.log';

    /**
     * @var int
     */
    protected $loggerType = \Monolog\Logger::DEBUG;
}
