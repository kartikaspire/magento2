<?php

namespace Aspire\Module\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Page Options
 */
class ConfigOption implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'catalog_category_view', 'label' => __('Category')],
            ['value' => 'catalog_product_view', 'label' => __('Product Details')],
            ['value' => 'checkout_cart_index', 'label' => __('Cart')],
            ['value' => 'checkout_index_index', 'label' => __('Checkout')]
        ];
    }
}