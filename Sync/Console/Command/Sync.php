<?php

namespace Fortvision\Sync\Console\Command;

use Fortvision\Sync\Logger\Integration as LoggerIntegration;
use Fortvision\Sync\Service\Sync as SyncService;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Console\Cli;
use Magento\Framework\Exception\LocalizedException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Sync
 * @package Fortvision\Sync\Console\Command
 */
class Sync extends Command
{
    const SYNC_COMMAND = 'fortvision:db:sync';

    /**
     * @var State
     */
    protected $state;

    /**
     * @var SyncService
     */
    protected $sync;

    /**
     * @var LoggerIntegration
     */
    protected $logger;

    /**
     * Sync constructor.
     * @param State $state
     * @param SyncService $sync
     * @param LoggerIntegration $logger
     * @param string|null $name
     */
    public function __construct(
        State $state,
        SyncService $sync,
        LoggerIntegration $logger,
        string $name = null
    ) {
        $this->state = $state;
        $this->sync = $sync;
        $this->logger = $logger;
        parent::__construct($name);
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName(self::SYNC_COMMAND);
        $this->setDescription('Fortvision DB sync');
        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void
     * @throws LocalizedException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->state->setAreaCode(Area::AREA_ADMINHTML);

        $output->writeln('<info>Start sync DB process</info>');
        try {
            $this->sync->process();
            $output->writeln('<info>Sync DB has been finished</info>');
            return Cli::RETURN_SUCCESS;
        } catch (\Exception $e) {
            $output->writeln('<error>Sync DB has been failed</error>');
            $this->logger->critical($e->getMessage());
            return Cli::RETURN_FAILURE;
        }
    }
}
