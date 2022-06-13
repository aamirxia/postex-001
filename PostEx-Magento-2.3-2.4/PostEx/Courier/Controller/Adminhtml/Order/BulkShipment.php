<?php

namespace PostEx\Courier\Controller\Adminhtml\Order;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Class BulkShipment
 * @package PostEx\Courier\Controller\Adminhtml\Order
 */
class BulkShipment extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'PostEx_Courier::postex_courier_config';

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * BulkShipment constructor.
     * @param Context $context
     * @param OrderRepositoryInterface $orderRepository
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        OrderRepositoryInterface $orderRepository,
        PageFactory $resultPageFactory
    ) {
        parent::__construct(
            $context
        );
        $this->orderRepository = $orderRepository;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * @return ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('PostEx Bulk Booking'));
        return $resultPage;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(self::ADMIN_RESOURCE);
    }
}
