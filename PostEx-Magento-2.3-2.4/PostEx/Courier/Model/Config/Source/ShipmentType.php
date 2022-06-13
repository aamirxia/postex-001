<?php

namespace PostEx\Courier\Model\Config\Source;

/**
 * Class ShipmentType
 * @package PostEx\Courier\Model\Config\Source
 */
class ShipmentType implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('Normal')],
            ['value' => 1, 'label' => __('Reversed')],
            ['value' => 2, 'label' => __('Replacement')],
        ];
    }
}
