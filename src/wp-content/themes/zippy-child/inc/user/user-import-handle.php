<?php
add_action('admin_post_import_users_csv', 'handle_import_users_csv');

function handle_import_users_csv()
{
    if (!current_user_can('manage_options')) wp_die('No permission');
    check_admin_referer('import_users_csv');

    if (empty($_FILES['csv_file']['tmp_name'])) {
        wp_die('No file uploaded');
    }

    $file = fopen($_FILES['csv_file']['tmp_name'], 'r');
    $header = fgetcsv($file);
    $map = array_flip($header);

    $duplicate_ids = [];

    while (($row = fgetcsv($file)) !== false) {

        $username   = sanitize_text_field( wp_unslash($row[$map['Username']] ?? '') );
        $email      = sanitize_email( wp_unslash($row[$map['Email']] ?? '') );
        $name       = sanitize_text_field( wp_unslash($row[$map['Display Name']] ?? '') );
        $student_id = sanitize_text_field( wp_unslash($row[$map['Student ID']] ?? '') );

        if (!$student_id) {
            $duplicate_ids[] = '(empty)';
            continue;
        }

        // Find user by Student ID
        $users = get_users([
            'meta_key'   => 'student_id',
            'meta_value' => $student_id,
            'number'     => 1,
            'fields'     => 'ID'
        ]);

        if (!empty($users)) {
            // Update existing student
            $user_id = $users[0];

            wp_update_user([
                'ID'           => $user_id,
                'user_email'   => $email,
                'display_name'=> $name,
            ]);
        } else {
            // Create new user
            if (!$username || !$email) continue;

            $password = wp_generate_password(12, false);
            $user_id = wp_create_user($username, $password, $email);

            if (is_wp_error($user_id)) continue;

            wp_update_user([
                'ID' => $user_id,
                'display_name' => $name,
            ]);
        }

        // Safety check: Student ID already used by another user
        $conflict = get_users([
            'meta_key'   => 'student_id',
            'meta_value' => $student_id,
            'exclude'    => [$user_id],
            'number'     => 1,
            'fields'     => 'ID'
        ]);

        if (!empty($conflict)) {
            $duplicate_ids[] = $student_id;
            continue;
        }

        update_user_meta($user_id, 'student_id', $student_id);
    }

    fclose($file);

    $duplicate_ids = array_unique($duplicate_ids);

    if (!empty($duplicate_ids)) {
        wp_redirect(admin_url('users.php?page=import-users&error=' . urlencode(implode(', ', $duplicate_ids))));
        exit;
    }

    wp_redirect(admin_url('users.php?page=import-users&success=1'));
    exit;
}

