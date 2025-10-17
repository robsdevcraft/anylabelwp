<?php
/**
 * Fluent CRM module class for AnylabelWP
 */
class AnylabelWP_Fluent_CRM
{
    /**
     * Constructor: Checks if Fluent CRM is active, then hooks into WP
     */
    public function __construct()
    {
        if ($this->is_fluent_crm_active()) {
            add_action('admin_head', [$this, 'enqueue_scripts'], 999);
            add_action('admin_menu', [$this, 'change_fluent_crm_menu_name'], 999);
            add_action('admin_init', [$this, 'register_settings']);
            add_action('anylabelwp_render_crm_settings', [$this, 'render_settings']);
        }
    }

    /**
     * Check if Fluent CRM is active
     *
     * @return bool
     */
    private function is_fluent_crm_active()
    {
        return function_exists('is_plugin_active') && is_plugin_active('fluent-crm/fluent-crm.php');
    }

    /**
     * Enqueue scripts and styles for Fluent CRM
     */
    public function enqueue_scripts()
    {
        if (isset($_GET['page']) && $_GET['page'] === 'fluentcrm-admin') {
        
            wp_enqueue_style(
                'anylabelwp-fluent-crm-style',
                ANYLABELWP_PLUGIN_URL . 'modules/fluent-crm/css/fluent-crm.css',
                [],
                ANYLABELWP_VERSION
            );
            wp_enqueue_script(
                'anylabelwp-fluent-crm-script',
                ANYLABELWP_PLUGIN_URL . 'modules/fluent-crm/js/fluent-crm.js',
                [],
                ANYLABELWP_VERSION,
                false // false so that it loads in header
            );

            wp_localize_script(
                'anylabelwp-fluent-crm-script',
                'anylabelwp',
                [
                    'new_url' => esc_url(get_option('anylabelwp_fluent_crm_logo_url')),
                    'default_url' => esc_url(ANYLABELWP_PLUGIN_URL . 'assets/images/defaults/anylabel-fluentcrm-default.svg'),
                ]
            );
        }
    }

    /**
     * Rename Fluent CRM menu item to "CRM" with a dashicon
     */
    public function change_fluent_crm_menu_name()
    {
        global $menu;
        foreach ($menu as $key => $item) {
            if ($item[0] === 'FluentCRM') {
                $menu[$key][0] = esc_html__('CRM', 'anylabelwp-plugin');
                $menu[$key][6] = 'dashicons-groups';
                break;
            }
        }
    }

    /**
     * Register settings for Fluent CRM
     */
    public function register_settings()
    {
        if (isset($_POST['anylabelwp_fluent_crm_nonce']) &&
            wp_verify_nonce($_POST['anylabelwp_fluent_crm_nonce'], 'anylabelwp_fluent_crm_settings')
        ) {
            // Process any posted settings if needed
        }

        register_setting(
            'anylabelwp_crm',
            'anylabelwp_fluent_crm_logo_url',
            [
                'sanitize_callback' => 'esc_url_raw',
                'default'           => '',
            ]
        );
    }

    /**
     * Render the Fluent CRM tab content in admin-settings.php
     */
    public function render_settings()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'anylabelwp-plugin'));
        }

        wp_nonce_field('anylabelwp_fluent_crm_settings', 'anylabelwp_fluent_crm_nonce');
        
    $current_logo = get_option('anylabelwp_fluent_crm_logo_url', '');
    $default_logo = ANYLABELWP_PLUGIN_URL . 'assets/images/defaults/anylabel-fluentcrm-default.svg';
        ?>
        <div class="tab-content crm-settings">
            <h2><?php esc_html_e('Fluent CRM Settings', 'anylabelwp-plugin'); ?></h2>
            
            <!-- Default Logo Preview -->
            <h3><?php _e('Default Logo', 'anylabelwp-plugin'); ?></h3>
            <div style="margin: 10px 0;">
                <img src="<?php echo esc_url($default_logo); ?>" 
                     alt="<?php _e('Default CRM Logo', 'anylabelwp-plugin'); ?>" 
                     style="max-height: 40px; border: 1px solid #ddd; padding: 5px;" />
                <p class="description"><?php _e('This is the default logo that will be used if no custom logo is set.', 'anylabelwp-plugin'); ?></p>
            </div>

            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php esc_html_e('Custom Fluent CRM Logo URL', 'anylabelwp-plugin'); ?></th>
                    <td>
                        <div 
                            class="anylabelwp-logo-control<?php echo !empty($current_logo) ? ' anylabelwp-logo-active' : ''; ?>"
                            data-default-url="<?php echo esc_attr($default_logo); ?>"
                            data-media-title="<?php echo esc_attr__('Select a CRM Logo', 'anylabelwp-plugin'); ?>"
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
                                       name="anylabelwp_fluent_crm_logo_url" 
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
