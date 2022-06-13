<?php

namespace PostEx\Courier\Controller\Adminhtml\Order;

use Magento\Framework\App\ResponseInterface;

/**
 * Class Redirection
 * @package PostEx\Courier\Controller\Adminhtml\Order
 */
class Redirection extends \Magento\Backend\App\Action
{
    /**
     * @return ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('sales/*/');
        return $resultRedirect;
    }
}
