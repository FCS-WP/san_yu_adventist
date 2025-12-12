<?php
add_action('init', 'lightbox_login_form');
function lightbox_login_form()
{
    add_action('wp_body_open', function () {
        if (!is_user_logged_in() && !is_account_page()) {
            echo do_shortcode('[lightbox auto_open="true" auto_timer="2000" auto_show="always" width="500px" padding="0"][block id="login-popup"][/lightbox]');
        }
    });
}

 add_action('wp_footer', function(){
    if (!is_user_logged_in() && !is_account_page()) {
        echo do_shortcode('[lightbox id="my-login-popup" auto_open="false" width="600px"][block id="login-popup"][/lightbox]');
    }
});

function popup_login_shortcode()
{
    // Get category parent "level"
    $level_parent = get_term_by('slug', 'level', 'product_cat');

    $categories = [];
    if ($level_parent) {
        // Get only children of "level"
        $categories = get_terms([
            'taxonomy'   => 'product_cat',
            'hide_empty' => true,
            'parent'     => $level_parent->term_id,
        ]);
    }

    ob_start();
?>

    <div class="student-login-wrapper">
        <form id="student-login-form">
            <div class="student-login-message"></div>

            <label>Student Name *</label>
            <input type="text" name="student_name" required placeholder="Enter student name">

            <label>Level *</label>
            <select name="level" required>
                <option value="" disabled selected>Select level</option>

                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo esc_attr($cat->slug); ?>">
                        <?php echo esc_html($cat->name); ?>
                    </option>
                <?php endforeach; ?>

            </select>

            <label>Student ID *</label>
            <input type="text" name="student_id" required placeholder="Enter student ID">

            <div class="submit"><button type="submit">Login</button></div>
        </form>
    </div>

<?php
    return ob_get_clean();
}
add_shortcode('popup_login_form', 'popup_login_shortcode');
