<?php
/**
 * Fluent SMTP module class for AnylabelWP
 */
class AnylabelWP_Fluent_SMTP
{
    /**
     * Constructor: Checks if Fluent SMTP is active, then hooks into WP
     */
    public function __construct()
    {
        if ($this->is_fluent_smtp_active()) {
            add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
            add_action('admin_menu', [$this, 'change_fluent_smtp_menu_name'], 999);
            add_action('admin_init', [$this, 'register_settings']);
            add_action('anylabelwp_render_smtp_settings', [$this, 'render_settings']);
        } else {
            add_action('admin_notices', [$this, 'fluent_smtp_not_active_notice']);
        }
    }

    /**
     * Check if Fluent SMTP plugin is active
     *
     * @return bool
     */
    private function is_fluent_smtp_active()
    {
        return function_exists('is_plugin_active') && is_plugin_active('fluent-smtp/fluent-smtp.php');
    }

    /**
     * Enqueue scripts and styles for Fluent SMTP pages
     */
    public function enqueue_scripts()
    {
        $page = isset($_GET['page']) ? sanitize_key($_GET['page']) : '';
        if ($page === 'fluent-mail') {
            wp_enqueue_style(
                'anylabelwp-fluent-smtp-style',
                ANYLABELWP_PLUGIN_URL . 'modules/fluent-smtp/css/fluent-smtp.css',
                [],
                ANYLABELWP_VERSION
            );
            wp_enqueue_script(
                'anylabelwp-fluent-smtp-script',
                ANYLABELWP_PLUGIN_URL . 'modules/fluent-smtp/js/fluent-smtp.js',
                [],
                ANYLABELWP_VERSION,
                true
            );
            wp_localize_script(
                'anylabelwp-fluent-smtp-script',
                'anylabelwp',
                [
                    'new_url' => esc_url(get_option('anylabelwp_fluent_smtp_logo_url')),
                ]
            );
        }
    }

    /**
     * Rename Fluent SMTP menu item under Settings > SMTP
     *
     * @return void
     */
    public function change_fluent_smtp_menu_name()
    {
        global $submenu;
        if (isset($submenu['options-general.php'])) {
            foreach ($submenu['options-general.php'] as $key => $value) {
                if ('FluentSMTP' === $value[0]) {
                    $submenu['options-general.php'][$key][0] = esc_html__('SMTP', 'anylabelwp-plugin');
                    break;
                }
            }
        }
    }

    /**
     * Register Fluent SMTP-related options
     */
    public function register_settings()
    {
        // Example nonce check for posted settings
        if (isset($_POST['anylabelwp_fluent_smtp_nonce']) && wp_verify_nonce($_POST['anylabelwp_fluent_smtp_nonce'], 'anylabelwp_fluent_smtp_settings')) {
            // Process posted settings here if needed
        }

        register_setting(
            'anylabelwp_smtp',
            'anylabelwp_fluent_smtp_logo_url',
            [
                'sanitize_callback' => 'esc_url_raw',
                'default'           => '',
                'validate_callback' => [$this, 'validate_logo_url'],
            ]
        );
    }

    /**
     * Validate logo URL setting
     *
     * @param string $url
     * @return bool
     */
    public function validate_logo_url($url)
    {
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            return false;
        }
        return true;
    }

    /**
     * Render the Fluent SMTP tab content in admin-settings.php
     */
    public function render_settings()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'anylabelwp-plugin'));
        }

        wp_nonce_field('anylabelwp_fluent_smtp_settings', 'anylabelwp_fluent_smtp_nonce');
        ?>
        <div class="tab-content smtp-settings">
            <h2><?php esc_html_e('Fluent SMTP Settings', 'anylabelwp-plugin'); ?></h2>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php esc_html_e('Custom Fluent SMTP Logo URL', 'anylabelwp-plugin'); ?></th>
                    <td>
                        <input type="text" name="anylabelwp_fluent_smtp_logo_url" value="<?php echo esc_attr(get_option('anylabelwp_fluent_smtp_logo_url')); ?>" />
                    </td>
                </tr>
            </table>
        </div>
        <?php
    }

    /**
     * Admin notice if Fluent SMTP is not active
     */
    public function fluent_smtp_not_active_notice()
    {
        ?>
        <div class="notice notice-warning is-dismissible">
            <p><?php echo esc_html__('Fluent SMTP is not active. AnylabelWP customizations for Fluent SMTP will not be applied.', 'anylabelwp-plugin'); ?></p>
        </div>
        <?php
    }
}
