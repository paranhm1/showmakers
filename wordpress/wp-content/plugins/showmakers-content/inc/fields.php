<?php
defined( 'ABSPATH' ) || exit;
add_filter( 'acf/settings/load_json', function ( $paths ) {
    $paths[] = SHOWMAKERS_CONTENT_PATH . 'acf-json';
    return $paths;
} );
foreach ( array( 'projects', 'clients', 'services', 'media', 'about', 'home', 'services_page', 'contact_page', 'seo' ) as $group ) {
    add_filter( 'acf/settings/save_json/key=group_showmakers_' . $group, function () { return SHOWMAKERS_CONTENT_PATH . 'acf-json'; } );
}
// These are plain editorial text fields, not arbitrary executable markup.
foreach ( array( 'short_summary', 'client_name', 'service_intro', 'service_description', 'capabilities', 'platforms', 'formats', 'service_media_caption', 'service_media_2_caption', 'platform_facebook_description', 'platform_instagram_description', 'platform_xiaohongshu_description', 'platform_tiktok_description', 'internal_media_note' ) as $name ) {
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

// Preserve legacy Client metadata without presenting ambiguous marquee controls.
foreach ( array( 'field_showmakers_c_visible', 'field_showmakers_c_sort_order' ) as $key ) {
    add_filter( 'acf/prepare_field/key=' . $key, '__return_false' );
}

// About copy is plain text; page-specific field keys avoid changing unrelated content.
$about_fields = json_decode( file_get_contents( SHOWMAKERS_CONTENT_PATH . 'acf-json/group_showmakers_about.json' ), true );
foreach ( $about_fields['fields'] as $field ) {
    if ( in_array( $field['type'], array( 'text', 'textarea' ), true ) ) {
        add_filter( 'acf/update_value/key=' . $field['key'], function ( $value ) { return sanitize_textarea_field( (string) $value ); } );
        if ( $field['required'] ) add_filter( 'acf/validate_value/key=' . $field['key'], function ( $valid, $value ) {
            return trim( sanitize_textarea_field( (string) $value ) ) !== '' ? $valid : 'Please complete this About content field.';
        }, 10, 2 );
    }
}
add_action( 'admin_notices', function () {
    $screen = get_current_screen();
    $id = absint( $_GET['post'] ?? 0 );
    if ( ! $screen || $screen->post_type !== 'page' || ! $id || get_post_field( 'post_name', $id ) !== 'about' || ! current_user_can( 'edit_post', $id ) ) return;
    $definition = json_decode( file_get_contents( SHOWMAKERS_CONTENT_PATH . 'acf-json/group_showmakers_about.json' ), true );
    $missing = array();
    foreach ( $definition['fields'] as $field ) if ( $field['required'] && trim( (string) get_post_meta( $id, $field['name'], true ) ) === '' ) $missing[] = $field['label'];
    if ( $missing ) echo '<div class="notice notice-warning"><p>Complete the required About content: ' . esc_html( implode( ', ', $missing ) ) . '.</p></div>';
    $image = absint( get_post_meta( $id, 'about_approach_image', true ) );
    if ( $image && ! showmakers_approved_media( $image ) ) echo '<div class="notice notice-warning"><p>The Production Image is not shown. Choose an approved image in the Media Library.</p></div>';
} );

// This single structured Page uses the native classic field editor, not an unused block canvas.
add_filter( 'use_block_editor_for_post', function ( $use, $post ) {
    return $post->post_type === 'page' && (int) $post->ID === 9 ? false : $use;
}, 10, 2 );

// Page-specific copy uses free ACF plain fields; no shared content is duplicated.
foreach ( array( 'home', 'services_page', 'contact_page' ) as $group ) {
    $definition = json_decode( file_get_contents( SHOWMAKERS_CONTENT_PATH . 'acf-json/group_showmakers_' . $group . '.json' ), true );
    foreach ( $definition['fields'] as $field ) {
        add_filter( 'acf/update_value/key=' . $field['key'], function ( $value ) { return sanitize_textarea_field( (string) $value ); } );
        if ( $field['required'] ) add_filter( 'acf/validate_value/key=' . $field['key'], function ( $valid, $value ) {
            return trim( sanitize_textarea_field( (string) $value ) ) !== '' ? $valid : 'Please complete this page heading field.';
        }, 10, 2 );
    }
}
add_filter( 'use_block_editor_for_post', function ( $use, $post ) {
    return $post->post_type === 'page' && in_array( (int) $post->ID, array( 95, 8, 10 ), true ) ? false : $use;
}, 10, 2 );
add_action( 'admin_notices', function () {
    $id = absint( $_GET['post'] ?? 0 );
    $group = $id === 95 ? 'home' : ( $id === 8 ? 'services_page' : ( $id === 10 ? 'contact_page' : '' ) );
    if ( ! $group || ! current_user_can( 'edit_post', $id ) ) return;
    $definition = json_decode( file_get_contents( SHOWMAKERS_CONTENT_PATH . 'acf-json/group_showmakers_' . $group . '.json' ), true );
    $missing = array();
    foreach ( $definition['fields'] as $field ) if ( $field['required'] && trim( (string) get_post_meta( $id, $field['name'], true ) ) === '' ) $missing[] = $field['label'];
    if ( $missing ) echo '<div class="notice notice-warning"><p>Complete the required page headings: ' . esc_html( implode( ', ', $missing ) ) . '.</p></div>';
} );

foreach ( array( 'seo_title', 'seo_description' ) as $name ) add_filter( 'acf/update_value/name=' . $name, function ( $value ) { return sanitize_textarea_field( (string) $value ); } );
