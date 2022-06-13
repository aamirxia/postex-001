<?php

namespace PostEx\Courier\Block\Adminhtml\Order;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Model\UrlInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\ConfigInterface;

/**
 * Class BulkShipView
 * @package PostEx\Courier\Block\Adminhtml\Order
 */
class BulkShipView extends \Magento\Sales\Block\Adminhtml\Order\View
{
    public const ORDER_BOOKING = 'postex/order/ajaxBulkShip';
    public const REDIRECT_URL = 'postex/order/redirection';
    public const SALES_ORDER_GRID_URL = 'sales/order/index';

    /**
     * @var Context
     */
    protected $_context;

    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $backendUrl;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * BulkShipView constructor.
     * @param Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ConfigInterface $salesConfig
     * @param \Magento\Sales\Helper\Reorder $reorderHelper
     * @param OrderRepositoryInterface $orderRepository
     * @param UrlInterface $backendUrl
     * @param array $data
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Registry $registry,
        ConfigInterface $salesConfig,
        \Magento\Sales\Helper\Reorder $reorderHelper,
        OrderRepositoryInterface $orderRepository,
        UrlInterface $backendUrl,
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
        $this->backendUrl = $backendUrl;
    }

    /**
     * @return array
     */
    function getOrderCollections(): array
    {
        $orderIds = $this->getRequest()->getParam('selected');
        $orders = [];
        foreach ($orderIds as $orderId) {
            $order = $this->orderRepository->get($orderId);
            $orders [] = $order;
        }
        return $orders;
    }

    /**
     * @return string
     */
    public function getPostExUrl()
    {
        return $this->backendUrl->getUrl(self::ORDER_BOOKING);
    }

    /**
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->backendUrl->getUrl(self::REDIRECT_URL);
    }

    /**
     * @return string
     */
    public function getSalesOrderGridUrl()
    {
        return $this->backendUrl->getUrl(self::SALES_ORDER_GRID_URL);
    }
}
