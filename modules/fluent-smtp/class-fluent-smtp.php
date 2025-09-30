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
                    'default_url' => esc_url(ANYLABELWP_PLUGIN_URL . 'assets/images/defaults/anylabel-smtp-default.png'),
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
        
    $current_logo = get_option('anylabelwp_fluent_smtp_logo_url', '');
    $default_logo = ANYLABELWP_PLUGIN_URL . 'assets/images/defaults/anylabel-smtp-default.png';
        ?>
        <div class="tab-content smtp-settings">
            <h2><?php esc_html_e('Fluent SMTP Settings', 'anylabelwp-plugin'); ?></h2>
            
            <!-- Default Logo Preview -->
            <h3><?php _e('Default Logo', 'anylabelwp-plugin'); ?></h3>
            <div style="margin: 10px 0;">
                <img src="<?php echo esc_url($default_logo); ?>" 
                     alt="<?php _e('Default SMTP Logo', 'anylabelwp-plugin'); ?>" 
                     style="max-height: 40px; border: 1px solid #ddd; padding: 5px;" />
                <p class="description"><?php _e('This is the default logo that will be used if no custom logo is set.', 'anylabelwp-plugin'); ?></p>
            </div>

            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php esc_html_e('Custom Fluent SMTP Logo URL', 'anylabelwp-plugin'); ?></th>
                    <td>
                        <div 
                            class="anylabelwp-logo-control<?php echo !empty($current_logo) ? ' anylabelwp-logo-active' : ''; ?>"
                            data-default-url="<?php echo esc_attr($default_logo); ?>"
                            data-media-title="<?php echo esc_attr__('Select an SMTP Logo', 'anylabelwp-plugin'); ?>"
                            data-media-button="<?php echo esc_attr__('Use this logo', 'anylabelwp-plugin'); ?>"
                        >
                            <label class="anylabelwp-logo-toggle">
                                <input type="checkbox"
                                       class="anylabelwp-logo-toggle-checkbox"
                                       <?php checked(!empty($current_logo)); ?> />
                                <?php esc_html_e('Use custom logo?', 'anylabelwp-plugin'); ?>
                            </label>
                            <div class="anylabelwp-logo-custom-fields">
                                <input type="url" 
                                       name="anylabelwp_fluent_smtp_logo_url" 
                                       value="<?php echo esc_attr($current_logo); ?>" 
                                       class="regular-text anylabelwp-logo-field" 
                                       placeholder="<?php _e('Enter custom logo URL or choose from the media library', 'anylabelwp-plugin'); ?>" />
                                <p class="description">
                                    <?php _e('Pick an image from the media library or paste a logo URL. Leave blank to keep the default logo.', 'anylabelwp-plugin'); ?>
                                </p>
                                <div class="anylabelwp-logo-actions">
                                    <button type="button" class="button button-secondary anylabelwp-logo-media">
                                        <?php esc_html_e('Choose from Media Library', 'anylabelwp-plugin'); ?>
                                    </button>
                                    <button type="button" class="button button-link anylabelwp-logo-reset">
                                        <?php esc_html_e('Use Default Logo', 'anylabelwp-plugin'); ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        <?php
    }
}
