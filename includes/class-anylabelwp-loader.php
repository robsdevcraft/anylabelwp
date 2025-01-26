<?php
namespace AnylabelWP;

/**
 * Main Loader class for AnylabelWP plugin
 */
class Loader
{
    /**
     * Run all plugin setup
     */
    public function run()
    {
        add_action('admin_menu', [$this, 'add_plugin_admin_menu']);
        // Force admin CSS to load for all admin users:
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_styles']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);

        $this->load_modules();
    }

    /**
     * Load and instantiate each active module class
     */
    private function load_modules()
    {
        $modules = [
            'fluent-smtp'  => 'Fluent_SMTP',
            'fluent-forms' => 'Fluent_Forms',
            // Add other modules here
        ];

        foreach ($modules as $module => $class_name) {
            require_once ANYLABELWP_PLUGIN_DIR . "modules/{$module}/class-{$module}.php";
            $module_class = "AnylabelWP_{$class_name}";
            new $module_class();
        }

        // Load General Settings if it exists
        $general_file = ANYLABELWP_PLUGIN_DIR . 'modules/general/class-general-settings.php';
        if (file_exists($general_file)) {
            require_once $general_file;
            if (class_exists('AnylabelWP\AnylabelWP_General_Settings')) {
                new \AnylabelWP\AnylabelWP_General_Settings();
            }
        }
    }

    /**
     * Add AnylabelWP settings page to the WordPress Settings menu
     */
    public function add_plugin_admin_menu()
    {
        // If you want to hide the settings from unselected roles again, re-enable the check:
        // if (!$this->current_user_has_allowed_role()) {
        //     return;
        // }

        add_options_page(
            __('AnylabelWP Settings', 'anylabelwp-plugin'),
            __('AnylabelWP', 'anylabelwp-plugin'),
            'manage_options', // fallback capability check
            'anylabelwp-settings',
            [$this, 'display_plugin_admin_page']
        );
    }

    /**
     * (Optional) role check to see if user can access the plugin
     */
    private function current_user_has_allowed_role()
    {
        $allowed_roles = get_option('anylabelwp_allowed_roles', []);
        if (empty($allowed_roles)) {
            // If no roles have been saved, default to Administrator
            $allowed_roles = ['administrator'];
        }
        $user = wp_get_current_user();
        if (!isset($user->roles) || !is_array($user->roles)) {
            return false;
        }
        foreach ($user->roles as $role) {
            if (in_array($role, $allowed_roles, true)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Display the admin settings page
     */
    public function display_plugin_admin_page()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'anylabelwp-plugin'));
        }

        // Basic nonce check if needed
        if (isset($_POST['action']) && $_POST['action'] === 'update') {
            if (!isset($_POST['anylabelwp_nonce']) || !wp_verify_nonce($_POST['anylabelwp_nonce'], 'anylabelwp_settings')) {
                wp_die(__('Security check failed', 'anylabelwp-plugin'));
            }
            // Example: process form data if needed
            if (isset($_POST['some_option'])) {
                $some_option = sanitize_text_field($_POST['some_option']);
                if ($this->validate_some_option($some_option)) {
                    update_option('anylabelwp_some_option', $some_option);
                } else {
                    add_settings_error('anylabelwp_messages', 'anylabelwp_message', __('Invalid option value', 'anylabelwp-plugin'), 'error');
                }
            }
        }

        require_once ANYLABELWP_PLUGIN_DIR . 'views/admin-settings.php';
    }

    /**
     * Example validation stub
     *
     * @param string $option
     * @return bool
     */
    private function validate_some_option($option)
    {
        // Add your validation logic
        return true;
    }

    /**
     * Enqueue plugin admin CSS for all admin pages
     */
    public function enqueue_admin_styles()
    {
        wp_enqueue_style(
            'anylabelwp-admin-styles',
            ANYLABELWP_PLUGIN_URL . 'assets/css/admin.css',
            [],
            ANYLABELWP_VERSION
        );
    }

    /**
     * Enqueue plugin admin JavaScript for all admin pages
     */
    public function enqueue_admin_scripts()
    {
        wp_enqueue_script(
            'anylabelwp-admin-script',
            ANYLABELWP_PLUGIN_URL . 'assets/js/admin.js',
            [],
            ANYLABELWP_VERSION,
            true
        );
    }
}
