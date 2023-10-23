<?php

namespace Fortvision\Platform\Block;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\FlagManager;
use Magento\Store\Model\ResourceModel\Website\CollectionFactory as WebsiteCollectionFactory;

class Navigation extends \Magento\Config\Block\System\Config\Form\Field\Notification
{

    protected $_activation;
    private $scopeConfig;
    protected $websiteCollectionFactory;
    private FlagManager $flagManager;



    protected function _getElementHtml(AbstractElement $element) {
        return '<div><suprematik-page></suprematik-page></div>';
    }


}
