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
