<?php
class AnylabelWP_Fluent_SMTP {
    public function __construct() {
        if ($this->is_fluent_smtp_active()) {
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
            add_action( 'admin_menu', array( $this, 'change_fluent_smtp_menu_name' ), 999 );
            add_action( 'admin_init', array( $this, 'register_settings' ) );
            add_action( 'anylabelwp_render_module_settings', array( $this, 'render_settings' ) );
        } else {
            add_action('admin_notices', array($this, 'fluent_smtp_not_active_notice'));
        }
    }

    private function is_fluent_smtp_active() {
        return is_plugin_active('fluent-smtp/fluent-smtp.php');
    }

    public function enqueue_scripts() {
        if ( isset( $_GET['page'] ) && $_GET['page'] == 'fluent-mail' ) {
            wp_enqueue_style( 'anylabelwp-fluent-smtp-style', ANYLABELWP_PLUGIN_URL . 'modules/fluent-smtp/css/fluent-smtp.css', array(), ANYLABELWP_VERSION );
            wp_enqueue_script( 'anylabelwp-fluent-smtp-script', ANYLABELWP_PLUGIN_URL . 'modules/fluent-smtp/js/fluent-smtp.js', array( 'jquery' ), ANYLABELWP_VERSION, true );
            wp_localize_script( 'anylabelwp-fluent-smtp-script', 'anylabelwp', array(
                'new_url' => esc_url( get_option( 'anylabelwp_fluent_smtp_logo_url' ) ),
            ) );
        }
    }

    public function change_fluent_smtp_menu_name() {
        global $submenu;
        if ( isset( $submenu['options-general.php'] ) ) {
            foreach ( $submenu['options-general.php'] as $key => $value ) {
                if ( 'FluentSMTP' == $value[0] ) {
                    $submenu['options-general.php'][$key][0] = 'SMTP';
                    break;
                }
            }
        }
    }

    public function register_settings() {
        register_setting( 'anylabelwp_settings', 'anylabelwp_fluent_smtp_logo_url', 'sanitize_text_field' );
        // Register any other Fluent SMTP specific settings here
    }

    public function render_settings() {
        ?>
        <h2>Fluent SMTP Settings</h2>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">Fluent SMTP Logo URL</th>
                <td>
                    <input type="text" name="anylabelwp_fluent_smtp_logo_url" value="<?php echo esc_attr( get_option( 'anylabelwp_fluent_smtp_logo_url' ) ); ?>" />
                </td>
            </tr>
            <!-- Add more Fluent SMTP specific settings here -->
        </table>
        <?php
    }

    public function fluent_smtp_not_active_notice() {
        ?>
        <div class="notice notice-warning is-dismissible">
            <p><?php _e('Fluent SMTP is not active. AnylabelWP customizations for Fluent SMTP will not be applied.', 'anylabelwp'); ?></p>
        </div>
        <?php
    }
}
