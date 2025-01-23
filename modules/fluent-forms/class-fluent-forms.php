<?php
class AnylabelWP_Fluent_Forms {
    public function __construct() {
        if ($this->is_fluent_forms_active()) {
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
            add_action( 'admin_menu', array( $this, 'change_fluent_forms_menu_name' ), 999 );
            add_action( 'admin_init', array( $this, 'register_settings' ) );
            add_action( 'anylabelwp_render_module_settings', array( $this, 'render_settings' ) );
        } else {
            add_action('admin_notices', array($this, 'fluent_forms_not_active_notice'));
        }
    }

    private function is_fluent_forms_active() {
        return is_plugin_active('fluentform/fluentform.php');
    }

    public function enqueue_scripts() {
        // Enqueue scripts and styles for Fluent Forms
    }

    // Changes the name from Fluent Forms to Forms and adds new generic dashicon
    public function change_fluent_forms_menu_name() {
        global $menu;
        foreach ($menu as $key => $item) {
            if ($item[0] === 'Fluent Forms') {
                $menu[$key][0] = esc_html__('Forms', 'anylabelwp-plugin');
                $menu[$key][6] = 'dashicons-text-page';
                break;
            }
        }
    }

    public function register_settings() {
        // Register settings for Fluent Forms
    }

    public function render_settings() {
        // Render settings for Fluent Forms
    }

    public function fluent_forms_not_active_notice() {
        // Display notice if Fluent Forms is not active
    }
}
