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
        
        // Check plugin status for tab display
        $plugin_status = [
            'smtp' => is_plugin_active('fluent-smtp/fluent-smtp.php'),
            'forms' => is_plugin_active('fluentform/fluentform.php'),
            'crm' => is_plugin_active('fluent-crm/fluent-crm.php'),
            'socials' => is_plugin_active('wp-social-ninja/wp-social-ninja.php')
        ];
        ?>
        <a href="<?php echo esc_url(add_query_arg(['page' => 'anylabelwp-settings', 'tab' => 'general'])); ?>"
           class="nav-tab <?php echo $current_tab === 'general' ? 'nav-tab-active' : ''; ?>">
            <?php echo esc_html__('General', 'anylabelwp-plugin'); ?>
        </a>
        <a href="<?php echo esc_url(add_query_arg(['page' => 'anylabelwp-settings', 'tab' => 'smtp'])); ?>"
           class="nav-tab <?php echo $current_tab === 'smtp' ? 'nav-tab-active' : ''; ?> <?php echo !$plugin_status['smtp'] ? 'tab-inactive' : ''; ?>">
            <?php echo esc_html__('Fluent SMTP', 'anylabelwp-plugin'); ?>
            <?php if (!$plugin_status['smtp']): ?>
                <span class="dashicons dashicons-warning" style="color: #f56e28; margin-left: 5px;" title="<?php _e('Plugin not active', 'anylabelwp-plugin'); ?>"></span>
            <?php endif; ?>
        </a>
        <a href="<?php echo esc_url(add_query_arg(['page' => 'anylabelwp-settings', 'tab' => 'forms'])); ?>"
           class="nav-tab <?php echo $current_tab === 'forms' ? 'nav-tab-active' : ''; ?> <?php echo !$plugin_status['forms'] ? 'tab-inactive' : ''; ?>">
            <?php echo esc_html__('Fluent Forms', 'anylabelwp-plugin'); ?>
            <?php if (!$plugin_status['forms']): ?>
                <span class="dashicons dashicons-warning" style="color: #f56e28; margin-left: 5px;" title="<?php _e('Plugin not active', 'anylabelwp-plugin'); ?>"></span>
            <?php endif; ?>
        </a>
        <a href="<?php echo esc_url(add_query_arg(['page' => 'anylabelwp-settings', 'tab' => 'crm'])); ?>"
           class="nav-tab <?php echo $current_tab === 'crm' ? 'nav-tab-active' : ''; ?> <?php echo !$plugin_status['crm'] ? 'tab-inactive' : ''; ?>">
            <?php echo esc_html__('Fluent CRM', 'anylabelwp-plugin'); ?>
            <?php if (!$plugin_status['crm']): ?>
                <span class="dashicons dashicons-warning" style="color: #f56e28; margin-left: 5px;" title="<?php _e('Plugin not active', 'anylabelwp-plugin'); ?>"></span>
            <?php endif; ?>
        </a>
        <a href="<?php echo esc_url(add_query_arg(['page' => 'anylabelwp-settings', 'tab' => 'socials'])); ?>"
           class="nav-tab <?php echo $current_tab === 'socials' ? 'nav-tab-active' : ''; ?> <?php echo !$plugin_status['socials'] ? 'tab-inactive' : ''; ?>">
            <?php echo esc_html__('WP Social Ninja', 'anylabelwp-plugin'); ?>
            <?php if (!$plugin_status['socials']): ?>
                <span class="dashicons dashicons-warning" style="color: #f56e28; margin-left: 5px;" title="<?php _e('Plugin not active', 'anylabelwp-plugin'); ?>"></span>
            <?php endif; ?>
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
         * Show plugin installation notices for inactive plugins
         */
        if ($current_tab === 'general') {
            do_action('anylabelwp_render_general_settings');
        } elseif ($current_tab === 'smtp') {
            if (!$plugin_status['smtp']) {
                echo '<div class="notice notice-warning inline"><p>';
                printf(__('You need %s installed and activated to use AnylabelWP customizations for SMTP functionality.', 'anylabelwp-plugin'), '<strong>Fluent SMTP</strong>');
                echo '</p></div>';
            }
            do_action('anylabelwp_render_smtp_settings');
        } elseif ($current_tab === 'forms') {
            if (!$plugin_status['forms']) {
                echo '<div class="notice notice-warning inline"><p>';
                printf(__('You need %s installed and activated to use AnylabelWP customizations for Forms functionality.', 'anylabelwp-plugin'), '<strong>Fluent Forms</strong>');
                echo '</p></div>';
            }
            do_action('anylabelwp_render_forms_settings');
        } elseif ($current_tab === 'crm') {
            if (!$plugin_status['crm']) {
                echo '<div class="notice notice-warning inline"><p>';
                printf(__('You need %s installed and activated to use AnylabelWP customizations for CRM functionality.', 'anylabelwp-plugin'), '<strong>Fluent CRM</strong>');
                echo '</p></div>';
            }
            do_action('anylabelwp_render_crm_settings');
        } elseif ($current_tab === 'socials') {
            if (!$plugin_status['socials']) {
                echo '<div class="notice notice-warning inline"><p>';
                printf(__('You need %s installed and activated to use AnylabelWP customizations for Social Media functionality.', 'anylabelwp-plugin'), '<strong>WP Social Ninja</strong>');
                echo '</p></div>';
            }
            do_action('anylabelwp_render_wp_social_ninja_settings');
        }
        ?>

        <?php submit_button(__('Save Changes', 'anylabelwp-plugin')); ?>
    </form>
</div>

<style>
.tab-inactive {
    opacity: 0.7;
}
.notice.inline {
    margin: 20px 0;
    padding: 12px;
}
.notice.inline p {
    margin: 0;
}
</style>
