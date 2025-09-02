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
        } else {
            add_action('admin_notices', [$this, 'fluent_crm_not_active_notice']);
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
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }
        return true;
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
        ?>
        <div class="tab-content crm-settings">
            <h2><?php esc_html_e('Fluent CRM Settings', 'anylabelwp-plugin'); ?></h2>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php esc_html_e('Custom Fluent CRM Logo', 'anylabelwp-plugin'); ?></th>
                    <td>
                        <?php 
                        \AnylabelWP\Loader::render_logo_selector(
                            'anylabelwp_fluent_crm_logo_url',
                            get_option('anylabelwp_fluent_crm_logo_url'),
                            'crm'
                        ); 
                        ?>
                    </td>
                </tr>
            </table>
        </div>
        <?php
    }

    /**
    * Admin notice if Fluent CRM is not active
    */
    public function fluent_crm_not_active_notice()
    {
        ?>
        <div class="notice notice-warning is-dismissible">
            <p><?php echo esc_html__('Fluent CRM is not active. AnylabelWP customizations for Fluent CRM will not be applied.', 'anylabelwp-plugin'); ?></p>
        </div>
        <?php
    }
}
