<?php
add_action('personal_options_update', 'save_student_custom_fields');
add_action('edit_user_profile_update', 'save_student_custom_fields');

function save_student_custom_fields($user_id)
{
    if (!current_user_can('manage_options')) return;

    // Validate student_id duplicate
    if (isset($_POST['student_id'])) {

        $new_student_id = sanitize_text_field($_POST['student_id']);

        // Find users who already have this student_id
        $existing_users = get_users([
            'meta_key'   => 'student_id',
            'meta_value' => $new_student_id,
            'exclude'    => [$user_id],
            'number'     => 1
        ]);

        if (!empty($existing_users)) {
            wp_die(
                'Error: This student ID is already assigned to another user. Please use a unique ID.'
            );
        }

        update_user_meta($user_id, 'student_id', $new_student_id);
    }

    // Save level
    if (isset($_POST['student_level'])) {
        update_user_meta($user_id, 'student_level', intval($_POST['student_level']));
    }
}
