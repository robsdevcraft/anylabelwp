<?php
// If uninstall is not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Perform uninstall actions here
function anylabelwp_uninstall() {
    // Delete plugin options
    delete_option('anylabelwp_settings');

    // Delete any custom database tables (if you've created any)
    global $wpdb;
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}anylabelwp_custom_table");

    // Clear any scheduled cron jobs
    wp_clear_scheduled_hook('anylabelwp_cron_job');

    // Remove any custom user roles or capabilities
    remove_role('anylabelwp_custom_role');

    // Delete any transients
    delete_transient('anylabelwp_transient');

    // Perform any other cleanup tasks specific to your plugin
}

anylabelwp_uninstall();
