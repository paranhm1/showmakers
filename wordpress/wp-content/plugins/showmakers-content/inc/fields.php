<?php
defined( 'ABSPATH' ) || exit;
add_filter( 'acf/settings/load_json', function ( $paths ) {
    $paths[] = SHOWMAKERS_CONTENT_PATH . 'acf-json';
    return $paths;
} );
foreach ( array( 'projects', 'clients', 'services', 'media' ) as $group ) {
    add_filter( 'acf/settings/save_json/key=group_showmakers_' . $group, function () { return SHOWMAKERS_CONTENT_PATH . 'acf-json'; } );
}
// These are plain editorial text fields, not arbitrary executable markup.
foreach ( array( 'short_summary', 'client_name', 'service_intro', 'service_description', 'capabilities', 'platforms', 'formats', 'internal_media_note' ) as $name ) {
    add_filter( 'acf/update_value/name=' . $name, function ( $value ) { return sanitize_textarea_field( (string) $value ); } );
}
add_filter( 'acf/validate_value/name=client', function ( $valid, $value ) {
    return ! $value || get_post_type( (int) $value ) === 'client' ? $valid : __( 'Please select a Client record.', 'showmakers-content' );
}, 10, 2 );
add_action( 'admin_notices', function () {
    if ( ! function_exists( 'acf_get_field_groups' ) && current_user_can( 'activate_plugins' ) ) {
        echo '<div class="notice notice-warning"><p>' . esc_html__( 'ShowMakers editorial fields require the official free Advanced Custom Fields plugin.', 'showmakers-content' ) . '</p></div>';
    }
} );
