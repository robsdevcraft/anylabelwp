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
            add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
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
        // Add any needed scripts/styles for the Fluent Forms pages
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
     * Register settings for Fluent Forms if necessary
     */
    public function register_settings()
    {
        // Register any relevant options here
    }

    /**
     * Render the Fluent Forms tab content in admin-settings.php
     */
    public function render_settings()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'anylabelwp-plugin'));
        }
        ?>
        <div class="tab-content forms-settings">
            <h2><?php echo esc_html__('Fluent Forms Settings', 'anylabelwp-plugin'); ?></h2>
            <p><?php echo esc_html__('Fluent Forms specific settings or options can go here.', 'anylabelwp-plugin'); ?></p>
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
