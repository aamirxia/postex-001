<?php

namespace PostEx\Courier\Controller\Adminhtml\Order;

use Magento\Backend\App\Action\Context;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use PostEx\Courier\Model\Shipment\GenerateShipment;
use PostEx\Courier\Model\App\Config;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Ship
 * @package PostEx\Courier\Controller\Adminhtml\Order
 */
class Ship extends \Magento\Backend\App\Action
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
     * @var Config
     */
    protected $config;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Ship constructor.
     * @param Context $context
     * @param OrderRepositoryInterface $orderRepository
     * @param GenerateShipment $generateShipment
     * @param Config $config
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        OrderRepositoryInterface $orderRepository,
        GenerateShipment $generateShipment,
        Config $config,
        PageFactory $resultPageFactory
    ) {
        parent::__construct(
            $context
        );
        $this->orderRepository = $orderRepository;
        $this->generateShipment = $generateShipment;
        $this->config = $config;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Exception
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__(' heading '));

        $orderId = $this->getRequest()->getParam('orderId');
        $order = $this->orderRepository->get($orderId);
        try {
            $trackingNumber = $this->createOrderAPI($order);
            $this->generateShipment->generateShipment($order, $trackingNumber);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        $this->messageManager->addSuccessMessage(
            __('Order(%1) shipment has been created', $order->getIncrementId())
        );

        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($trackingNumber);
        return $resultJson;
    }

    /**
     * @param $order
     * @return mixed
     */
    public function createOrderAPI($order)
    {
        $postExToken = $this->config->getApiToken();
        $url = $this->config->getEnvironmentMode();

        if ($url == 'Stg') {
            $AppUrl = 'https://stg-api.postex.pk/services/integration/api/order/v1/create-order';
        } else {
            $AppUrl = 'https://api.postex.pk/services/integration/api/order/v1/create-order';
        }

        $postExUrl = preg_replace('/(\/+)/', '/', $AppUrl);

        $data = [
            "cityName" => $order->getShippingAddress()->getCity(),
            "customerName" => $order->getCustomerName(),
            "customerPhone" => $order->getShippingAddress()->getTelephone(),
            "deliveryAddress" => $order->getShippingAddress()->getCity(),
            "invoiceDivision" => '1',
            "invoicePayment" => $order->getBaseGrandTotal(),
            "items" => count($order->getItems()),
            "orderDetail" => 'Test Magento Order1',
            "orderRefNumber" => '#'.$order->getIncrementId(),
            "transactionNotes" => 'SQA',
        ];

        $dataString = json_encode($data);

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $postExUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $dataString,
            CURLOPT_HTTPHEADER => [
                'token: ' . $postExToken,
                'Content-Type: application/json'
            ],
        ]);

        $result = json_decode(curl_exec($curl));
        curl_close($curl);

        if ($result->statusCode != 200) {
            $this->messageManager->addErrorMessage($result->error);
        }

        return $result->dist->trackingNumber;
    }
}
