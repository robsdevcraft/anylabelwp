<?php
namespace AnylabelWP;

/**
 * Main Loader class for AnylabelWP plugin
 */
class Loader
{
    /**
     * Store module instances for cleanup
     */
    private $module_instances = [];
    
    /**
     * Run all plugin setup
     */
    public function run()
    {
        // Only add basic hooks first to test if plugin loads
        add_action('admin_menu', [$this, 'add_plugin_admin_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_styles']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);

        // Comment out module loading for now to test basic functionality
        // $this->load_modules();
        
        // Add a simple admin notice to confirm plugin is loading
        add_action('admin_notices', [$this, 'plugin_loaded_notice']);
    }
    
    /**
     * Simple admin notice to confirm plugin is loading
     */
    public function plugin_loaded_notice()
    {
        if (isset($_GET['page']) && $_GET['page'] === 'anylabelwp-settings') {
            echo '<div class="notice notice-success is-dismissible"><p>AnylabelWP plugin is loaded and working!</p></div>';
        }
    }
    
    /**
     * Clean shutdown method
     */
    public function shutdown()
    {
        // Remove all hooks added by this plugin
        remove_action('admin_menu', [$this, 'add_plugin_admin_menu']);
        remove_action('admin_enqueue_scripts', [$this, 'enqueue_admin_styles']);
        remove_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
        remove_action('admin_notices', [$this, 'plugin_loaded_notice']);
        
        // Clear module instances
        $this->module_instances = [];
    }
    
    /**
     * Emergency shutdown for plugin deletion
     */
    public function emergency_shutdown()
    {
        // More aggressive cleanup
        $this->shutdown();
        
        // Force remove any remaining hooks
        global $wp_filter;
        if (isset($wp_filter['admin_menu'])) {
            foreach ($wp_filter['admin_menu']->callbacks as $priority => $callbacks) {
                foreach ($callbacks as $key => $callback) {
                    if (is_array($callback['function']) && 
                        is_object($callback['function'][0]) && 
                        $callback['function'][0] instanceof self) {
                        unset($wp_filter['admin_menu']->callbacks[$priority][$key]);
                    }
                }
            }
        }
        
        // Clear all instances
        $this->module_instances = [];
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
            // Add other modules here
        ];

        foreach ($modules as $module => $class_name) {
            try {
                $module_file = ANYLABELWP_PLUGIN_DIR . "modules/{$module}/class-{$module}.php";
                if (file_exists($module_file)) {
                    require_once $module_file;
                    $module_class = "AnylabelWP_{$class_name}";
                    if (class_exists($module_class)) {
                        $this->module_instances[$module] = new $module_class();
                    } else {
                        error_log("AnylabelWP: Class {$module_class} not found in {$module_file}");
                    }
                } else {
                    error_log("AnylabelWP: Module file not found: {$module_file}");
                }
            } catch (\Exception $e) {
                error_log("AnylabelWP: Error loading module {$module}: " . $e->getMessage());
            }
        }

        // Load General Settings if it exists
        try {
            $general_file = ANYLABELWP_PLUGIN_DIR . 'modules/general/class-general-settings.php';
            if (file_exists($general_file)) {
                require_once $general_file;
                if (class_exists('AnylabelWP\AnylabelWP_General_Settings')) {
                    $this->module_instances['general'] = new \AnylabelWP\AnylabelWP_General_Settings();
                }
            }
        } catch (\Exception $e) {
            error_log("AnylabelWP: Error loading general settings: " . $e->getMessage());
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
     * Enqueue plugin admin CSS for relevant admin pages only
     */
    public function enqueue_admin_styles($hook)
    {
        // Only load on our plugin's settings page
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
     * Enqueue plugin admin JavaScript for relevant admin pages only
     */
    public function enqueue_admin_scripts($hook)
    {
        // Only load on our plugin's settings page
        if ($hook !== 'settings_page_anylabelwp-settings') {
            return;
        }
        
        wp_enqueue_script(
            'anylabelwp-admin-script',
            ANYLABELWP_PLUGIN_URL . 'assets/js/admin.js',
            ['jquery'], // Add jQuery dependency
            ANYLABELWP_VERSION,
            true
        );
    }
}
