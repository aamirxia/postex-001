<?php

namespace PostEx\Courier\Controller\Adminhtml\Order;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use PostEx\Courier\Model\Shipment\GenerateLoadSheetPdf;

/**
 * Class GenerateLoadSheet
 * @package PostEx\Courier\Controller\Adminhtml\Order
 */
class GenerateLoadSheet extends \Magento\Backend\App\Action
{

    /**
     * @var GenerateLoadSheetPdf
     */
    private $generateLoadSheetPdf;

    /**
     * GenerateLoadSheet constructor.
     * @param Context $context
     * @param GenerateLoadSheetPdf $generateLoadSheetPdf
     */
    public function __construct(
        Context $context,
        GenerateLoadSheetPdf $generateLoadSheetPdf
    ) {
        parent::__construct(
            $context
        );
        $this->generateLoadSheetPdf = $generateLoadSheetPdf;
    }

    /**
     * @return ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $postValues = $this->getRequest()->getPost();
        $this->generateLoadSheetPdf->generateLoadSheetApi($postValues);
    }
}
