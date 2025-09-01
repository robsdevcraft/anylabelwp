<?php
/**
 * Uninstall script for AnylabelWP plugin
 *
 * This file is called when the plugin is deleted from WordPress admin.
 * It should remove all traces of the plugin from the database.
 */

// If uninstall is not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Additional security check
if (!current_user_can('delete_plugins')) {
    exit;
}

// Check if it's being uninstalled specifically
if (__FILE__ !== WP_UNINSTALL_PLUGIN) {
    exit;
}

// Force any remaining cleanup
if (function_exists('anylabelwp_emergency_shutdown')) {
    anylabelwp_emergency_shutdown();
}

/*
 * Uninstall actions
 */

// Array of all plugin options to delete
$options_to_delete = [
    'anylabelwp_fluent_smtp_logo_url',
    'anylabelwp_fluent_crm_logo_url', 
    'anylabelwp_fluent_forms_logo_url',
    'anylabelwp_wp_social_ninja_logo_url',
    'anylabelwp_allowed_roles',
    'anylabelwp_some_option',
    'anylabelwp_version',
    'anylabelwp_plugin_lock',
    'anylabelwp_activation_time',
];

// Delete all plugin options
foreach ($options_to_delete as $option) {
    delete_option($option);
    delete_site_option($option); // For multisite
}

// Clean up any scheduled events
wp_clear_scheduled_hook('anylabelwp_cleanup_task');

// Remove any custom capabilities if added
// remove_cap() calls would go here if you add custom capabilities

// Clear any transients
$transients_to_clear = [
    'anylabelwp_cache',
    'anylabelwp_modules_cache',
    'anylabelwp_settings_cache',
];

foreach ($transients_to_clear as $transient) {
    delete_transient($transient);
    delete_site_transient($transient);
}

// Force clear object cache
if (function_exists('wp_cache_flush')) {
    wp_cache_flush();
}

// Remove any plugin-specific database tables if they were created
global $wpdb;

// Clear any remaining hooks (aggressive cleanup)
global $wp_filter;
if (isset($wp_filter)) {
    foreach ($wp_filter as $hook => $filter) {
        if (is_object($filter) && isset($filter->callbacks)) {
            foreach ($filter->callbacks as $priority => $callbacks) {
                foreach ($callbacks as $key => $callback) {
                    if (is_array($callback['function']) && 
                        isset($callback['function'][0]) &&
                        is_object($callback['function'][0])) {
                        $class_name = get_class($callback['function'][0]);
                        if (strpos($class_name, 'AnylabelWP') !== false) {
                            unset($wp_filter[$hook]->callbacks[$priority][$key]);
                        }
                    }
                }
            }
        }
    }
}

// Force garbage collection
if (function_exists('gc_collect_cycles')) {
    gc_collect_cycles();
}

// Log uninstall for debugging (optional)
if (defined('WP_DEBUG') && WP_DEBUG) {
    error_log('AnylabelWP plugin uninstalled successfully at ' . current_time('mysql'));
}
