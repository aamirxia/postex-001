<?php

namespace PostEx\Courier\Block\Adminhtml\Order;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Model\UrlInterface;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\ConfigInterface;
use PostEx\Courier\Model\App\Config;

/**
 * Class Logs
 * @package PostEx\Courier\Block\Adminhtml\Order
 */
class Logs extends \Magento\Sales\Block\Adminhtml\Order\View
{
    public const LOGS_URL = 'postex/order/logs';

    /**
     * @var Context
     */
    protected $_context;

    /**
     * @var MessageManagerInterface
     */
    protected $messageManager;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;
    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var UrlInterface
     */
    protected $backendUrl;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @param Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ConfigInterface $salesConfig
     * @param \Magento\Sales\Helper\Reorder $reorderHelper
     * @param OrderRepositoryInterface $orderRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param UrlInterface $backendUrl
     * @param Config $config
     * @param MessageManagerInterface $messageManager
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param array $data
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Registry $registry,
        ConfigInterface $salesConfig,
        \Magento\Sales\Helper\Reorder $reorderHelper,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        UrlInterface $backendUrl,
        Config $config,
        MessageManagerInterface $messageManager,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $salesConfig,
            $reorderHelper,
            $data
        );
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->backendUrl = $backendUrl;
        $this->config = $config;
        $this->messageManager = $messageManager;
        $this->timezone = $timezone;
    }

    /**
     * @return mixed
     */
    public function getPickUpAddress()
    {
        $url = $this->config->getEnvironmentMode();
        $apiToken = $this->config->getApiToken();

        if ($url == 'Stg') {
            $postExUrl = 'https://stg-api.postex.pk/services/integration/api/order/v1/get-merchant-address';
        } else {
            $postExUrl = 'https://api.postex.pk/services/integration/api/order/v1/get-merchant-address';
        }

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $postExUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => [
                "token: $apiToken",
                'Content-Type: application/json'
            ],
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        $response = json_decode($response, true);
        return $response;
    }

    /**
     * @return mixed
     */
    public function getUnBookedOrders()
    {
        if (isset($_POST['hxs_print_btn'])) {
            $orders = $_POST['order'];
            $trackings = [];
            foreach ($orders as $order) {
                if (isset($order['check'])) {
                    $trackings[] = $order['tracking'];
                }
            }
            $trackings = implode(',', $trackings);
            if ($trackings == "") {
                return 'Empty Orders';
            }
            /*echo "<script>window.open('https://stg-merchant.postex.pk//get-invoice?trackingNumbers $trackings', '_newtab');</script>";
            return;*/
            echo "<script>window.open('https://stg-merchant.postex.pk/get-invoice?trackingNumbers=$trackings?>', '_newtab');</script>";
            return ;
        }

        $url = $this->config->getEnvironmentMode();
        $apiToken = $this->config->getApiToken();

        if ($url == 'Stg') {
            $postExUrl = 'https://stg-api.postex.pk/services/integration/api/order/v1/get-all-order';
        } else {
            $postExUrl = 'https://api.postex.pk/services/integration/api/order/v1/get-all-order';
        }

        $orderStatusId = 1;
        $orderStartDate = '2021-01-01';
        $orderEndDate = '2023-01-01';

        if (isset($_POST['hxs_date_btn'])) {
            $orderStatusId = $_POST['hxs_status'];
            $startDate = str_replace('/', '-', $_POST['hxs_date_from']);
            $orderStartDate = $this->timezone->date(strtotime($startDate))->format("Y-m-d");
            $endDate = str_replace('/', '-', $_POST['hxs_date_to']);
            $orderEndDate = $this->timezone->date(strtotime($endDate))->format("Y-m-d");
        }

        // Get unbooked order

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
            CURLOPT_POSTFIELDS => '{"orderStatusId":' . $orderStatusId . ',"orderStartDate":"' . $orderStartDate . '","orderEndDate":"' . $orderEndDate . '"}',
            CURLOPT_HTTPHEADER => [
                "token: $apiToken",
                'Content-Type: application/json'
            ],
        ]);
        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response, true);
        if (!$response['dist']) {
            return null;
        }
        return $response;
    }

    /**
     * @return string
     */
    public function getGenerateLoadSheetUrl()
    {
        return $this->backendUrl->getUrl(self::LOGS_URL);
    }
}
