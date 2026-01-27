<?php
// Remove account dashboard
add_filter( 'woocommerce_account_menu_items', 'remove_dashboard_link' );
function remove_dashboard_link( $menu_links ){
    unset( $menu_links['dashboard'] );
    return $menu_links;
}



add_action('show_user_profile', 'add_student_id_field');
add_action('edit_user_profile', 'add_student_id_field');
function add_student_id_field($user)
{
    if (!current_user_can('manage_options')) return;

    $student_id = get_user_meta($user->ID, 'student_id', true);
?>
    <h2>Student</h2>
    <table class="form-table">
        <tr>
            <th><label for="student_id">Student ID</label></th>
            <td>
                <input type="text"
                    name="student_id"
                    id="student_id"
                    value="<?php echo esc_attr($student_id); ?>"
                    class="regular-text" />
            </td>
        </tr>
    </table>
<?php
}


add_action('personal_options_update', 'save_student_id_fields');
add_action('edit_user_profile_update', 'save_student_id_fields');
function save_student_id_fields($user_id)
{
    if (!current_user_can('manage_options')) return;
    if (isset($_POST['student_id'])) {
        $new_student_id = sanitize_text_field(wp_unslash($_POST['student_id']));

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

    if (isset($_POST['student_level'])) {
        update_user_meta($user_id, 'student_level', intval($_POST['student_level']));
    }
}


// Display student ID in users list
add_filter('manage_users_columns', function ($columns) {
    $columns['student_id'] = __('Student ID');
    return $columns;
});
add_filter('manage_users_custom_column', function ($value, $column_name, $user_id) {
    if ($column_name === 'student_id') {
        $student_id = get_user_meta($user_id, 'student_id', true);
        return $student_id ? esc_html($student_id) : '—';
    }
    return $value;
}, 10, 3);

