<?php
/**
 * Plugin Name: AnylabelWP
 * Plugin URI:  # (Replace with your plugin URI if any)
 * Description: White Label 3rd Party Plugins.
 * Version:     0.0.2
 * Author:      WPOperator
 * Author URI:  # (Replace with your plugin URI if any)
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: anylabelwp
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'ANYLABELWP_VERSION', '0.0.2' );
define( 'ANYLABELWP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'ANYLABELWP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once ANYLABELWP_PLUGIN_DIR . 'includes/class-anylabelwp-loader.php';

if (!function_exists('is_plugin_active')) {
    include_once(ABSPATH . 'wp-admin/includes/plugin.php');
}

function run_anylabelwp() {
    $plugin = new AnylabelWP_Loader();
    $plugin->run();
}

run_anylabelwp();

?>
