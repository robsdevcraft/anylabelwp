<?php
/**
 * Plugin Name: AnylabelWP
 * Plugin URI: https://github.com/wpoperator/anylabelwp
 * Description: White Label 3rd Party Plugins for WordPress.
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
 */

// Escape if accessed directly 
if (!defined('ABSPATH')) {
    exit;
}

// Version and filepath declarations
define('ANYLABELWP_VERSION', '0.0.2');
define('ANYLABELWP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ANYLABELWP_PLUGIN_URL', plugin_dir_url(__FILE__));

// Strictly define is_plugin_active() function
if (!function_exists('is_plugin_active')) {
    include_once(ABSPATH . 'wp-admin/includes/plugin.php');
}

// Global variable to store plugin instance
$anylabelwp_plugin_instance = null;

/**
 * Get the plugin instance
 */
function anylabelwp_get_instance() {
    global $anylabelwp_plugin_instance;
    return $anylabelwp_plugin_instance;
}

/**
 * Emergency shutdown function for plugin deletion
 */
function anylabelwp_emergency_shutdown() {
    global $anylabelwp_plugin_instance;
    
    if ($anylabelwp_plugin_instance && method_exists($anylabelwp_plugin_instance, 'emergency_shutdown')) {
        $anylabelwp_plugin_instance->emergency_shutdown();
    }
    
    // Force remove all hooks
    remove_all_actions('admin_menu');
    remove_all_actions('admin_enqueue_scripts');
    remove_all_filters('plugin_action_links');
    
    $anylabelwp_plugin_instance = null;
}

// Register emergency shutdown on plugin file deletion
register_shutdown_function('anylabelwp_emergency_shutdown');

/**
 * Initialize the plugin by instantiating the loader class.
 *
 * @return void
 */
function run_anylabelwp()
{
    global $anylabelwp_plugin_instance;
    
    // Check if the loader class file exists before requiring it
    $loader_file = ANYLABELWP_PLUGIN_DIR . 'includes/class-anylabelwp-loader.php';
    if (file_exists($loader_file)) {
        require_once $loader_file;
        
        if (class_exists('\AnylabelWP\Loader')) {
            $anylabelwp_plugin_instance = new \AnylabelWP\Loader();
            $anylabelwp_plugin_instance->run();
        } else {
            add_action('admin_notices', function() {
                echo '<div class="notice notice-error"><p>AnylabelWP: Loader class not found!</p></div>';
            });
        }
    } else {
        add_action('admin_notices', function() {
            echo '<div class="notice notice-error"><p>AnylabelWP: Loader file not found!</p></div>';
        });
    }
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
    
    // Create any necessary database tables or options
    flush_rewrite_rules();
}

/**
 * Plugin deactivation hook
 */
function anylabelwp_deactivate()
{
    global $anylabelwp_plugin_instance;
    
    // Emergency shutdown first
    anylabelwp_emergency_shutdown();
    
    // Remove any scheduled hooks
    wp_clear_scheduled_hook('anylabelwp_cleanup_task');
    
    // Clean up transients
    delete_transient('anylabelwp_cache');
    
    // Remove any plugin-specific options that might be locks
    delete_option('anylabelwp_plugin_lock');
    delete_option('anylabelwp_activation_time');
    
    // Force clear any object cache
    if (function_exists('wp_cache_flush')) {
        wp_cache_flush();
    }
    
    // Flush rewrite rules
    flush_rewrite_rules();
    
    // Set instance to null
    $anylabelwp_plugin_instance = null;
    
    // Force garbage collection
    if (function_exists('gc_collect_cycles')) {
        gc_collect_cycles();
    }
}

// Register activation and deactivation hooks
register_activation_hook(__FILE__, 'anylabelwp_activate');
register_deactivation_hook(__FILE__, 'anylabelwp_deactivate');

// Add a filter to handle plugin deletion more gracefully
add_filter('pre_delete_plugin', function($result, $plugin) {
    if ($plugin === plugin_basename(__FILE__)) {
        // Force deactivation before deletion
        anylabelwp_deactivate();
        anylabelwp_emergency_shutdown();
        
        // Clear any persistent data
        wp_cache_flush();
        
        // Small delay to ensure cleanup
        usleep(100000); // 0.1 seconds
    }
    return $result;
}, 10, 2);

// Load plugin
run_anylabelwp();
