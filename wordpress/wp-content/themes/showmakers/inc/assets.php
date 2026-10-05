<?php
/** Approved assets; native WordPress routes and ES modules. */
defined( 'ABSPATH' ) || exit;
function showmakers_asset_url( $path ) {
    return get_theme_file_uri( '/assets/' . ltrim( $path, '/' ) );
}
function showmakers_page_key() {
    if ( is_404() ) return '404';
    if ( is_front_page() ) return 'home';
    foreach ( array( 'work', 'services', 'about', 'contact' ) as $page ) {
        if ( is_page( $page ) ) return $page;
    }
    return 'about'; // Safe shared typography for the fallback shell.
}
function showmakers_asset_version( $path ) {
    $file = get_theme_file_path( '/assets/' . $path );
    return is_file( $file ) ? (string) filemtime( $file ) : '0.2.0';
}
add_action( 'wp_enqueue_scripts', function () {
    wp_enqueue_style( 'showmakers-global', showmakers_asset_url( 'css/global.css' ), array(), showmakers_asset_version( 'css/global.css' ) );
    wp_enqueue_style( 'showmakers-components', showmakers_asset_url( 'css/components.css' ), array( 'showmakers-global' ), showmakers_asset_version( 'css/components.css' ) );
    $page = showmakers_page_key();
    $path = 'css/pages/' . $page . '.css';
    wp_enqueue_style( 'showmakers-page', showmakers_asset_url( $path ), array( 'showmakers-components' ), showmakers_asset_version( $path ) );
    wp_enqueue_script_module( 'showmakers-global', showmakers_asset_url( 'js/global.js' ), array(), showmakers_asset_version( 'js/global.js' ) );
    if ( is_front_page() ) {
        wp_enqueue_script_module( 'showmakers-home', showmakers_asset_url( 'js/home.js' ), array(), showmakers_asset_version( 'js/home.js' ) );
    }
} );
