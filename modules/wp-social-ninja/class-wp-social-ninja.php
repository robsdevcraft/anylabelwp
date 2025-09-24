<?php
/**
 * WP Social Ninja module class for AnylabelWP
 */
class AnylabelWP_WP_Social_Ninja
{
    /**
     * Constructor: Checks if WP Social Ninja is active, then hooks into WP
     */
    public function __construct()
    {
        if ($this->is_wp_social_ninja_active()) {
            add_action('admin_head', [$this, 'enqueue_scripts'], 999);
            add_action('admin_menu', [$this, 'change_wp_social_ninja_menu_name'], 999);
            add_action('admin_init', [$this, 'register_settings']);
            add_action('anylabelwp_render_wp_social_ninja_settings', [$this, 'render_settings']);
        } else {
            add_action('admin_notices', [$this, 'wp_social_ninja_not_active_notice']);
        }
    }

    /**
     * Check if WP Social Ninja is active
     *
     * @return bool
     */
    private function is_wp_social_ninja_active()
    {
        return function_exists('is_plugin_active') && is_plugin_active('wp-social-reviews/wp-social-reviews.php');
    }

    /**
     * Enqueue scripts and styles for WP Social Ninja
     */
    public function enqueue_scripts()
    {
        if (isset($_GET['page']) && $_GET['page'] === 'wpsocialninja.php') {
        
            wp_enqueue_style(
                'anylabelwp-wp-social-ninja-style',
                ANYLABELWP_PLUGIN_URL . 'modules/wp-social-ninja/css/wp-social-ninja.css',
                [],
                ANYLABELWP_VERSION
            );
            wp_enqueue_script(
                'anylabelwp-wp-social-ninja-script',
                ANYLABELWP_PLUGIN_URL . 'modules/wp-social-ninja/js/wp-social-ninja.js',
                [],
                ANYLABELWP_VERSION,
                false // false so that it loads in header
            );

            wp_localize_script(
                'anylabelwp-wp-social-ninja-script',
                'anylabelwp',
                [
                    'new_url' => esc_url(get_option('anylabelwp_wp_social_ninja_logo_url')),
                    'default_url' => esc_url(ANYLABELWP_PLUGIN_URL . 'assets/images/defaults/anylabel-social-default.png'),
                ]
            );
        }
    }

    /**
     * Rename WP Social Ninja menu item to "Social Media" with a dashicon
     */
    public function change_wp_social_ninja_menu_name()
    {
        global $menu;
        foreach ($menu as $key => $item) {
            if ($item[0] === 'WP Social Ninja') {
                $menu[$key][0] = esc_html__('Social Media', 'anylabelwp-plugin');
                $menu[$key][6] = 'dashicons-share';
                break;
            }
        }
    }

    /**
     * Register settings for WP Social Ninja
     */
    public function register_settings()
    {
        if (isset($_POST['anylabelwp_wp_social_ninja_nonce']) &&
            wp_verify_nonce($_POST['anylabelwp_wp_social_ninja_nonce'], 'anylabelwp_wp_social_ninja_settings')
        ) {
            // Process any posted settings if needed
        }

        register_setting(
            'anylabelwp_social_media',
            'anylabelwp_wp_social_ninja_logo_url',
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
     * Render the WP Social Ninja tab content in admin-settings.php
     */
    public function render_settings()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'anylabelwp-plugin'));
        }

        $current_logo_url = get_option('anylabelwp_wp_social_ninja_logo_url');
        $default_logo_url = ANYLABELWP_PLUGIN_URL . 'assets/images/defaults/anylabel-social-default.png';

        wp_nonce_field('anylabelwp_wp_social_ninja_settings', 'anylabelwp_wp_social_ninja_nonce');
        ?>
        <div class="tab-content wp-social-ninja-settings">
            <h2><?php esc_html_e('WP Social Ninja Settings', 'anylabelwp-plugin'); ?></h2>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php esc_html_e('Custom WP Social Ninja Logo', 'anylabelwp-plugin'); ?></th>
                    <td>
                        <input 
                            type="url" 
                            name="anylabelwp_wp_social_ninja_logo_url" 
                            value="<?php echo esc_attr($current_logo_url); ?>" 
                            placeholder="<?php esc_attr_e('Enter logo URL (leave blank for default)', 'anylabelwp-plugin'); ?>"
                            style="width: 400px;"
                        />
                        <div style="margin-top: 10px;">
                            <p><strong><?php esc_html_e('Default Logo:', 'anylabelwp-plugin'); ?></strong></p>
                            <img src="<?php echo esc_url($default_logo_url); ?>" alt="Default Social Logo" style="max-width: 200px; height: auto; border: 1px solid #ddd; padding: 5px;" />
                        </div>
                        <?php if (!empty($current_logo_url)) : ?>
                        <div style="margin-top: 10px;">
                            <p><strong><?php esc_html_e('Current Logo:', 'anylabelwp-plugin'); ?></strong></p>
                            <img src="<?php echo esc_url($current_logo_url); ?>" alt="Current Social Logo" style="max-width: 200px; height: auto; border: 1px solid #ddd; padding: 5px;" />
                        </div>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        </div>
        <?php
    }

    /**
    * Admin notice if WP Social Ninja is not active
    */
    public function wp_social_ninja_not_active_notice()
    {
        ?>
        <div class="notice notice-warning is-dismissible">
            <p><?php echo esc_html__('WP Social Ninja is not active. AnylabelWP customizations for WP Social Ninja will not be applied.', 'anylabelwp-plugin'); ?></p>
        </div>
        <?php
    }

}
