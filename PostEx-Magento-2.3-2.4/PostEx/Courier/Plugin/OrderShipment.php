<?php

namespace PostEx\Courier\Plugin;

/**
 * Class OrderShipment
 * @package PostEx\Courier\Plugin
 */
class OrderShipment
{
    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $order;

    /**
     * @param \Magento\Sales\Model\Order $order
     */
    public function __construct(
        \Magento\Sales\Model\Order $order
    ) {
        $this->order = $order;
    }

    /**
     * @param \Magento\Backend\Block\Widget\Button\Toolbar\Interceptor $subject
     * @param \Magento\Framework\View\Element\AbstractBlock $context
     * @param \Magento\Backend\Block\Widget\Button\ButtonList $buttonList
     * @return void
     */
    public function beforePushButtons(
        \Magento\Backend\Block\Widget\Button\Toolbar\Interceptor $subject,
        \Magento\Framework\View\Element\AbstractBlock $context,
        \Magento\Backend\Block\Widget\Button\ButtonList $buttonList
    ): void {
        $this->_request = $context->getRequest();
        if ($this->_request->getFullActionName() == 'sales_order_view') {
            $order_id = $this->_request->getParams();
            if (isset($order_id['order_id'])) {
                $current_order = $this->order->load($order_id['order_id']);
                if ($current_order->canShip()) {
                    $buttonList->add(
                        'postex_ship',
                        [
                            'label' => __('Book at PostEx'),
                            'onclick' => 'jQuery("#postexShipModal").modal("openModal")',
                        ]
                    );
                }
            }
        }
    }
}
