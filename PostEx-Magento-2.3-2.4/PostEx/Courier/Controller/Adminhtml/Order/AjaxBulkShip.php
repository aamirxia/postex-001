<?php

namespace PostEx\Courier\Controller\Adminhtml\Order;

use Magento\Backend\App\Action\Context;
use Magento\Sales\Api\OrderRepositoryInterface;
use PostEx\Courier\Controller\Adminhtml\Order\Ship as OrderShip;
use PostEx\Courier\Model\Shipment\GenerateShipment;

class AjaxBulkShip extends \Magento\Backend\App\Action implements \Magento\Framework\App\Action\HttpPostActionInterface
{

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var GenerateShipment
     */
    private $generateShipment;

    /**
     * @var OrderShip
     */
    private $orderShip;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @param Context $context
     * @param OrderRepositoryInterface $orderRepository
     * @param GenerateShipment $generateShipment
     * @param Ship $orderShip
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        OrderRepositoryInterface $orderRepository,
        GenerateShipment $generateShipment,
        OrderShip $orderShip,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        parent::__construct(
            $context
        );
        $this->orderRepository = $orderRepository;
        $this->generateShipment = $generateShipment;
        $this->orderShip = $orderShip;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    public function execute()
    {
        $bookedOrders = 0;
        $unBookedOrders = 0;
        $orderNumbers = [];
        $orderTrackingNumbers = [];

        $resultJson = $this->resultJsonFactory->create();
        $orderIds = unserialize($this->getRequest()->getParam('orderIds'));

        foreach ($orderIds as $orderId) {
            $order = $this->orderRepository->get($orderId);
            if ($order->canShip()) {
                try {
                    $orderTrackingNumber = $this->orderShip->createOrderAPI($order);
                    $this->generateShipment->generateShipment($order, $orderTrackingNumber);
                    $orderTrackingNumbers[] = $orderTrackingNumber;
                    $orderNumbers[] = $order->getIncrementId();
                    $this->messageManager->addSuccessMessage(
                        __('Shipment against order(%1) has been created', $order->getIncrementId())
                    );
                    $bookedOrders++;
                } catch (\Exception $e) {
                    throw new \Exception($e->getMessage());
                }
            } else {
                $unBookedOrders++;
                $this->messageManager->addErrorMessage(
                    __('Shipment against order(%1) has already been created', $order->getIncrementId())
                );
            }
        }

        return $resultJson->setData(
            [
                'booked' => $bookedOrders,
                'unbooked' => $unBookedOrders,
                'Tracking_Numbers' => $orderTrackingNumbers,
                'Order_Numbers' => $orderNumbers
            ]
        );
    }
}
