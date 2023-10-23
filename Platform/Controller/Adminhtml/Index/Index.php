<?php
declare(strict_types=1);

namespace Fortvision\Platform\Controller\Adminhtml\Index;

class Index extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context, $coreRegistry);
    }

    public function execute()
    {
        echo 'My admin controller';
    }

    protected function _isAllowed()
    {
        return true;
    }
}
