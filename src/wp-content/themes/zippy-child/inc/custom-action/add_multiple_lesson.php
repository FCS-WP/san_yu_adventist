<?php
add_action('wp_ajax_add_multiple_to_cart', 'add_multiple_to_cart');
add_action('wp_ajax_nopriv_add_multiple_to_cart', 'add_multiple_to_cart');

function add_multiple_to_cart()
{
    if (!isset($_POST['items'])) {
        wp_send_json_error(['message' => 'No items received']);
    }

    foreach ($_POST['items'] as $item) {
        $product_id = intval($item['product_id']);
        $quantity   = intval($item['quantity']);

        if ($quantity > 0) {
            WC()->cart->add_to_cart($product_id, $quantity);
        }
    }

    wp_send_json_success(['message' => 'Products added to cart']);
}
