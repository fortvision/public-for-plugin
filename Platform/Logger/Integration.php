<?php

namespace Fortvision\Platform\Logger;

use Monolog\Logger;
use Fortvision\Platform\Provider\GeneralSettings;
use Fortvision\Platform\Service\MainVision;

/**
 * Class Integration
 * @package Fortvision\Platform\Logger
 */
class Integration extends Logger
{
    /**
     * @var GeneralSettings
     */
    protected $generalSettings;
    protected $mainVision;

    /**
     * Integration constructor.
     * @param GeneralSettings $generalSettings
     * @param string $name
     * @param array $handlers
     * @param array $processors
     */
    public function __construct(
        GeneralSettings $generalSettings,
        $name = 'loggerFortvisionIntegration',
        $handlers = [],
        $processors = []
    ) {
        $this->generalSettings = $generalSettings;
      //  $this->mainVision = $mainVision;
        parent::__construct($name, $handlers, $processors);
    }

    /**
     * @param int $level
     * @param string $message
     * @param array $context
     * @return bool
     */
    // public function addRecord($level, $message, array $context = [])
    // {
    //     if ($level < Logger::ERROR && !$this->generalSettings->isDebugMode()) {
    //         return false;
    //     }

    //     return parent::addRecord($level, $message, $context);
    // }

    /**
     * @param $message
     * @param array $context
     * @return bool
     */
    public function apiDebug($message, array $context = [])
    {
        // if (!$this->generalSettings->isDebugMode()) {
        //     return false;
        // }

        return parent::debug($message, $context);
    }

    /**
     * @param string $message
     * @param array|null $context
     * @return bool
     */
    // public function debug($message, array $context = null)
    // {
    //     if (is_null($context)) {
    //         $context = [];
    //     }
    //     return parent::debug($message, $context);
    // }
}
