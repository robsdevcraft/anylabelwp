<div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
    
    <?php
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }
    ?>

    <h2 class="nav-tab-wrapper">
        <?php 
        $allowed_tabs = ['smtp', 'forms', 'crm'];
        $current_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'smtp';
        $current_tab = in_array($current_tab, $allowed_tabs) ? $current_tab : 'smtp';
        ?>
        <a href="<?php echo esc_url(add_query_arg(['page' => 'anylabelwp-settings', 'tab' => 'smtp'])); ?>" class="nav-tab <?php echo $current_tab === 'smtp' ? 'nav-tab-active' : ''; ?>">Fluent SMTP</a>
        <a href="<?php echo esc_url(add_query_arg(['page' => 'anylabelwp-settings', 'tab' => 'forms'])); ?>" class="nav-tab <?php echo $current_tab === 'forms' ? 'nav-tab-active' : ''; ?>">Fluent Forms</a>
        <a href="<?php echo esc_url(add_query_arg(['page' => 'anylabelwp-settings', 'tab' => 'crm'])); ?>" class="nav-tab <?php echo $current_tab === 'crm' ? 'nav-tab-active' : ''; ?>">Fluent CRM</a>
    </h2>

    <?php
    $active_tab = $current_tab;
    ?>

    <form action="<?php echo esc_url(admin_url('options.php')); ?>" method="post">
        <?php settings_fields( 'anylabelwp_settings' ); ?>
        
        <?php if ($active_tab === 'smtp'): ?>
            <div class="tab-content smtp-settings">
                <?php
                do_settings_sections( 'anylabelwp_settings' );
                do_action( 'anylabelwp_render_module_settings' );
                ?>
            </div>
        <?php elseif ($active_tab === 'forms'): ?>
            <div class="tab-content forms-settings">
                <p>Fluent Forms settings go here</p>
            </div>
        <?php elseif ($active_tab === 'crm'): ?>
            <div class="tab-content crm-settings">
                <p>Fluent CRM settings go here</p>
            </div>
        <?php endif; ?>

        <?php if (isset($_POST['anylabelwp_settings_nonce']) && !wp_verify_nonce($_POST['anylabelwp_settings_nonce'], 'anylabelwp_settings')) {
            wp_die(__('Security check failed.'));
        } ?>
        <?php submit_button( 'Save Changes' ); ?>
    </form>
</div>

<style>
.tab-content {
    margin-top: 20px;
}
</style>
