<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <?php
    // Basic capability check
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.', 'anylabelwp-plugin'));
    }
    ?>

    <h2 class="nav-tab-wrapper">
        <?php
        $allowed_tabs = ['general', 'smtp', 'forms', 'crm', 'socials'];
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
        <a href="<?php echo esc_url(add_query_arg(['page' => 'anylabelwp-settings', 'tab' => 'socials'])); ?>"
           class="nav-tab <?php echo $current_tab === 'crm' ? 'nav-tab-active' : ''; ?>">
            <?php echo esc_html__('WP Social Ninja', 'anylabelwp-plugin'); ?>
        </a>
    </h2>

    <form action="<?php echo esc_url(admin_url('options.php')); ?>" method="post">
        <?php
        /**
         * For each tab, call settings_fields() and do_settings_sections() 
         * on the matching group. This prevents overlap between tabs.
         */
        if ($current_tab === 'general') {
            settings_fields('anylabelwp_general');
            do_settings_sections('anylabelwp_general');
        } elseif ($current_tab === 'smtp') {
            settings_fields('anylabelwp_smtp');
            do_settings_sections('anylabelwp_smtp');
        } elseif ($current_tab === 'forms') {
            settings_fields('anylabelwp_forms');
            do_settings_sections('anylabelwp_forms');
        } elseif ($current_tab === 'crm') {
            settings_fields('anylabelwp_crm');
            do_settings_sections('anylabelwp_crm');
        } elseif ($current_tab === 'socials') {
            settings_fields('anylabelwp_social_media');
            do_settings_sections('anylabelwp_social_media');
        } 
        ?>

        <?php
        // Extra nonce check
        if (isset($_POST['anylabelwp_settings_nonce'])
            && !wp_verify_nonce($_POST['anylabelwp_settings_nonce'], 'anylabelwp_settings')) {
            wp_die(__('Security check failed.', 'anylabelwp-plugin'));
        }

        /**
         * Fire actions for each tab to render UI
         */
        if ($current_tab === 'general') {
            do_action('anylabelwp_render_general_settings');
        } elseif ($current_tab === 'smtp') {
            do_action('anylabelwp_render_smtp_settings');
        } elseif ($current_tab === 'forms') {
            do_action('anylabelwp_render_forms_settings');
        } elseif ($current_tab === 'crm') {
            do_action('anylabelwp_render_crm_settings');
        } elseif ($current_tab === 'socials') {
            do_action('anylabelwp_render_wp_social_ninja_settings');
        }
        ?>

        <?php submit_button(__('Save Changes', 'anylabelwp-plugin')); ?>
    </form>
</div>
