<?php

namespace PostEx\Courier\Model\Shipment;

use Magento\Framework\Message\ManagerInterface;
use PostEx\Courier\Model\App\Config;

/**
 * Class CancelOrder
 * @package PostEx\Courier\Model\Shipment
 */
class CancelOrder
{
    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * @var Config
     */
    protected $config;

    /**
     * CancelOrder constructor.
     * @param ManagerInterface $messageManager
     * @param Config $config
     */
    public function __construct(
        ManagerInterface $messageManager,
        Config $config
    ) {
        $this->messageManager = $messageManager;
        $this->config = $config;
    }

    /**
     * @param $trackingNumber
     * @return bool|mixed
     */
    public function cancelOrder($trackingNumber)
    {
        try {
            return $this->cancelOrderAPI($trackingNumber);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        return true;
    }

    /**
     * @param $trackingNumber
     * @return mixed
     */
    public function cancelOrderAPI($trackingNumber)
    {
        $postExToken = $this->config->getApiToken();
        $url = $this->config->getEnvironmentMode();

        if ($url == 'Stg') {
            $AppUrl = 'https://stg-api.postex.pk/services/integration/api/order/v1/cancel-order';
        } else {
            $AppUrl = 'https://api.postex.pk/services/integration/api/order/v1/cancel-order';
        }

        $postExUrl = preg_replace('/(\/+)/', '/', $AppUrl);

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $postExUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_POSTFIELDS => '{"trackingNumber": "' . $trackingNumber . '"}',
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

        return $result;
    }
}
