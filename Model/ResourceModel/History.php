<?php

namespace Fortvision\Platform\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class History
 * @package Fortvision\Platform\Model\ResourceModel
 */
class History extends AbstractDb
{
    protected function _construct()
    {
   // echo("T!");
        $this->_init('fortvision_history', 'history_id');
    }
}
