<?php
namespace AnylabelWP;

/**
 * Plugin Name: AnylabelWP
 * Plugin URI:  # (Replace with your plugin URI if any)
 * Description: White Label 3rd Party Plugins.
 * Version:     0.0.2
 * Author:      WPOperator
 * Author URI:  # (Replace with your plugin URI if any)
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: anylabelwp-plugin
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

// Load plugin
run_anylabelwp();
