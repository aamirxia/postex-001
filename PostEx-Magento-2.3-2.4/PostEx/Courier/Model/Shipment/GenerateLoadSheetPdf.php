<?php

namespace PostEx\Courier\Model\Shipment;

use PostEx\Courier\Model\App\Config;

/**
 * Class GenerateLoadSheetPdf
 * @package PostEx\Courier\Model\Shipment
 */
class GenerateLoadSheetPdf
{

    /**
     * @var Config
     */
    protected $config;

    /**
     * GenerateLoadSheetPdf constructor.
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * @param $postValues
     */
    public function generateLoadSheetApi($postValues)
    {
        $trackings = [];
        $postExToken = $this->config->getApiToken();
        $url = $this->config->getEnvironmentMode();

        if ($url == 'Stg') {
            $AppUrl = 'https://stg-api.postex.pk/services/integration/api/order/v1/generate-load-sheet';
        } else {
            $AppUrl = 'https://api.postex.pk/services/integration/api/order/v1/generate-load-sheet';
        }

        $postExUrl = preg_replace('/(\/+)/', '/', $AppUrl);

        if (isset($postValues['hxs_loadsheet_btn'])) {
            $orders = $postValues['order'];
            foreach ($orders as $order) {
                if (isset($order['check'])) {
                    $trackings[] = $order['tracking'];
                }
            }

            $pickup_data = $postValues['hxs_pickup'];
            $pickup_data = explode('%', $pickup_data);
            $data = [
                "cityName" => $pickup_data[1],
                "contactNumber" => $pickup_data[2],
                "pickupAddress" => $pickup_data[0],
                "trackingNumbers" => $trackings,
            ];
            $loadsheet_data = json_encode($data);

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
                CURLOPT_POSTFIELDS => $loadsheet_data,
                CURLOPT_HTTPHEADER => [
                    'token: ' . $postExToken,
                    'Content-Type: application/json'
                ],
            ]);
            $response = curl_exec($curl);
            header('Content-type: application/pdf');
            header('Content-Disposition: attachment; filename="postex-loadsheet.pdf"');
            echo $response;
            curl_close($curl);

        }
    }
}
