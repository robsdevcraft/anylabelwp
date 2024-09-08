<?php
class AnylabelWP_Loader {
    public function run() {
        add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
        
        $this->load_modules();
    }

    private function load_modules() {
        $modules = array(
            'fluent-smtp' => 'Fluent_SMTP',
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
            'manage_options',
            'anylabelwp-settings',
            array( $this, 'display_plugin_admin_page' )
        );
    }

    public function display_plugin_admin_page() {
        require_once ANYLABELWP_PLUGIN_DIR . 'views/admin-settings.php';
    }

    public function enqueue_admin_styles() {
        wp_enqueue_style( 'anylabelwp-admin-styles', ANYLABELWP_PLUGIN_URL . 'assets/css/admin.css', array(), ANYLABELWP_VERSION );
    }

    public function enqueue_admin_scripts() {
        wp_enqueue_script( 'anylabelwp-admin-script', ANYLABELWP_PLUGIN_URL . 'assets/js/admin.js', array( 'jquery' ), ANYLABELWP_VERSION, true );
    }
}
