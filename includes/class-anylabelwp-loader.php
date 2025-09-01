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
            'fluent-crm' => 'Fluent_CRM',
            'wp-social-ninja' => 'WP_Social_Ninja'
        ];

        foreach ($modules as $module => $class_name) {
            $module_file = ANYLABELWP_PLUGIN_DIR . "modules/{$module}/class-{$module}.php";
            if (file_exists($module_file)) {
                require_once $module_file;
                $module_class = "AnylabelWP_{$class_name}";
                if (class_exists($module_class)) {
                    new $module_class();
                }
            }
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
        add_options_page(
            __('AnylabelWP Settings', 'anylabelwp-plugin'),
            __('AnylabelWP', 'anylabelwp-plugin'),
            'manage_options',
            'anylabelwp-settings',
            [$this, 'display_plugin_admin_page']
        );
    }

    /**
     * Display the admin settings page
     */
    public function display_plugin_admin_page()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'anylabelwp-plugin'));
        }

        require_once ANYLABELWP_PLUGIN_DIR . 'views/admin-settings.php';
    }

    /**
     * Enqueue plugin admin CSS
     */
    public function enqueue_admin_styles($hook)
    {
        if ($hook !== 'settings_page_anylabelwp-settings') {
            return;
        }
        
        wp_enqueue_style(
            'anylabelwp-admin-styles',
            ANYLABELWP_PLUGIN_URL . 'assets/css/admin.css',
            [],
            ANYLABELWP_VERSION
        );
    }

    /**
     * Enqueue plugin admin JavaScript
     */
    public function enqueue_admin_scripts($hook)
    {
        if ($hook !== 'settings_page_anylabelwp-settings') {
            return;
        }
        
        wp_enqueue_script(
            'anylabelwp-admin-script',
            ANYLABELWP_PLUGIN_URL . 'assets/js/admin.js',
            ['jquery'],
            ANYLABELWP_VERSION,
            true
        );
    }
}
