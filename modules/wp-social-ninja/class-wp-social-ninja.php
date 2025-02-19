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


}
