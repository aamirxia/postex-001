<?php

namespace PostEx\Courier\Model\App;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;

/**
 * Class Config
 * @package PostEx\Courier\Model\App
 */
class Config
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var EncryptorInterface
     */
    protected $encryptor;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param EncryptorInterface $encryptor
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        EncryptorInterface $encryptor
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->encryptor = $encryptor;
    }

    /**
     * @return string
     */
    public function getApiToken()
    {
        $apiToken =  $this->encryptor->decrypt($this->scopeConfig->getValue("carriers/postex/token",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES));
        return $apiToken;
    }

    /**
     * @return mixed
     */
    public function getEnvironmentMode()
    {
        $apiUrl = $this->scopeConfig->getValue("carriers/postex/environment_mode",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES);
        return $apiUrl;
    }

    /**
     * @return mixed
     */
    public function getTrackingUrl()
    {
        return $this->scopeConfig->getValue("carriers/postex/tracking_url",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES);
    }

    /**
     * @return mixed
     */
    public function getShipmentType()
    {
        return $this->scopeConfig->getValue("carriers/postex/shipment_type",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES);
    }

    /**
     * @return mixed
     */
    public function getHandlingType()
    {
        return $this->scopeConfig->getValue("carriers/postex/handling_type",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES);
    }

    /**
     * @return mixed
     */
    public function getConsigneeDetails()
    {
        return $this->scopeConfig->getValue("carriers/postex/consignee_details",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES);
    }

    /**
     * @return mixed
     */
    public function getRemarks()
    {
        return $this->scopeConfig->getValue("carriers/postex/remarks",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES);
    }

    /**
     * @return mixed
     */
    public function getDefaultProducts()
    {
        return $this->scopeConfig->getValue("carriers/postex/default_products",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES);
    }

    /**
     * @return mixed
     */
    public function getPrintItemNameOnLabel()
    {
        return $this->scopeConfig->getValue("carriers/postex/print_item_name_on_label",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES);
    }

    /**
     * @return mixed
     */
    public function getPrintSkuOnLabel()
    {
        return $this->scopeConfig->getValue("carriers/postex/print_sku_on_label",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES);
    }

    /**
     * @return mixed
     */
    public function getAutoCalculateWeight()
    {
        return $this->scopeConfig->getValue("carriers/postex/auto_calculate_weight",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES);
    }

    /**
     * @return mixed
     */
    public function getAutoCalculatePieces()
    {
        return $this->scopeConfig->getValue("carriers/postex/auto_calculate_pieces",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES);
    }

    /**
     * @return mixed
     */
    public function getCalculateNonCodAsZero()
    {
        return $this->scopeConfig->getValue("carriers/postex/calculate_non_cod_as_zero",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES);
    }

    /**
     * @return mixed
     */
    public function getPostExOrderStatus()
    {
        return $this->scopeConfig->getValue("carriers/postex/add_postex_order_status",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES);
    }

    /**
     * @return mixed
     */
    public function getSandboxMode()
    {
        return $this->scopeConfig->getValue("carriers/postex/sandbox_mode",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES);
    }
}
