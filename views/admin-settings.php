<div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
    <form action="options.php" method="post">
        <?php
        settings_fields( 'anylabelwp_settings' );
        do_settings_sections( 'anylabelwp_settings' );
        ?>
        <h2>General Settings</h2>
        <!-- Add any general plugin settings here -->

        <?php do_action( 'anylabelwp_render_module_settings' ); ?>

        <?php wp_nonce_field( 'anylabelwp_settings', 'anylabelwp_settings_nonce' ); ?>
        <?php submit_button( 'Save Changes' ); ?>
    </form>
</div>
