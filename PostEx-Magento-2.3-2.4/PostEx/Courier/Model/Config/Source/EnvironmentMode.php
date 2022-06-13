<?php

namespace PostEx\Courier\Model\Config\Source;

/**
 * Class EnvironmentMode
 * @package PostEx\Courier\Model\Config\Source
 */
class EnvironmentMode implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'Stg', 'label' => __('Staging')],
            ['value' => 'Prod', 'label' => __('Production')],
        ];
    }
}
