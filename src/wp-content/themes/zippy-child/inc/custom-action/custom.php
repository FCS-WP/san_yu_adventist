<?php
add_action('woocommerce_before_checkout_form', function () {
    echo '<h2 class="checkout-title">Checkout</h2>';
});
add_action('woocommerce_before_cart', function () {
    echo '<h2 class="checkout-title">Cart</h2>';
});

add_action('template_redirect', function () {
    if (is_account_page()) {
        wc_clear_notices();
    }
});