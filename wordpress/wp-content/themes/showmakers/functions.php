<?php
/** Theme infrastructure only: no content types, fields or contact processing. */
defined( 'ABSPATH' ) || exit;
require_once get_theme_file_path( '/inc/assets.php' );
require_once get_theme_file_path( '/inc/projects.php' );
require_once get_theme_file_path( '/inc/services.php' );
require_once get_theme_file_path( '/inc/about.php' );
add_action( 'after_setup_theme', function () {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails', array( 'project' ) );
    add_theme_support( 'html5', array( 'style', 'script' ) );
} );

/** Plain page wording; visual markup and destinations belong to the theme. */
function showmakers_page_copy( $page, $field ) {
    return trim( sanitize_textarea_field( (string) get_post_meta( $page, $field, true ) ) );
}
