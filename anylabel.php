<?php
/**
 * Plugin Name: AnylabelWP
 * Plugin URI:  # (Replace with your plugin URI if any)
 * Description: White Label 3rd Party Plugins.
 * Version:     0.0.1
 * Author:      WPOperator
 * Author URI:  # (Replace with your plugin URI if any)
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: anylabel
 */

 if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function anylabelwp_activate() {
  // (Optional) Add activation logic here if needed
}
register_activation_hook( __FILE__, 'anylabelwp_activate' );

function anylabelwp_add_menu() {
    add_options_page(
        'AnylabelWP', // Page title
        'AnylabelWP', // Menu title
        'manage_options', // Capability
        'anylabelwp-settings', // Menu slug
        'anylabelwp_settings_page' // Function that handles the page content
    );
}
add_action( 'admin_menu', 'anylabelwp_add_menu' );

function anylabelwp_register_settings() {
    register_setting( 'anylabelwp', 'anylabelwp_new_url', 'sanitize_text_field' );
}

function anylabelwp_enqueue_scripts() {
    if (isset($_GET['page']) && $_GET['page'] == 'anylabelwp-settings') {
        wp_enqueue_media();
    }
}
add_action('admin_enqueue_scripts', 'anylabelwp_enqueue_scripts');

add_action( 'admin_init', 'anylabelwp_register_settings' );

function anylabelwp_settings_page() {
    // Verify user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }

    // Check nonce
    if (isset($_POST['anylabelwp_nonce']) && wp_verify_nonce($_POST['anylabelwp_nonce'], 'anylabelwp_save_settings')) {
        // Save settings here
    }

    include plugin_dir_path( __FILE__ ) . 'settings.php';
}

function anylabelwp_enqueue_script_admin() {
    wp_enqueue_script( 'anylabelwp-script', plugin_dir_url( __FILE__ ) . 'fluent-smtp.js' );
    wp_enqueue_style( 'anylabelwp-style', plugin_dir_url( __FILE__ ) . 'styles.css' ); // Enqueue your CSS file
    wp_localize_script( 'anylabelwp-script', 'anylabelwp', array(
    'new_url' => esc_url(anylabelwp_get_new_url()),
    ) );
}
add_action( 'admin_enqueue_scripts', 'anylabelwp_enqueue_script_admin' );

function anylabelwp_get_new_url() {
    // Get the new URL from the database or wherever it's stored
    $new_url = get_option( 'anylabelwp_new_url' );
    return $new_url;
}
add_action('admin_menu', 'change_fluent_smtp_menu_name', 999);

function change_fluent_smtp_menu_name() {
    global $submenu;

    // Change submenu name
    if (isset($submenu['options-general.php'])) {
        // Output the contents of the submenu for debugging
        error_log(print_r($submenu['options-general.php'], true));

        foreach ($submenu['options-general.php'] as $key => $value) {
            if ('FluentSMTP' == $value[0]) {
                $submenu['options-general.php'][$key][0] = 'SMTP';
                break;
            }
        }
    }
}

register_uninstall_hook(__FILE__, 'anylabelwp_uninstall');

function anylabelwp_uninstall() {
    // Delete options from the database
    delete_option('anylabelwp_new_url');
}

?>
