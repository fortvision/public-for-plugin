<?php

namespace Fortvision\Platform\Controller\Adminhtml\History;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Log
 * @package Fortvision\Platform\Controller\Adminhtml\History
 */
class Log extends Action
{
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
            ->addBreadcrumb(__('Fortvision Integration History'), __('Integration History'))
            ->addBreadcrumb(__('Integration History Log'), __('Integration History Log'));
        $resultPage->getConfig()->getTitle()->prepend('History Log');

        return $resultPage;
    }
}
