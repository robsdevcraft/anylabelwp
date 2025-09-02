<?php
/**
 * Uninstall AnylabelWP
 *
 * Removes all plugin data when deleted via WordPress admin.
 *
 * @package AnylabelWP
 * @since 0.0.1
 */

// If uninstall is not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

/*
 * Remove all plugin data
 */

// Plugin options to delete
$options_to_delete = [
    'anylabelwp_fluent_smtp_logo_url',
    'anylabelwp_fluent_crm_logo_url',
    'anylabelwp_fluent_forms_logo_url', 
    'anylabelwp_wp_social_ninja_logo_url',
    'anylabelwp_allowed_roles',
    'anylabelwp_activation_time',
    'anylabelwp_version',
];

// Delete options
foreach ($options_to_delete as $option) {
    delete_option($option);
    // Also delete from multisite if applicable
    delete_site_option($option);
}

// Delete user meta
delete_metadata('user', 0, 'anylabelwp_hide_plugin_notice', '', true);

// Clear transients
delete_transient('anylabelwp_activation_notice');
delete_site_transient('anylabelwp_activation_notice');

// Clear scheduled events
wp_clear_scheduled_hook('anylabelwp_cleanup_task');

// Clear any cached data
wp_cache_flush();

// Log uninstall if debug mode is on
if (defined('WP_DEBUG') && WP_DEBUG) {
    error_log('AnylabelWP: Plugin uninstalled and all data removed.');
}
