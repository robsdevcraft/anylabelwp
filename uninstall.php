<?php
// If uninstall is not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

/*
*   Uninstall actions
*/

// Array of option suffixes to delete
$options_to_delete = [
    'fluent_smtp_logo_url',
    'fluent_crm_logo_url',
    'fluent_forms_logo_url',
    'wp_social_ninja_logo_url',
];

// Loop through the options and delete them
foreach ($options_to_delete as $option_suffix) {
    delete_option('anylabelwp_' . $option_suffix);
}
