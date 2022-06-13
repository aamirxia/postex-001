<?php

namespace PostEx\Courier\Model\Shipment;

use Magento\Framework\DB\TransactionFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Convert\OrderFactory as ConvertOrderFactory;
use Magento\Sales\Model\Order\Shipment\Sender\EmailSender as ShipmentSender;
use Magento\Sales\Model\Order\Shipment\TrackFactory;

/**
 * Class GenerateShipment
 * @package PostEx\Courier\Model\Shipment
 */
class GenerateShipment
{
    protected const CARRIER_CODE = 'PostEx';
    protected const CARRIER_TITLE = 'PostEx';

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepositoryInterface;

    /**
     * @var ConvertOrderFactory
     */
    private $convertOrderFactory;

    /**
     * @var TransactionFactory
     */
    private $transactionFactory;

    /**
     * @var ShipmentSender
     */
    private $shipmentSender;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * @var TrackFactory
     */
    private $trackFactory;

    /**
     * GenerateShipment constructor.
     * @param OrderRepositoryInterface $orderRepositoryInterface
     * @param ConvertOrderFactory $convertOrderFactory
     * @param TransactionFactory $transactionFactory
     * @param ShipmentSender $shipmentSender
     * @param ManagerInterface $messageManager
     * @param TrackFactory $trackFactory
     */
    public function __construct(
        OrderRepositoryInterface $orderRepositoryInterface,
        ConvertOrderFactory $convertOrderFactory,
        TransactionFactory $transactionFactory,
        ShipmentSender $shipmentSender,
        ManagerInterface $messageManager,
        TrackFactory $trackFactory
    ) {
        $this->orderRepositoryInterface = $orderRepositoryInterface;
        $this->convertOrderFactory = $convertOrderFactory->create();
        $this->transactionFactory = $transactionFactory;
        $this->shipmentSender = $shipmentSender;
        $this->messageManager = $messageManager;
        $this->trackFactory = $trackFactory;
    }

    /**
     * @param $order
     * @param $trackingNumber
     * @return bool
     */
    public function generateShipment($order, $trackingNumber): bool
    {
        try {
            if (!$order->getId()) {
                throw new \Magento\Framework\Exception\LocalizedException(__('The order no longer exists.'));
            }
            /* check shipment exist for order or not */
            if ($order->canShip()) {
                // Initialize the order shipment object
                $shipment = $this->convertOrderFactory->toShipment($order);
                foreach ($order->getAllItems() as $orderItem) {
                    // Check if order item has qty to ship or is order is virtual
                    if (!$orderItem->getQtyToShip() || $orderItem->getIsVirtual()) {
                        continue;
                    }
                    $qtyShipped = $orderItem->getQtyToShip();
                    // Create shipment item with qty
                    $shipmentItem = $this->convertOrderFactory->itemToShipmentItem($orderItem)->setQty($qtyShipped);
                    // Add shipment item to shipment
                    $shipment->addItem($shipmentItem);
                }
                $track = $this->trackFactory->create();
                $track->setCarrierCode(self::CARRIER_CODE);
                $track->setCarrierTitle(self::CARRIER_TITLE);
                $track->setTitle(self::CARRIER_TITLE);
                $track->setTrackNumber($trackingNumber);
                $shipment->addTrack($track);
                // Register shipment
                $shipment->register();
                $shipment->getOrder()->setIsInProcess(true);
                try {
                    $transaction = $this->transactionFactory->create()->addObject($shipment)
                        ->addObject($shipment->getOrder())
                        ->save();
                    $shipmentId = $shipment->getIncrementId();
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage(__('We can\'t generate shipment.'));
                }
                if ($shipment) {
                    try {
                        $this->shipmentSender->send($order, $shipment);
                    } catch (\Exception $e) {
                        $this->messageManager->addErrorMessage(__('We can\'t send the shipment right now.'));
                    }
                }
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        return true;
    }
}
