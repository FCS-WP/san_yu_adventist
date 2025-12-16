<?php
add_action('woocommerce_before_checkout_form', function () {
    echo '<h2 class="checkout-title">Checkout</h2>';
});
add_action('woocommerce_before_cart', function () {
    echo '<h2 class="checkout-title">Cart</h2>';
});


add_filter('woocommerce_cart_item_permalink', function ($permalink, $cart_item, $cart_item_key) {
    return false;
}, 10, 3);

