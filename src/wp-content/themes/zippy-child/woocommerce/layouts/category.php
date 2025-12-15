<?php

/**
 * Category layout with left sidebar.
 *
 * @package          Flatsome/WooCommerce/Templates
 * @flatsome-version 3.18.7
 */

?>
<?php
$q = get_queried_object();

if (is_product_category() && $q->parent) {
	$parent_id = $q->parent;
	if ($parent_id == get_term_by('slug', 'level', 'product_cat')->term_id) {

		$level_slug = $q;
		$products = new WP_Query([
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
			'tax_query'      => [
				[
					'taxonomy' => 'product_cat',
					'field'    => 'slug',
					'terms'    => array($level_slug->slug),
					'operator' => 'IN',
				],
			],
		]);

		add_action('wp_footer', function () { ?>
			<script>
				document.addEventListener('DOMContentLoaded', function() {
					const titleBar = document.querySelector('.shop-page-title.category-page-title.page-title');
					if (titleBar) titleBar.classList.add('display-none');
				});
			</script>
		<?php });

		?>
		<div class="row row-main">
			<h2 class="lesson-shop-title">Shop</h2>
			<div id="lesson-shop-wrapper">
				<h2 class="booklist-title text-center mb-lg-2 mb-1"><?php echo esc_html($level_slug->name); ?></h2>

				<div class="booklist-scroll">
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
		</div>

	<?php
	} else {
	add_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
	?>
		<div class="row row-main"> 
			<h1 class="lesson-shop-title">Shop</h1>
		</div>
		<div class="row category-page-row">
			<h2 class="booklist-title text-center mb-lg-2 mb-1"><?php echo esc_html(get_term($q->parent)->name); ?></h2>
			<div class="col large-3 hide-for-medium <?php flatsome_sidebar_classes(); ?>">
				<?php flatsome_sticky_column_open('category_sticky_sidebar'); ?>
				<div id="shop-sidebar" class="sidebar-inner col-inner">
					<?php
					if (is_active_sidebar('shop-sidebar')) {
						dynamic_sidebar('shop-sidebar');
					} else {
						echo '<p>You need to assign Widgets to <strong>"Shop Sidebar"</strong> in <a href="' . get_site_url() . '/wp-admin/widgets.php">Appearance > Widgets</a> to show anything here</p>';
					}
					?>
				</div>
				<?php flatsome_sticky_column_close('category_sticky_sidebar'); ?>
			</div>

			<div class="col large-9">
				<?php
				do_action('woocommerce_before_main_content');

				if (fl_woocommerce_version_check('8.8.0')) {
					do_action('woocommerce_shop_loop_header');
				} else {
					do_action('woocommerce_archive_description');
				}

				if (woocommerce_product_loop()) {

					do_action('woocommerce_before_shop_loop');

					woocommerce_product_loop_start();

					if (wc_get_loop_prop('total')) {
						while (have_posts()) {
							the_post();

							do_action('woocommerce_shop_loop');

							wc_get_template_part('content', 'product');
						}
					}

					woocommerce_product_loop_end();

					do_action('woocommerce_after_shop_loop');
				} else {
					do_action('woocommerce_no_products_found');
				}
				?>

				<?php
				do_action('flatsome_products_after');
				do_action('woocommerce_after_main_content');
				?>
			</div>
		</div>

	<?php
	}
} else {
	add_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
	?>
	<div class="row row-main"> 
		<h1 class="lesson-shop-title">Shop</h1>
	</div>
	<div class="row category-page-row">
		<h2 class="booklist-title text-center mb-lg-2 mb-1">Uniforms</h2>
		<div class="col large-3 hide-for-medium <?php flatsome_sidebar_classes(); ?>">
			<?php flatsome_sticky_column_open('category_sticky_sidebar'); ?>
			<div id="shop-sidebar" class="sidebar-inner col-inner">
				<?php
				if (is_active_sidebar('shop-sidebar')) {
					dynamic_sidebar('shop-sidebar');
				} else {
					echo '<p>You need to assign Widgets to <strong>"Shop Sidebar"</strong> in <a href="' . get_site_url() . '/wp-admin/widgets.php">Appearance > Widgets</a> to show anything here</p>';
				}
				?>
			</div>
			<?php flatsome_sticky_column_close('category_sticky_sidebar'); ?>
		</div>

		<div class="col large-9">
			<?php
			do_action('woocommerce_before_main_content');

			if (fl_woocommerce_version_check('8.8.0')) {
				do_action('woocommerce_shop_loop_header');
			} else {
				do_action('woocommerce_archive_description');
			}

			if (woocommerce_product_loop()) {

				do_action('woocommerce_before_shop_loop');

				woocommerce_product_loop_start();

				if (wc_get_loop_prop('total')) {
					while (have_posts()) {
						the_post();

						do_action('woocommerce_shop_loop');

						wc_get_template_part('content', 'product');

					}
				}

				woocommerce_product_loop_end();

				do_action('woocommerce_after_shop_loop');
			} else {
				do_action('woocommerce_no_products_found');
			}
			?>

			<?php
			do_action('flatsome_products_after');
			do_action('woocommerce_after_main_content');
			?>
		</div>
	</div>

<?php
}
?>