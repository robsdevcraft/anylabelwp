<?php
/**
 * Plugin Name: AnylabelWP
 * Plugin URI: https://github.com/wpoperator/anylabelwp
 * Description: White Label 3rd Party Plugins for WordPress. Perfect for agencies and WAAS providers.
 * Version: 0.0.2
 * Author: WPOperator
 * Author URI: https://wpoperator.com
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: anylabelwp-plugin
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.3
 * Requires PHP: 7.4
 * Network: false
 * Update URI: https://github.com/wpoperator/anylabelwp
 *
 * @package AnylabelWP
 * @author WPOperator
 * @since 0.0.1
 */

// Escape if accessed directly 
if (!defined('ABSPATH')) {
    exit;
}

// Check minimum requirements
if (!anylabelwp_check_requirements()) {
    return;
}

/**
 * Check plugin requirements
 */
function anylabelwp_check_requirements() {
    global $wp_version;
    
    $min_wp = '5.0';
    $min_php = '7.4';
    
    if (version_compare(PHP_VERSION, $min_php, '<')) {
        add_action('admin_notices', function() use ($min_php) {
            echo '<div class="notice notice-error"><p>';
            printf(
                __('AnylabelWP requires PHP %s or higher. You are running PHP %s.', 'anylabelwp-plugin'),
                $min_php,
                PHP_VERSION
            );
            echo '</p></div>';
        });
        return false;
    }
    
    if (version_compare($wp_version, $min_wp, '<')) {
        add_action('admin_notices', function() use ($min_wp) {
            global $wp_version;
            echo '<div class="notice notice-error"><p>';
            printf(
                __('AnylabelWP requires WordPress %s or higher. You are running WordPress %s.', 'anylabelwp-plugin'),
                $min_wp,
                $wp_version
            );
            echo '</p></div>';
        });
        return false;
    }
    
    return true;
}

// Version and filepath declarations
define('ANYLABELWP_VERSION', '0.0.2');
define('ANYLABELWP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ANYLABELWP_PLUGIN_URL', plugin_dir_url(__FILE__));

// Require the class autoloader
require_once ANYLABELWP_PLUGIN_DIR . 'includes/class-anylabelwp-loader.php';

// Strictly define is_plugin_active() function
if (!function_exists('is_plugin_active')) {
    include_once(ABSPATH . 'wp-admin/includes/plugin.php');
}

/**
 * Initialize the plugin by instantiating the loader class.
 *
 * @return void
 */
function run_anylabelwp()
{
    $plugin = new \AnylabelWP\Loader();
    $plugin->run();
}

/**
 * Plugin activation hook
 */
function anylabelwp_activate()
{
    // Set default options
    if (!get_option('anylabelwp_allowed_roles')) {
        update_option('anylabelwp_allowed_roles', ['administrator']);
    }
    
    // Set activation notice
    set_transient('anylabelwp_activation_notice', true, 30);
    
    // Store activation time
    if (!get_option('anylabelwp_activation_time')) {
        update_option('anylabelwp_activation_time', time());
    }
    
    // Create any necessary database tables or options
    flush_rewrite_rules();
}

/**
 * Plugin deactivation hook
 */
function anylabelwp_deactivate()
{
    // Clean up any temporary data, caches, etc.
    flush_rewrite_rules();
}

// Register activation and deactivation hooks
register_activation_hook(__FILE__, 'anylabelwp_activate');
register_deactivation_hook(__FILE__, 'anylabelwp_deactivate');

// Load plugin
run_anylabelwp();
