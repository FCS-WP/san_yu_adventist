<?php 
add_action('show_user_profile', 'add_student_custom_fields');
add_action('edit_user_profile', 'add_student_custom_fields');

function add_student_custom_fields($user) {
    $student_id    = get_user_meta($user->ID, 'student_id', true);

    $exclude_slugs = ['uniforms', 'uncategorized'];

    $exclude_ids = [];
    foreach ($exclude_slugs as $slug) {
        $term = get_term_by('slug', $slug, 'product_cat');
        if ($term) {
            $exclude_ids[] = $term->term_id;
        }
    }
    ?>
    
    <h3>Student Information</h3>

    <table class="form-table">
        <tr>
            <th><label for="student_id">Student ID</label></th>
            <td>
                <input type="text" name="student_id" id="student_id"
                    value="<?php echo esc_attr($student_id); ?>"
                    class="regular-text">
                <p class="description">Enter the student's unique ID.</p>
            </td>
        </tr>
    </table>
    <?php
}


add_action('personal_options_update', 'save_student_custom_fields');
add_action('edit_user_profile_update', 'save_student_custom_fields');

function save_student_custom_fields($user_id) {
    if (!current_user_can('manage_options')) return;

    if (isset($_POST['student_id'])) {
        update_user_meta($user_id, 'student_id', sanitize_text_field($_POST['student_id']));
    }
}
