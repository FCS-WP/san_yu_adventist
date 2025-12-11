<?php

function popup_login_shortcode()
{
    $exclude_slugs = ['uniforms', 'uncategorized'];

    $exclude_ids = [];
    foreach ($exclude_slugs as $slug) {
        $term = get_term_by('slug', $slug, 'product_cat');
        if ($term) {
            $exclude_ids[] = $term->term_id;
        }
    }

    $categories = get_terms([
        'taxonomy'   => 'product_cat',
        'hide_empty' => true,
        'parent'     => 0,
        'exclude'    => $exclude_ids,
    ]);

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

            <button type="submit">Login</button>
        </form>

    </div>

<?php
    return ob_get_clean();
}
add_shortcode('popup_login_form', 'popup_login_shortcode');


add_action('wp_ajax_student_login', 'ajax_student_login_handler');
add_action('wp_ajax_nopriv_student_login', 'ajax_student_login_handler');

function ajax_student_login_handler() {

    check_ajax_referer('student_login_nonce', 'nonce');

    $username   = sanitize_text_field($_POST['student_name']);
    $student_id = sanitize_text_field($_POST['student_id']);
    $level_slug = sanitize_text_field($_POST['level']);

    if (empty($username)) {
        wp_send_json_error(['message' => '❌ Vui lòng nhập Student Name.']);
    }

    if (empty($student_id)) {
        wp_send_json_error(['message' => '❌ Vui lòng nhập Student ID.']);
    }

    // Get user by username
    $user = get_user_by('login', $username);
    if (!$user) {
        wp_send_json_error(['message' => '❌ Student Name không tồn tại.']);
    }

    // Get saved student ID
    $saved_id = get_user_meta($user->ID, 'student_id', true);

    // Nếu user chưa có student_id → gán lần đầu
    if (empty($saved_id)) {
        update_user_meta($user->ID, 'student_id', $student_id);
        $saved_id = $student_id;
    }

    // Validate student ID
    if ($saved_id !== $student_id) {
        wp_send_json_error(['message' => '❌ Student ID không đúng.']);
    }

    // Validate level slug
    $term = get_term_by('slug', $level_slug, 'product_cat');
    if (!$term) {
        wp_send_json_error(['message' => '❌ Level không hợp lệ.']);
    }

    // Login user
    wp_set_current_user($user->ID);
    wp_set_auth_cookie($user->ID);

    // Redirect
    $redirect_url = get_term_link($term);

    wp_send_json_success([
        'message'  => 'Login successful',
        'redirect' => $redirect_url
    ]);
}


