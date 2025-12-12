<?php
// AJAX HANDLE LOGIN
add_action('wp_ajax_student_login', 'handle_student_login');
add_action('wp_ajax_nopriv_student_login', 'handle_student_login');

function handle_student_login()
{
    $input_student_id = sanitize_text_field($_POST['student_id']);
    $input_name       = strtolower(trim($_POST['student_name']));
    $input_level      = sanitize_text_field($_POST['level']);

    // Find user by student_id
    $users = get_users([
        'meta_key'   => 'student_id',
        'meta_value' => $input_student_id,
        'number'     => 1
    ]);

    if (empty($users)) {
        wp_send_json_error(['message' => 'Invalid student ID']);
        wp_die();
    }

    $user = $users[0];

    // Validate student name (switch logic)

    $first = strtolower(trim($user->first_name));
    $last  = strtolower(trim($user->last_name));
    $full  = trim($first . ' ' . $last);

    $name_valid = false;

    switch (true) {

        // both first + last exist → require exact full name match
        case (!empty($first) && !empty($last)):
            if ($input_name === $full) {
                $name_valid = true;
            }
            break;

        // only first exists → match first name only
        case (!empty($first) && empty($last)):
            if ($input_name === $first) {
                $name_valid = true;
            }
            break;

        // only last exists → match last name only
        case (empty($first) && !empty($last)):
            if ($input_name === $last) {
                $name_valid = true;
            }
            break;

        // no name stored → automatically pass
        default:
            $name_valid = true;
            break;
    }

    if (!$name_valid) {
        wp_send_json_error(['message' => 'Incorrect student name']);
        wp_die();
    }

    // Validate level

    $user_level_term_id = get_user_meta($user->ID, 'student_level', true);
    $user_level_term    = get_term($user_level_term_id, 'product_cat');
    $user_level_slug    = $user_level_term ? $user_level_term->slug : '';

    // if ($user_level_slug !== $input_level) {
    //     wp_send_json_error(['message' => 'Incorrect level']);
    //     wp_die();
    // }

    // All checks passed → Login user

    wp_set_current_user($user->ID);
    wp_set_auth_cookie($user->ID, true);

    // Save selected level into cookie
    setcookie('selected_level', $input_level, time() + 3600, "/");


    wp_send_json_success([
        'message' => 'Login successful',
        'user'    => $user->user_login,
        'level'   => $user_level_slug
    ]);

    wp_die();
}
