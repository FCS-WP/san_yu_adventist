<?php

add_filter('woocommerce_dropdown_variation_attribute_options_args', 'order_variation_value_custom');

function order_variation_value_custom($args)
{
    if (empty($args['options']) || !is_array($args['options'])) {
        return $args;
    }

    $numbers = [];
    $letters = [];

    $sizeOrder = ['XXS', 'XS', 'S', 'M', 'L', 'XL', 'XXL'];

    foreach ($args['options'] as $option) {
        if (is_numeric($option)) {
            $numbers[] = $option;
        } else {
            $letters[] = strtoupper(trim($option));
        }
    }
    sort($numbers, SORT_NUMERIC);

    usort($letters, function ($a, $b) use ($sizeOrder) {
        return array_search($a, $sizeOrder) <=> array_search($b, $sizeOrder);
    });

    $args['options'] = array_merge($numbers, $letters);

    return $args;
}
