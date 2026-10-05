<?php
/** Theme infrastructure only: no content types, fields or contact processing. */
defined( 'ABSPATH' ) || exit;
require_once get_theme_file_path( '/inc/assets.php' );
add_action( 'after_setup_theme', function () {
    add_theme_support( 'title-tag' );
    add_theme_support( 'html5', array( 'style', 'script' ) );
} );
