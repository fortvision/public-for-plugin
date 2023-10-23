<?php

namespace Fortvision\Sync\Service;

use Fortvision\Sync\Helper\Data;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Shell;
use Magento\Framework\Shell\CommandRenderer;

/**
 * Class Sync
 * @package Fortvision\Sync\Service
 */
class Sync
{
    const FORTVISION_FILENAME = 'fortvision_tables_';

    /**
     * @var array
     */
    protected $dumpOptions = [
        '--no-tablespaces',
        '--single-transaction'
    ];

    protected $_shell;
    /**
     * @var string
     */
    protected $fileName;

    /**
     * @var State
     */
    protected $state;

    /**
     * @var Json
     */
    protected $json;

    /**
     * @var DateTime
     */
    protected $date;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var File
     */
    protected $file;

    /**
     * @var DirectoryList
     */
    protected $dir;

    /**
     * Sync constructor.
     * @param DateTime $date
     * @param Data $helper
     * @param File $file
     * @param DirectoryList $dir
     */
    public function __construct(
        DateTime $date,
        Data $helper,
        File $file,
        DirectoryList $dir
    ) {
        $this->date = $date;
        $this->helper = $helper;
        $this->file = $file;
        $this->dir = $dir;
        $this->_shell = new Shell(new CommandRenderer());
    }

    /**
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function process()
    {
        try {
            if (!$this->helper->isModuleEnabled()) {
                throw new LocalizedException(__('Fortvision Sync is disabled'));
            }
            $this->helper->checkRemoteDatabase();
            $this->getFileName();
            $tables = $this->helper->getTablesArrea();
            $connectionString = $this->helper->getMysqlConnectionString(false, $this->dumpOptions);
            $tableList = implode(' ', $tables);
            $this->_shell->execute('mysqldump ' . $connectionString . ' ' . $tableList . ' > ' . $this->fileName);

            if ($this->file->isExists($this->fileName)) {
                $connectionString = $this->helper->getMysqlConnectionString(true);
                $this->_shell->execute('mysql ' . $connectionString . ' < ' . $this->fileName);
                $this->file->deleteFile($this->fileName);
            }
        } catch (\Exception $e) {
            if ($this->file->isExists($this->fileName)) {
                $this->file->deleteFile($this->fileName);
            }
        }
    }

    /**
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    protected function getFileName()
    {
        $varDir = $this->dir->getPath('var');
        $this->fileName = $varDir . '/' . self::FORTVISION_FILENAME . $this->date->date('Ymd_His') . '.sql';
    }
}
