<?
namespace Fortvision\Platform\Setup;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
    private $scopeConfig;
    private $installer;

    public function install(\Magento\Framework\Setup\SchemaSetupInterface   $setup,
                            ScopeConfigInterface                            $scopeConfig,

                            \Magento\Framework\Setup\ModuleContextInterface $context)
    {цукцукцукуц
        $installer = $setup;
        $this->scopeConfig = $scopeConfig;
        echo('INSTALL FORTVISION');


       // $installer->startSetup();

    }
}

?>
