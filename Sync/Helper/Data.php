<?php

namespace Fortvision\Sync\Helper;

use Fortvision\Sync\Provider\GeneralSettings;
use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Config\ConfigOptionsListConstants;
use Magento\Framework\Shell;
use Magento\Framework\Shell\CommandRenderer;

/**
 * Class Data
 * @package Fortvision\Sync\Helper
 */
class Data extends AbstractHelper
{
    /**
     * @var array
     */
    protected $tables = [
        // Stores
        'store',
        'store_group',
        'store_website',

        // Attributes
        'eav_attribute',
        'eav_attribute_group',
        'eav_attribute_label',
        'eav_attribute_option',
        'eav_attribute_option_swatch',
        'eav_attribute_option_value',
        'eav_attribute_set',
        'eav_entity',
        'eav_entity_attribute',
        'eav_entity_datetime',
        'eav_entity_decimal',
        'eav_entity_int',
        'eav_entity_store',
        'eav_entity_text',
        'eav_entity_type',
        'eav_entity_varchar',
        'customer_eav_attribute',
        'customer_eav_attribute_website',

        // Categories
        'catalog_category_entity',
        'catalog_category_entity_datetime',
        'catalog_category_entity_decimal',
        'catalog_category_entity_int',
        'catalog_category_entity_text',
        'catalog_category_entity_varchar',
        'catalog_category_product',

        // Products
        'catalog_product_entity',
        'catalog_product_entity_datetime',
        'catalog_product_entity_decimal',
        'catalog_product_entity_gallery',
        'catalog_product_entity_int',
        'catalog_product_entity_media_gallery',
        'catalog_product_entity_media_gallery_value',
        'catalog_product_entity_text',
        'catalog_product_entity_tier_price',
        'catalog_product_entity_varchar',
        'catalog_product_bundle_option',
        'catalog_product_bundle_option_value',
        'catalog_product_bundle_selection',
        'catalog_product_bundle_selection_price',
        'catalog_product_link',
        'catalog_product_link_attribute',
        'catalog_product_link_attribute_decimal',
        'catalog_product_link_attribute_int',
        'catalog_product_link_attribute_varchar',
        'catalog_product_link_type',
        'catalog_product_option',
        'catalog_product_option_price',
        'catalog_product_option_title',
        'catalog_product_option_type_price',
        'catalog_product_option_type_title',
        'catalog_product_option_type_value',
        'catalog_product_relation',
        'catalog_product_super_attribute',
        'catalog_product_super_attribute_label',
        'catalog_product_super_link',
        'catalog_product_website',

        // Inventory
        'cataloginventory_stock',
        'cataloginventory_stock_item',
        'cataloginventory_stock_status',
        'inventory_stock',
        'inventory_stock_sales_channel',
        'inventory_source',
        'inventory_source_item',
        'inventory_source_stock_link',
        'inventory_reservation',
        'inventory_shipment_source',

        // Customers
        'customer_entity',
        'customer_entity_datetime',
        'customer_entity_decimal',
        'customer_entity_int',
        'customer_entity_text',
        'customer_entity_varchar',
        'customer_group',

        // Customer addresses
        'customer_address_entity',
        'customer_address_entity_datetime',
        'customer_address_entity_decimal',
        'customer_address_entity_int',
        'customer_address_entity_text',
        'customer_address_entity_varchar',

        // Orders
        'sales_order',
        'sales_order_address',
        'sales_order_item',
        'sales_order_payment',
        'sales_order_status',
        'sales_order_status_label',
        'sales_order_status_state',
        'sales_order_tax',
        'sales_order_tax_item',

        // Invoices
        'sales_invoice',
        'sales_invoice_comment',
        'sales_invoice_item',

        // Shipments
        'sales_shipment',
        'sales_shipment_comment',
        'sales_shipment_item',

        // Tax
        'tax_class',
        'tax_calculation',
        'tax_calculation_rate',
        'tax_calculation_rate_title',
        'tax_calculation_rule',

        // Catalog Rules
        'catalogrule',
        'catalogrule_product',
        'catalogrule_product_price',
        'catalogrule_website',
        'catalogrule_group_website',
        'catalogrule_customer_group',

        // Sales Rules
        'salesrule',
        'salesrule_website',
        'salesrule_coupon',
        'salesrule_customer',
        'salesrule_customer_group',
        'salesrule_label',
        'salesrule_product_attribute',
    ];
    protected $_shell;
    /**
     * @var array
     */
    protected $dbSettings = [];

    /**
     * @var bool
     */
    protected $isSocketConnect;

    /**
     * @var DeploymentConfig
     */
    protected $deploymentConfig;

    /**
     * @var string
     */
    private $connectionName = 'default';

    /**
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * @var GeneralSettings
     */
    protected $generalSettings;

    /**
     * Data constructor.
     * @param Context $context
     * @param ResourceConnection $resource
     * @param DeploymentConfig $deploymentConfig
     * @param GeneralSettings $generalSettings
     */
    public function __construct(
        Context $context,
        ResourceConnection $resource,
        DeploymentConfig $deploymentConfig,
        GeneralSettings $generalSettings
    ) {
        $this->deploymentConfig = $deploymentConfig;
        $this->resource = $resource;
        $this->generalSettings = $generalSettings;
        $this->_shell = new Shell(new CommandRenderer());
        parent::__construct($context);
    }

    /**
     * @return bool
     */
    public function isModuleEnabled()
    {
        return $this->generalSettings->isModuleEnabled();
    }

    /**
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\RuntimeException
     */
    public function checkRemoteDatabase()
    {
        $this->getDbSettings(true);

        if ($this->isSocketConnect) {
            $string = '--socket=' . escapeshellarg($this->dbSettings['unix_socket']);
        } else {
            $string = '-h' . escapeshellarg($this->dbSettings['host']);
        }

        $string .= ' -u' . escapeshellarg($this->dbSettings['username']) . ' ';
        $string .= !empty($this->dbSettings['port']) ? '-P' . escapeshellarg($this->dbSettings['port']) . ' ' : '';
        $string .= strlen($this->dbSettings['password']) ? '--password=' . escapeshellarg($this->dbSettings['password']) . ' ' : '';
        $this->_shell->execute('mysql ' . $string . ' -e "CREATE DATABASE IF NOT EXISTS ' . $this->dbSettings['dbname'] . '"');
    }

    /**
     * @return array
     */
    public function getTablesArrea()
    {
        $result = [];
        $connection = $this->resource->getConnection();
        foreach ($this->tables as $table) {
            $result[] = $connection->getTableName($table);
        }

        return $result;
    }

    /**
     * @param bool $fortvisionDb
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\RuntimeException
     */
    public function getDbSettings($fortvisionDb = false)
    {
        if ($fortvisionDb) {
            $connectionConfig['host'] = $this->generalSettings->getHost();
            $connectionConfig['dbname'] = $this->generalSettings->getDbName();
            $connectionConfig['username'] = $this->generalSettings->getDbUsername();
            $connectionConfig['password'] = $this->generalSettings->getDbPassword();
        } else {
            $resource = $this->deploymentConfig->getConfigData(ConfigOptionsListConstants::KEY_RESOURCE) ?: [];
            foreach ($resource as $resourceName => $resourceData) {
                if (!isset($resourceData['connection'])) {
                    throw new \InvalidArgumentException('Invalid initial resource configuration');
                }
                $connectionName = $resourceData['connection'];
            }

            $connectionName = $connectionName ?? $this->connectionName;
            $connectionConfig = $this->deploymentConfig->get(
                ConfigOptionsListConstants::CONFIG_PATH_DB_CONNECTIONS . '/' . $connectionName
            );

            if (empty($connectionConfig)) {
                throw new RuntimeException('DB settings was not found in app/etc/env.php file');
            }
        }

        $this->dbSettings = (array) $connectionConfig;

        if (strpos($this->dbSettings['host'], '/') !== false) {
            $this->dbSettings['unix_socket'] = $this->dbSettings['host'];
            unset($this->dbSettings['host']);
        } elseif (strpos($this->dbSettings['host'], ':') !== false) {
            list($this->dbSettings['host'], $this->dbSettings['port']) = explode(':', $this->dbSettings['host']);
        }

        if (isset($this->dbSettings['unix_socket'])) {
            $this->isSocketConnect = true;
        }
    }

    /**
     * @param bool $fortvisionDb
     * @param array $options
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\RuntimeException
     */
    public function getMysqlConnectionString($fortvisionDb = false, $options = [])
    {
        $this->getDbSettings($fortvisionDb);

        if ($this->isSocketConnect) {
            $string = '--socket=' . escapeshellarg($this->dbSettings['unix_socket']);
        } else {
            $string = '-h' . escapeshellarg($this->dbSettings['host']);
        }

        $string .= ' -u' . escapeshellarg($this->dbSettings['username']) . ' ';
        $string .= !empty($this->dbSettings['port']) ? '-P' . escapeshellarg($this->dbSettings['port']) . ' ' : '';
        $string .= strlen($this->dbSettings['password']) ? '--password=' . escapeshellarg($this->dbSettings['password']) . ' ' : '';

        foreach ($options as $option) {
            $string .= $option . ' ';
        }
        $string .= escapeshellarg($this->dbSettings['dbname']);

        return $string;
    }
}
