<?php

namespace Fortvision\Platform\Controller\Adminhtml\History;

use Fortvision\Platform\Model\HistoryProcess;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Process
 * @package Fortvision\Platform\Controller\Adminhtml\History
 */
class Process extends Action
{
    const ACTIVE_MENU = 'Fortvision_Platform::history';

    /**
     * @var HistoryProcess
     */
    protected $historyProcess;

    /**
     * Process constructor.
     * @param Context $context
     * @param HistoryProcess $historyProcess
     */
    public function __construct(
        Context $context,
        HistoryProcess $historyProcess
    ) {
        $this->historyProcess = $historyProcess;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        $historyId = $this->getRequest()->getParam('history_id');
        $this->historyProcess->processById($historyId);
        $resultRedirect =  $this->resultRedirectFactory->create();
        $resultRedirect->setPath('fortvision/history/log', ['history_id' => $historyId]);
        return $resultRedirect;
    }
}
