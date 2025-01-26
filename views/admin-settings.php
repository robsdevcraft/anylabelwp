<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <?php
    // Basic permission check
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.', 'anylabelwp-plugin'));
    }
    ?>

    <h2 class="nav-tab-wrapper">
        <?php
        $allowed_tabs = ['general', 'smtp', 'forms', 'crm'];
        $current_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'general';
        $current_tab = in_array($current_tab, $allowed_tabs, true) ? $current_tab : 'general';
        ?>
        <a href="<?php echo esc_url(add_query_arg(['page' => 'anylabelwp-settings', 'tab' => 'general'])); ?>"
           class="nav-tab <?php echo $current_tab === 'general' ? 'nav-tab-active' : ''; ?>">
            <?php echo esc_html__('General', 'anylabelwp-plugin'); ?>
        </a>
        <a href="<?php echo esc_url(add_query_arg(['page' => 'anylabelwp-settings', 'tab' => 'smtp'])); ?>"
           class="nav-tab <?php echo $current_tab === 'smtp' ? 'nav-tab-active' : ''; ?>">
            <?php echo esc_html__('Fluent SMTP', 'anylabelwp-plugin'); ?>
        </a>
        <a href="<?php echo esc_url(add_query_arg(['page' => 'anylabelwp-settings', 'tab' => 'forms'])); ?>"
           class="nav-tab <?php echo $current_tab === 'forms' ? 'nav-tab-active' : ''; ?>">
            <?php echo esc_html__('Fluent Forms', 'anylabelwp-plugin'); ?>
        </a>
        <a href="<?php echo esc_url(add_query_arg(['page' => 'anylabelwp-settings', 'tab' => 'crm'])); ?>"
           class="nav-tab <?php echo $current_tab === 'crm' ? 'nav-tab-active' : ''; ?>">
            <?php echo esc_html__('Fluent CRM', 'anylabelwp-plugin'); ?>
        </a>
    </h2>

    <form action="<?php echo esc_url(admin_url('options.php')); ?>" method="post">
        <?php wp_nonce_field('anylabelwp_settings', 'anylabelwp_settings_nonce'); ?>
        <?php settings_fields('anylabelwp_settings'); ?>
        <?php do_settings_sections('anylabelwp_settings'); ?>

        <?php
        // Extra nonce check for the sub-settings
        if (isset($_POST['anylabelwp_settings_nonce']) && !wp_verify_nonce($_POST['anylabelwp_settings_nonce'], 'anylabelwp_settings')) {
            wp_die(__('Security check failed.', 'anylabelwp-plugin'));
        }

        /**
         * We use $current_tab to decide which module’s settings to show
         * Modules hook into do_action() for their respective tab or section
         */
        if ($current_tab === 'general') {
            do_action('anylabelwp_render_general_settings');
        } elseif ($current_tab === 'smtp') {
            do_action('anylabelwp_render_smtp_settings');
        } elseif ($current_tab === 'forms') {
            do_action('anylabelwp_render_forms_settings');
        } else {
            // CRM is largely ignored here as per instructions
            echo '<div class="tab-content crm-settings">';
            echo '<p>' . esc_html__('Fluent CRM settings go here', 'anylabelwp-plugin') . '</p>';
            echo '</div>';
        }
        ?>

        <?php submit_button(__('Save Changes', 'anylabelwp-plugin')); ?>
    </form>
</div>
