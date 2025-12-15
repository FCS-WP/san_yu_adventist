<?php
add_shortcode('lesson_shop', 'lesson_shop_shortcode');

function lesson_shop_shortcode()
{
    if (!is_user_logged_in()) {
        return '<p>Please login to view your booklist.</p>';
    }

    $user_id = get_current_user_id();
    $level_term_id = get_user_meta($user_id, 'student_level', true);
    $level_term = get_term($level_term_id, 'product_cat');

    $level_slug = isset($_COOKIE['selected_level']) ? sanitize_text_field($_COOKIE['selected_level']) : '';

    if (!$level_slug) {
        if ($level_term) {
            $level_slug = $level_term->slug;
        } else {
            return '<p>No level assigned. Please contact administrator.</p>';
        }
    }

    $args = [
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
        'tax_query'      => [
            [
                'taxonomy' => 'product_cat',
                'field'    => 'slug',
                'terms'    => $level_slug,
            ]
        ]
    ];

    $products = new WP_Query($args);

    ob_start();
?>

    <div id="lesson-shop-wrapper">

        <h2 class="booklist-title text-center mb-lg-2 mb-1"><?php echo esc_html(get_term_by('slug', $level_slug, 'product_cat')->name); ?> Booklist</h2>

        <div>
            <table class="booklist-table">
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Title of Books</th>
                        <th>Publisher</th>
                        <th>Price ($)</th>
                        <th>Quantity</th>
                    </tr>
                </thead>
                <tbody>

                    <?php if ($products->have_posts()) : ?>
                        <?php while ($products->have_posts()) : $products->the_post();
                            $product = wc_get_product(get_the_ID());
                        ?>
                            <tr>
                                <td><?php echo esc_html(get_the_terms(get_the_ID(), 'product_tag')[0]->name);?></td>
                                <td><?php the_title(); ?></td>
                                <td><?php echo esc_html(get_the_author()); ?></td>
                                <td><?php echo wc_price($product->get_price()); ?></td>

                                <td class="quantity">
                                    <div class="custom-qty-wrapper">
                                        <button type="button" class="custom-minus">-</button>

                                        <input
                                            type="number"
                                            name="qty_<?php echo $product->get_id(); ?>"
                                            class="custom-qty"
                                            value="0"
                                            min="0"
                                            max="<?php echo $product->get_max_purchase_quantity(); ?>"
                                            step="1" />

                                        <button type="button" class="custom-plus">+</button>
                                    </div>
                                </td>

                            </tr>

                        <?php endwhile; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="4">No products found for this level.</td>
                        </tr>
                    <?php endif; ?>

                </tbody>
            </table>
        </div>
        <div class="add-all-btn-wrapper">
            <button id="add-selected-to-cart" class="add-all-btn">Add Selected to Cart</button>
        </div>

    </div>

<?php
    wp_reset_postdata();
    return ob_get_clean();
}
