<?php
namespace AnylabelWP;

/**
 * Main Loader class for AnylabelWP plugin
 */
class Loader
{
    /**
     * Run all plugin setup
     */
    public function run()
    {
        // Load translations
        add_action('init', [$this, 'load_textdomain']);
        
        add_action('admin_menu', [$this, 'add_plugin_admin_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_styles']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);

        // Add plugin action links
        add_filter('plugin_action_links_' . plugin_basename(ANYLABELWP_PLUGIN_DIR . 'anylabel.php'), [$this, 'add_plugin_action_links']);
        
        // Add plugin meta links
        add_filter('plugin_row_meta', [$this, 'add_plugin_meta_links'], 10, 2);
        
        // Admin notices
        add_action('admin_notices', [$this, 'admin_notices']);

        $this->load_modules();
    }

    /**
     * Add action links to plugin list page
     */
    public function add_plugin_action_links($links)
    {
        $settings_link = sprintf(
            '<a href="%s">%s</a>',
            admin_url('options-general.php?page=anylabelwp-settings'),
            __('Settings', 'anylabelwp-plugin')
        );
        
        array_unshift($links, $settings_link);
        return $links;
    }

    /**
     * Add meta links to plugin list page
     */
    public function add_plugin_meta_links($links, $file)
    {
        if ($file === plugin_basename(ANYLABELWP_PLUGIN_DIR . 'anylabel.php')) {
            $links[] = sprintf(
                '<a href="%s" target="_blank">%s</a>',
                'https://github.com/wpoperator/anylabelwp',
                __('GitHub', 'anylabelwp-plugin')
            );
            $links[] = sprintf(
                '<a href="%s" target="_blank">%s</a>',
                'https://github.com/wpoperator/anylabelwp/issues',
                __('Support', 'anylabelwp-plugin')
            );
            $links[] = sprintf(
                '<a href="%s" target="_blank">%s</a>',
                'https://github.com/wpoperator/anylabelwp#readme',
                __('Documentation', 'anylabelwp-plugin')
            );
        }
        return $links;
    }

    /**
     * Show admin notices
     */
    public function admin_notices()
    {
        // Check if this is first activation
        if (get_transient('anylabelwp_activation_notice')) {
            delete_transient('anylabelwp_activation_notice');
            
            printf(
                '<div class="notice notice-success is-dismissible"><p>%s <a href="%s">%s</a></p></div>',
                __('AnylabelWP activated successfully!', 'anylabelwp-plugin'),
                admin_url('options-general.php?page=anylabelwp-settings'),
                __('Configure Settings', 'anylabelwp-plugin')
            );
        }
        
        // Check for supported plugins
        $this->check_supported_plugins();
    }

    /**
     * Check if supported plugins are installed
     */
    private function check_supported_plugins()
    {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        $supported_plugins = [
            'fluent-smtp/fluent-smtp.php' => 'FluentSMTP',
            'fluentform/fluentform.php' => 'Fluent Forms',
            'fluent-crm/fluent-crm.php' => 'FluentCRM',
            'wp-social-ninja/wp-social-ninja.php' => 'WP Social Ninja'
        ];
        
        $inactive_plugins = [];
        foreach ($supported_plugins as $plugin_file => $plugin_name) {
            if (!is_plugin_active($plugin_file)) {
                $inactive_plugins[] = $plugin_name;
            }
        }
        
        if (!empty($inactive_plugins) && !get_user_meta(get_current_user_id(), 'anylabelwp_hide_plugin_notice', true)) {
            printf(
                '<div class="notice notice-info is-dismissible" data-dismissible="anylabelwp-plugin-notice"><p>%s <strong>%s</strong>. %s</p></div>',
                __('AnylabelWP can white-label these plugins:', 'anylabelwp-plugin'),
                implode(', ', $inactive_plugins),
                __('Install and activate them to use AnylabelWP features.', 'anylabelwp-plugin')
            );
        }
    }

    /**
     * Load plugin text domain for translations
     */
    public function load_textdomain()
    {
        load_plugin_textdomain(
            'anylabelwp-plugin',
            false,
            dirname(plugin_basename(ANYLABELWP_PLUGIN_DIR)) . '/languages/'
        );
    }

    /**
     * Load and instantiate each active module class
     */
    private function load_modules()
    {
        $modules = [
            'fluent-smtp'  => 'Fluent_SMTP',
            'fluent-forms' => 'Fluent_Forms',
            'fluent-crm' => 'Fluent_CRM',
            'wp-social-ninja' => 'WP_Social_Ninja'
        ];

        foreach ($modules as $module => $class_name) {
            $module_file = ANYLABELWP_PLUGIN_DIR . "modules/{$module}/class-{$module}.php";
            if (file_exists($module_file)) {
                require_once $module_file;
                $module_class = "AnylabelWP_{$class_name}";
                if (class_exists($module_class)) {
                    new $module_class();
                }
            }
        }

        // Load General Settings if it exists
        $general_file = ANYLABELWP_PLUGIN_DIR . 'modules/general/class-general-settings.php';
        if (file_exists($general_file)) {
            require_once $general_file;
            if (class_exists('AnylabelWP\AnylabelWP_General_Settings')) {
                new \AnylabelWP\AnylabelWP_General_Settings();
            }
        }
    }

    /**
     * Add AnylabelWP settings page to the WordPress Settings menu
     */
    public function add_plugin_admin_menu()
    {
        add_options_page(
            __('AnylabelWP Settings', 'anylabelwp-plugin'),
            __('AnylabelWP', 'anylabelwp-plugin'),
            'manage_options',
            'anylabelwp-settings',
            [$this, 'display_plugin_admin_page']
        );
    }

    /**
     * Display the admin settings page
     */
    public function display_plugin_admin_page()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'anylabelwp-plugin'));
        }

        require_once ANYLABELWP_PLUGIN_DIR . 'views/admin-settings.php';
    }

    /**
     * Enqueue plugin admin CSS
     */
    public function enqueue_admin_styles($hook)
    {
        if ($hook !== 'settings_page_anylabelwp-settings') {
            return;
        }
        
        wp_enqueue_style(
            'anylabelwp-admin-styles',
            ANYLABELWP_PLUGIN_URL . 'assets/css/admin.css',
            [],
            ANYLABELWP_VERSION
        );
    }

    /**
     * Enqueue plugin admin JavaScript
     */
    public function enqueue_admin_scripts($hook)
    {
        if ($hook !== 'settings_page_anylabelwp-settings') {
            return;
        }
        
        wp_enqueue_script(
            'anylabelwp-admin-script',
            ANYLABELWP_PLUGIN_URL . 'assets/js/admin.js',
            ['jquery'],
            ANYLABELWP_VERSION,
            true
        );
    }
}
