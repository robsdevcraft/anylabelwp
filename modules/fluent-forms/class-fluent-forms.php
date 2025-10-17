<?php
/**
 * Fluent Forms module class for AnylabelWP
 */
class AnylabelWP_Fluent_Forms
{
    /**
     * Constructor: Checks if Fluent Forms is active, then hooks into WP
     */
    public function __construct()
    {
        if ($this->is_fluent_forms_active()) {
            add_action('admin_head', [$this, 'enqueue_scripts'], 999);
            add_action('admin_menu', [$this, 'change_fluent_forms_menu_name'], 999);
            add_action('admin_init', [$this, 'register_settings']);
            add_action('anylabelwp_render_forms_settings', [$this, 'render_settings']);
        }
    }

    /**
     * Check if Fluent Forms is active
     *
     * @return bool
     */
    private function is_fluent_forms_active()
    {
        return function_exists('is_plugin_active') && is_plugin_active('fluentform/fluentform.php');
    }

    /**
     * Enqueue scripts and styles for Fluent Forms
     */
    public function enqueue_scripts()
    {
        if ((isset($_GET['page']) && strpos($_GET['page'], 'fluent_forms') !== false)) {
        
            wp_enqueue_style(
                'anylabelwp-fluent-forms-style',
                ANYLABELWP_PLUGIN_URL . 'modules/fluent-forms/css/fluent-forms.css',
                [],
                ANYLABELWP_VERSION
            );
            wp_enqueue_script(
                'anylabelwp-fluent-forms-script',
                ANYLABELWP_PLUGIN_URL . 'modules/fluent-forms/js/fluent-forms.js',
                [],
                ANYLABELWP_VERSION,
                false // false so that it loads in header
            );

            wp_localize_script(
                'anylabelwp-fluent-forms-script',
                'anylabelwp',
                [
                    'new_url' => esc_url(get_option('anylabelwp_fluent_forms_logo_url')),
                    'default_url' => esc_url(ANYLABELWP_PLUGIN_URL . 'assets/images/defaults/anylabel-fluentform-default.svg'),
                ]
            );
        }
    }

    /**
     * Rename Fluent Forms menu item to "Forms" with a dashicon
     */
    public function change_fluent_forms_menu_name()
    {
        global $menu;
        foreach ($menu as $key => $item) {
            if ($item[0] === 'Fluent Forms') {
                $menu[$key][0] = esc_html__('Forms', 'anylabelwp-plugin');
                $menu[$key][6] = 'dashicons-text-page';
                break;
            }
        }
    }

    /**
     * Register settings for Fluent Forms
     */
    public function register_settings()
    {
        if (isset($_POST['anylabelwp_fluent_forms_nonce']) &&
            wp_verify_nonce($_POST['anylabelwp_fluent_forms_nonce'], 'anylabelwp_fluent_forms_settings')
        ) {
            // Process any posted settings if needed
        }

        register_setting(
            'anylabelwp_forms',
            'anylabelwp_fluent_forms_logo_url',
            [
                'sanitize_callback' => 'esc_url_raw',
                'default'           => '',
            ]
        );
    }

    /**
     * Render the Fluent Forms tab content in admin-settings.php
     */
    public function render_settings()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'anylabelwp-plugin'));
        }

        wp_nonce_field('anylabelwp_fluent_forms_settings', 'anylabelwp_fluent_forms_nonce');
        
    $current_logo = get_option('anylabelwp_fluent_forms_logo_url', '');
    $default_logo = ANYLABELWP_PLUGIN_URL . 'assets/images/defaults/anylabel-fluentform-default.svg';
        ?>
        <div class="tab-content forms-settings">
            <h2><?php esc_html_e('Fluent Forms Settings', 'anylabelwp-plugin'); ?></h2>
            
            <!-- Default Logo Preview -->
            <h3><?php _e('Default Logo', 'anylabelwp-plugin'); ?></h3>
            <div style="margin: 10px 0;">
                <img src="<?php echo esc_url($default_logo); ?>" 
                     alt="<?php _e('Default Forms Logo', 'anylabelwp-plugin'); ?>" 
                     style="max-height: 40px; border: 1px solid #ddd; padding: 5px;" />
                <p class="description"><?php _e('This is the default logo that will be used if no custom logo is set.', 'anylabelwp-plugin'); ?></p>
            </div>

            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php esc_html_e('Custom Fluent Forms Logo URL', 'anylabelwp-plugin'); ?></th>
                    <td>
                        <div 
                            class="anylabelwp-logo-control<?php echo !empty($current_logo) ? ' anylabelwp-logo-active' : ''; ?>"
                            data-default-url="<?php echo esc_attr($default_logo); ?>"
                            data-media-title="<?php echo esc_attr__('Select a Forms Logo', 'anylabelwp-plugin'); ?>"
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
                                       name="anylabelwp_fluent_forms_logo_url" 
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
                                    <button type="button" class="anylabelwp-logo-reset">
                                        <?php esc_html_e('Cancel', 'anylabelwp-plugin'); ?>
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
