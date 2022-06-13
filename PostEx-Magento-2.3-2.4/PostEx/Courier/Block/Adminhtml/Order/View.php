<?php

namespace PostEx\Courier\Block\Adminhtml\Order;

/**
 * Class View
 * @package PostEx\Courier\Block\Adminhtml\Order
 */
class View extends \Magento\Sales\Block\Adminhtml\Order\View
{
    public const ORDER_BOOKING = 'postex/order/ship';

    /**
     * @var \Magento\Backend\Block\Widget\Context
     */
    protected $_context;

    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $backendUrl;

    /**
     * View constructor.
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Sales\Model\Config $salesConfig
     * @param \Magento\Sales\Helper\Reorder $reorderHelper
     * @param \Magento\Backend\Model\UrlInterface $backendUrl
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Model\Config $salesConfig,
        \Magento\Sales\Helper\Reorder $reorderHelper,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $salesConfig,
            $reorderHelper,
            $data
        );
        $this->_context = $context;
        $this->backendUrl = $backendUrl;
    }

    /**
     * @return string
     */
    public function getPostExUrl(): string
    {
        return $this->backendUrl->getUrl(self::ORDER_BOOKING);
    }

    /**
     * @return int|null
     */
    public function getCurrentOrderId(): ?int
    {
        return $this->getOrderId();
    }
}
