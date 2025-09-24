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
        } else {
            add_action('admin_notices', [$this, 'fluent_forms_not_active_notice']);
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
        //Debug page
        //error_log('Current $_GET["page"] value: ' . (isset($_GET['page']) ? $_GET['page'] : 'Not set'));

        // Change 'fluent_forms' to the actual page slug used by Fluent Forms
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
                    'default_url' => esc_url(ANYLABELWP_PLUGIN_URL . 'assets/images/defaults/anylabel-forms-default.png'),
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
     * Validate logo URL setting
     *
     * @param string $url
     * @return bool
     */
    public function validate_logo_url($url)
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }
        return true;
    }

    /**
     * Render the Fluent Forms tab content in admin-settings.php
     */
    public function render_settings()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'anylabelwp-plugin'));
        }

        $current_logo_url = get_option('anylabelwp_fluent_forms_logo_url');
        $default_logo_url = ANYLABELWP_PLUGIN_URL . 'assets/images/defaults/anylabel-forms-default.png';

        wp_nonce_field('anylabelwp_fluent_forms_settings', 'anylabelwp_fluent_forms_nonce');
        ?>
        <div class="tab-content forms-settings">
            <h2><?php esc_html_e('Fluent Forms Settings', 'anylabelwp-plugin'); ?></h2>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php esc_html_e('Custom Fluent Forms Logo', 'anylabelwp-plugin'); ?></th>
                    <td>
                        <input 
                            type="url" 
                            name="anylabelwp_fluent_forms_logo_url" 
                            value="<?php echo esc_attr($current_logo_url); ?>" 
                            placeholder="<?php esc_attr_e('Enter logo URL (leave blank for default)', 'anylabelwp-plugin'); ?>"
                            style="width: 400px;"
                        />
                        <div style="margin-top: 10px;">
                            <p><strong><?php esc_html_e('Default Logo:', 'anylabelwp-plugin'); ?></strong></p>
                            <img src="<?php echo esc_url($default_logo_url); ?>" alt="Default Forms Logo" style="max-width: 200px; height: auto; border: 1px solid #ddd; padding: 5px;" />
                        </div>
                        <?php if (!empty($current_logo_url)) : ?>
                        <div style="margin-top: 10px;">
                            <p><strong><?php esc_html_e('Current Logo:', 'anylabelwp-plugin'); ?></strong></p>
                            <img src="<?php echo esc_url($current_logo_url); ?>" alt="Current Forms Logo" style="max-width: 200px; height: auto; border: 1px solid #ddd; padding: 5px;" />
                        </div>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        </div>
        <?php
    }

    /**
     * Admin notice if Fluent Forms is not active
     */
    public function fluent_forms_not_active_notice()
    {
        ?>
        <div class="notice notice-warning is-dismissible">
            <p><?php echo esc_html__('Fluent Forms is not active. AnylabelWP customizations for Fluent Forms will not be applied.', 'anylabelwp-plugin'); ?></p>
        </div>
        <?php
    }
}
