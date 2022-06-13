<?php

namespace PostEx\Courier\Block\Adminhtml\Order\Tracking;

use PostEx\Courier\Model\App\Config;

class View extends \Magento\Shipping\Block\Adminhtml\Order\Tracking\View
{
    /**
     * @var \Magento\Shipping\Model\CarrierFactory
     */
    protected $_carrierFactory;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Shipping\Model\Config $shippingConfig
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Shipping\Model\CarrierFactory $carrierFactory
     * @param Config $config
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Shipping\Model\Config $shippingConfig,
        \Magento\Framework\Registry $registry,
        \Magento\Shipping\Model\CarrierFactory $carrierFactory,
        Config $config,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $shippingConfig,
            $registry,
            $carrierFactory,
            $data
        );
        $this->_carrierFactory = $carrierFactory;
        $this->config = $config;
    }

    /**
     * @param $code
     * @return bool|\Magento\Framework\Phrase|string
     */
    public function getCarrierTitle($code)
    {
        $carrierTitle = "PostEx";
        if ($code == "PostEx") {
            return $carrierTitle;
        }

        $carrier = $this->_carrierFactory->create($code);
        return $carrier ? $carrier->getConfigData('title') : __('Custom Value');
    }

    /**
     * @return string
     */
    public function getTrackingUrl(): string
    {
        $url = $this->config->getEnvironmentMode();
        if ($url == 'Stg') {
            $postExTrackingUrl = 'https://stg-merchant.postex.pk';
        } else {
            $postExTrackingUrl = 'https://merchant.postex.pk';
        }

        return $postExTrackingUrl;
    }
}
