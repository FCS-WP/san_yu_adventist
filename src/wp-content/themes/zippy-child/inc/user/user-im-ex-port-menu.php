<?php

// Add export users menu
add_action('admin_menu', function () {
    add_users_page(
        'Export Users',
        'Export Users',
        'manage_options',
        'export-users',
        'render_export_users_page'
    );
});

function render_export_users_page(){
?>
    <div class="wrap">
        <h1>Export Users</h1>

        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
            <input type="hidden" name="action" value="export_users_csv">
            <?php wp_nonce_field('export_users_csv'); ?>

            <p>
                <button class="button button-primary">Download CSV</button>
            </p>
        </form>
    </div>
<?php
}



// Add import users menu
add_action('admin_menu', function () {
    add_users_page(
        'Import Users',
        'Import Users',
        'manage_options',
        'import-users',
        'render_import_users_page'
    );
});

function render_import_users_page(){
    ?>
    <div class="wrap">
        <h1>Import Users</h1>

        <?php if (!empty($_GET['error'])) : ?>
            <div class="notice notice-error">
                <p>
                    <strong>Duplicate Student IDs (not imported):</strong><br>
                    <?php echo esc_html($_GET['error']); ?>
                </p>
            </div>
        <?php endif; ?>

        <?php if (!empty($_GET['success'])) : ?>
            <div class="notice notice-success">
                <p>Import completed successfully.</p>
            </div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data" action="<?php echo admin_url('admin-post.php'); ?>">
            <input type="hidden" name="action" value="import_users_csv">
            <?php wp_nonce_field('import_users_csv'); ?>

            <div style="width: 100%;"><input type="file" name="csv_file" accept=".csv" required></div>
            <button class="button button-primary" style="margin-top: 10px;">Import CSV</button>
        </form>
    </div>
    <?php
}
