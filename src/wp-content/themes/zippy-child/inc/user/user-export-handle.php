<?php
add_action('admin_post_export_users_csv', 'handle_export_users_csv');

function handle_export_users_csv()
{
    if (!current_user_can('manage_options')) {
        wp_die('Permission denied');
    }

    check_admin_referer('export_users_csv');

    $users = get_users([
        'fields' => [
            'ID',
            'user_registered',
            'user_login',
            'user_email',
            'display_name',
        ]
    ]);

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=Users-list-' . date('Y-m-d') . '.csv');

    $output = fopen('php://output', 'w');

    fputcsv($output, [
        'ID',
        'Student ID',
        'Username',
        'Email',
        'Display Name',
    ]);

    foreach ($users as $user) {
        $u = get_userdata($user->ID);

        $student_id = get_user_meta($u->ID, 'student_id', true);

        fputcsv($output, [
            $u->ID,
            $student_id,
            $u->user_login,
            $u->user_email,
            $u->display_name,
        ]);
    }

    fclose($output);
    exit;
}
