<?php

namespace Fortvision\Platform\Controller\Adminhtml\History;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Index
 * @package Fortvision\Platform\Controller\Adminhtml\History
 */
class Index extends Action
{
    const ADMIN_RESOURCE = 'Fortvision_Platform::history';
    const ACTIVE_MENU = 'Fortvision_Platform::history';

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
      //  echo('AAAAAA');
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);

    }

    /**
     * @return ResponseInterface|ResultInterface|Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu(self::ACTIVE_MENU)
            ->addBreadcrumb(__('System'), __('System'))
            ->addBreadcrumb(__('Fortvision Integration History'), __('Integration History'));
        $resultPage->getConfig()->getTitle()->prepend(__('Integration History'));

        return $resultPage;
    }
}
