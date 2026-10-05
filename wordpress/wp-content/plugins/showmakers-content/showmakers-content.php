<?php
/**
 * Plugin Name: ShowMakers Content
 * Description: ShowMakers content structures and controlled editorial fields.
 * Version: 0.3.0
 * Requires PHP: 8.2
 * Text Domain: showmakers-content
 */
defined( 'ABSPATH' ) || exit;
define( 'SHOWMAKERS_CONTENT_PATH', plugin_dir_path( __FILE__ ) );
foreach ( array( 'types', 'fields', 'media', 'clients', 'settings', 'contact', 'seo', 'admin' ) as $module ) require_once SHOWMAKERS_CONTENT_PATH . 'inc/' . $module . '.php';
register_activation_hook( __FILE__, 'showmakers_content_activate' );
register_deactivation_hook( __FILE__, function () { flush_rewrite_rules(); } );
