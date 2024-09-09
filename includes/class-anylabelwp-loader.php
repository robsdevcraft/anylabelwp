<?php
namespace AnylabelWP;

class Loader {
    public function run() {
        add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
        
        $this->load_modules();
    }

    private function load_modules() {
        $modules = array(
            'fluent-smtp' => 'Fluent_SMTP',
            'fluent-forms' => 'Fluent_Forms',
            // Add other modules here
        );

        foreach ( $modules as $module => $class_name ) {
            require_once ANYLABELWP_PLUGIN_DIR . "modules/{$module}/class-{$module}.php";
            $module_class = "AnylabelWP_{$class_name}";
            new $module_class();
        }
    }

    public function add_plugin_admin_menu() {
        add_options_page(
            'AnylabelWP Settings',
            'AnylabelWP',
            'manage_options', // This ensures only users with 'manage_options' capability can access
            'anylabelwp-settings',
            array( $this, 'display_plugin_admin_page' )
        );
    }

    public function display_plugin_admin_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'anylabelwp-plugin'));
        }

        if (isset($_POST['action']) && $_POST['action'] == 'update') {
            if (!isset($_POST['anylabelwp_nonce']) || !wp_verify_nonce($_POST['anylabelwp_nonce'], 'anylabelwp_settings')) {
                wp_die(__('Security check failed', 'anylabelwp-plugin'));
            }
            
            // Process form data here with proper sanitization and validation
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

    private function validate_some_option($option) {
        // Add your validation logic here
        return true; // or false if validation fails
    }

    public function enqueue_admin_styles() {
        wp_enqueue_style(
            'anylabelwp-admin-styles',
            ANYLABELWP_PLUGIN_URL . 'assets/css/admin.css',
            array(), // dependencies
            ANYLABELWP_VERSION // version
        );
    }

    public function enqueue_admin_scripts() {
        wp_enqueue_script(
            'anylabelwp-admin-script',
            ANYLABELWP_PLUGIN_URL . 'assets/js/admin.js',
            array(), // dependencies
            ANYLABELWP_VERSION, // version
            true // in footer
        );
    }
}
