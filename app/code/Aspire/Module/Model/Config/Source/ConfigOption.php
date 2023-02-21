<?php

namespace Aspire\Module\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;


class ConfigOption implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => '1', 'label' => __('Category')],
            ['value' => '2', 'label' => __('Product Details')],
            ['value' => '3', 'label' => __('Cart')],
            ['value' => '4', 'label' => __('Checkout')]
        ];
    }
}