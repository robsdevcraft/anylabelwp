<?php
// If uninstall is not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Perform minimal uninstall actions here
delete_option('anylabelwp_fluent_smtp_logo_url');
