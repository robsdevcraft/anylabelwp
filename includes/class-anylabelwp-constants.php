<?php
namespace AnylabelWP;

/**
 * Plugin constants and configuration
 */
class Constants
{
    /**
     * Plugin version
     */
    const VERSION = '0.0.2';
    
    /**
     * Option name prefix
     */
    const OPTION_PREFIX = 'anylabelwp_';
    
    /**
     * Text domain
     */
    const TEXT_DOMAIN = 'anylabelwp-plugin';
    
    /**
     * Minimum WordPress version
     */
    const MIN_WP_VERSION = '5.0';
    
    /**
     * Minimum PHP version
     */
    const MIN_PHP_VERSION = '7.4';
    
    /**
     * Supported third-party plugins
     */
    const SUPPORTED_PLUGINS = [
        'fluent-smtp' => 'fluent-smtp/fluent-smtp.php',
        'fluent-forms' => 'fluentform/fluentform.php',
        'fluent-crm' => 'fluent-crm/fluent-crm.php',
        'wp-social-ninja' => 'wp-social-ninja/wp-social-ninja.php'
    ];
    
    /**
     * Default allowed roles
     */
    const DEFAULT_ALLOWED_ROLES = ['administrator'];
}
