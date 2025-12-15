<?php
add_action('woocommerce_product_query', 'filter_shop_products_by_uniforms');

function filter_shop_products_by_uniforms($q){
    if (is_shop()) {
        $tax_query = $q->get('tax_query');

        $tax_query[] = array(
            'taxonomy' => 'product_cat',
            'field'    => 'slug',
            'terms'    => array('uniforms'),
            'operator' => 'IN',
        );

        $q->set('tax_query', $tax_query);
    }
}

// Hide product tabs
add_filter('woocommerce_product_tabs', '__return_empty_array', 98);

// Add description
add_action('woocommerce_single_product_summary', 'add_desc_after_title', 6);
function add_desc_after_title() {
    global $product;
    echo wpautop($product->get_description());
}

// Add size chart info
add_action('woocommerce_after_add_to_cart_form', 'add_size_chart_acf', 6);
function add_size_chart_acf() {
    global $product;

    $size_chart = get_field('size_chart_image', $product->get_id());

    if ($size_chart) {
        $url = $size_chart;
        echo '<div class="size-chart-img"><img src="' . esc_url($url) . '" alt="Size Chart"></div>';
    }
}

add_action ('woocommerce_before_single_product', function () {
    echo '<h2 class="single-product-title">Shop</h2>';
});

