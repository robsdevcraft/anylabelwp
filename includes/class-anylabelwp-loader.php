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
        
        /**
         * Notifcation
         */
        // if (!empty($inactive_plugins) && !get_user_meta(get_current_user_id(), 'anylabelwp_hide_plugin_notice', true)) {
        //     printf(
        //         '<div class="notice notice-info is-dismissible" data-dismissible="anylabelwp-plugin-notice"><p>%s <strong>%s</strong>. %s</p></div>',
        //         __('AnylabelWP can white-label these plugins:', 'anylabelwp-plugin'),
        //         implode(', ', $inactive_plugins),
        //         __('Install and activate them to use AnylabelWP features.', 'anylabelwp-plugin')
        //     );
        // }
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
     * Enqueue plugin admin CSS - loads on ALL admin pages for global modifications
     */
    public function enqueue_admin_styles($hook)
    {
        // Load global admin styles on ALL admin pages
        wp_enqueue_style(
            'anylabelwp-admin-styles',
            ANYLABELWP_PLUGIN_URL . 'assets/css/admin.css',
            [],
            ANYLABELWP_VERSION
        );
    }

    /**
     * Enqueue plugin admin JavaScript - only on settings page
     * Pure vanilla JavaScript implementation without jQuery dependencies
     */
    public function enqueue_admin_scripts($hook)
    {
        // Only load JavaScript on our plugin's settings page
        if ($hook !== 'settings_page_anylabelwp-settings') {
            return;
        }
        
        // Security check
        if (!current_user_can('manage_options')) {
            return;
        }
        
        // Enqueue WordPress media uploader (required for media library integration)
        wp_enqueue_media();
        
        // Enqueue admin JavaScript (vanilla JS, no jQuery dependency)
        wp_enqueue_script(
            'anylabelwp-admin-script',
            ANYLABELWP_PLUGIN_URL . 'assets/js/admin.js',
            [], // No dependencies - pure vanilla JavaScript
            ANYLABELWP_VERSION,
            true
        );
        
        // Enqueue logo selector JavaScript (vanilla JS with media library support)
        wp_enqueue_script(
            'anylabelwp-logo-selector',
            ANYLABELWP_PLUGIN_URL . 'assets/js/logo-selector.js',
            ['media-upload', 'media-views'], // Only WordPress media dependencies, no jQuery
            ANYLABELWP_VERSION,
            true
        );
        
        // Localize script with default images data and security nonce
        wp_localize_script(
            'anylabelwp-logo-selector',
            'anylabelwp_admin',
            [
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('anylabelwp_logo_nonce'),
                'default_images' => $this->get_default_images(),
                'media_title' => __('Choose Logo Image', 'anylabelwp-plugin'),
                'media_button' => __('Use This Image', 'anylabelwp-plugin'),
            ]
        );
    }

    /**
     * Get available default images
     * Updated to use custom PNG media files
     * 
     * @return array Array of default images with metadata
     */
    public function get_default_images()
    {
        $default_images = [];
        $defaults_dir = ANYLABELWP_PLUGIN_DIR . 'assets/images/defaults/';
        $defaults_url = ANYLABELWP_PLUGIN_URL . 'assets/images/defaults/';
        
        // Updated image files array to match your custom media files
        $image_files = [
            'anylabel-forms-default.png' => [
                'name' => __('AnylabelWP Forms Logo', 'anylabelwp-plugin'),
                'description' => __('Default logo for Fluent Forms white-labeling', 'anylabelwp-plugin'),
                'category' => 'forms'
            ],
            'anylabel-crm-default.png' => [
                'name' => __('AnylabelWP CRM Logo', 'anylabelwp-plugin'),
                'description' => __('Default logo for Fluent CRM white-labeling', 'anylabelwp-plugin'),
                'category' => 'crm'
            ],
            'anylabel-smtp-default.png' => [
                'name' => __('AnylabelWP SMTP Logo', 'anylabelwp-plugin'),
                'description' => __('Default logo for Fluent SMTP white-labeling', 'anylabelwp-plugin'),
                'category' => 'email'
            ],
            'anylabel-social-default.png' => [
                'name' => __('AnylabelWP Social Logo', 'anylabelwp-plugin'),
                'description' => __('Default logo for WP Social Ninja white-labeling', 'anylabelwp-plugin'),
                'category' => 'social'
            ],
        ];
        
        foreach ($image_files as $filename => $data) {
            if (file_exists($defaults_dir . $filename)) {
                $default_images[] = [
                    'filename' => $filename,
                    'url' => $defaults_url . $filename,
                    'path' => $defaults_dir . $filename,
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'category' => $data['category']
                ];
            }
        }
        
        return $default_images;
    }

    /**
     * Render logo selector component with default logo and custom option toggle
     * 
     * @param string $field_name The name attribute for the input field
     * @param string $current_value Current logo URL value
     * @param string $filter_category Category filter for default images ('forms', 'crm', 'email', 'social')
     */
    public static function render_logo_selector($field_name, $current_value = '', $filter_category = null)
    {
        // Security check
        if (!current_user_can('manage_options')) {
            return;
        }
        
        $loader = new self();
        $default_images = $loader->get_default_images();
        
        // Get the specific default image for this category
        $default_image = null;
        if ($filter_category) {
            foreach ($default_images as $image) {
                if ($image['category'] === $filter_category) {
                    $default_image = $image;
                    break;
                }
            }
        }
        
        // Determine if user is using custom logo (has a value that's not the default)
        $using_custom = false;
        $custom_checkbox_name = $field_name . '_use_custom';
        
        if (!empty($current_value)) {
            // Check if current value is NOT the default image URL
            if (!$default_image || $current_value !== $default_image['url']) {
                $using_custom = true;
            }
        }
        
        ?>
        <div class="anylabelwp-logo-selector" data-field-name="<?php echo esc_attr($field_name); ?>">
            <?php if ($default_image): ?>
            <!-- Default Logo Section -->
            <div class="default-logo-section">
                <div class="default-logo-preview">
                    <img src="<?php echo esc_url($default_image['url']); ?>" alt="<?php echo esc_attr($default_image['name']); ?>" style="max-height: 40px; height: auto;" />
                    <div class="default-logo-info">
                        <strong><?php echo esc_html($default_image['name']); ?></strong>
                        <p class="description"><?php echo esc_html($default_image['description']); ?></p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Custom Logo Toggle -->
            <div class="custom-logo-toggle">
                <label>
                    <input type="checkbox" 
                           class="use-custom-logo-checkbox" 
                           <?php checked($using_custom); ?> />
                    <?php _e('Use custom logo instead of default', 'anylabelwp-plugin'); ?>
                </label>
            </div>
            
            <!-- Hidden input for the actual logo URL -->
            <input type="hidden" 
                   name="<?php echo esc_attr($field_name); ?>" 
                   class="logo-url-input" 
                   value="<?php echo esc_attr($current_value); ?>" 
                   data-default-url="<?php echo $default_image ? esc_attr($default_image['url']) : ''; ?>" />
            
            <!-- Custom Logo Options (hidden by default) -->
            <div class="custom-logo-options" style="<?php echo $using_custom ? '' : 'display: none;'; ?>">
                <div class="custom-logo-input-section">
                    <label for="<?php echo esc_attr($field_name . '_custom'); ?>">
                        <?php _e('Custom Logo URL:', 'anylabelwp-plugin'); ?>
                    </label>
                    <input type="url" 
                           id="<?php echo esc_attr($field_name . '_custom'); ?>"
                           class="custom-logo-url-input" 
                           value="<?php echo $using_custom ? esc_attr($current_value) : ''; ?>" 
                           placeholder="<?php _e('Enter image URL or use media library', 'anylabelwp-plugin'); ?>" />
                    
                    <div class="button-group">
                        <button type="button" class="button media-upload-button">
                            <?php _e('Choose from Media Library', 'anylabelwp-plugin'); ?>
                        </button>
                        <button type="button" class="button clear-custom-logo-button">
                            <?php _e('Clear Custom Logo', 'anylabelwp-plugin'); ?>
                        </button>
                    </div>
                    
                    <!-- Custom Logo Preview -->
                    <div class="custom-logo-preview" <?php echo (!$using_custom || empty($current_value)) ? 'style="display:none;"' : ''; ?>>
                        <?php if ($using_custom && !empty($current_value)): ?>
                        <img src="<?php echo esc_url($current_value); ?>" alt="<?php _e('Custom Logo Preview', 'anylabelwp-plugin'); ?>" style="max-height: 40px; height: auto;" />
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
