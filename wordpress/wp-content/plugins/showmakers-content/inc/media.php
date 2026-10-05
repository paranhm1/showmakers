<?php
defined( 'ABSPATH' ) || exit;
function showmakers_approved_media( $id ) {
    $id = absint( $id );
    if ( get_post_type( $id ) !== 'attachment' || get_post_meta( $id, 'media_status', true ) !== 'approved' ) return false;
    $filename = basename( (string) get_post_meta( $id, '_wp_attached_file', true ) );
    return ! preg_match( '/^(brand-showcase|samsung-experience)(?:[-.]|$)/i', $filename );
}
function showmakers_public_project( $id ) {
    return get_post_type( $id ) === 'project' && get_post_status( $id ) === 'publish'
        && get_post_meta( $id, 'media_status', true ) === 'approved'
        && ! get_post_meta( $id, 'development_only', true )
        && ! get_post_meta( $id, 'is_placeholder', true );
}
function showmakers_project_images( $id ) {
    if ( ! showmakers_public_project( $id ) ) return array();
    $images = array();
    foreach ( array( 'listing_thumbnail', 'hero_media', 'project_image_1', 'project_image_2', 'project_image_3', 'project_image_4', 'project_image_5' ) as $key ) {
        $media = absint( get_post_meta( $id, $key, true ) );
        if ( showmakers_approved_media( $media ) ) $images[ $key ] = $media;
    }
    return $images; // IDs only: the later theme must escape all resolved URLs/attributes.
}
function showmakers_project_eligibility_query() {
    return array( 'relation' => 'AND',
        array( 'key' => 'media_status', 'value' => 'approved' ),
        array( 'relation' => 'OR', array( 'key' => 'development_only', 'compare' => 'NOT EXISTS' ), array( 'key' => 'development_only', 'value' => '1', 'compare' => '!=' ) ),
        array( 'relation' => 'OR', array( 'key' => 'is_placeholder', 'compare' => 'NOT EXISTS' ), array( 'key' => 'is_placeholder', 'value' => '1', 'compare' => '!=' ) ),
    );
}
add_action( 'pre_get_posts', function ( $query ) {
    if ( is_admin() || ! $query->is_main_query() ) return;
    if ( $query->get( 'post_type' ) === 'project' || $query->is_post_type_archive( 'project' ) ) {
        $old = $query->get( 'meta_query' );
        $query->set( 'meta_query', $old ? array( 'relation' => 'AND', $old, showmakers_project_eligibility_query() ) : showmakers_project_eligibility_query() );
    }
} );
add_filter( 'rest_project_query', function ( $args ) {
    if ( ! current_user_can( 'edit_projects' ) ) $args['meta_query'] = showmakers_project_eligibility_query();
    return $args;
} );
add_filter( 'rest_prepare_project', function ( $response, $post ) {
    if ( ! current_user_can( 'edit_post', $post->ID ) ) {
        if ( ! showmakers_public_project( $post->ID ) ) return new WP_Error( 'rest_not_found', 'Not found.', array( 'status' => 404 ) );
        $data = $response->get_data();
        if ( ! showmakers_approved_media( $data['featured_media'] ?? 0 ) ) $data['featured_media'] = 0;
        $response->set_data( $data );
    }
    return $response;
}, 10, 2 );
add_filter( 'wp_sitemaps_posts_query_args', function ( $args, $type ) {
    if ( $type === 'project' ) $args['meta_query'] = showmakers_project_eligibility_query();
    return $args;
}, 10, 2 );
function showmakers_public_client_logo( $id ) {
    if ( get_post_type( $id ) !== 'client' || get_post_status( $id ) !== 'publish'
        || ! get_post_meta( $id, 'visible', true ) || get_post_meta( $id, 'logo_status', true ) !== 'approved' ) return 0;
    $logo = absint( get_post_meta( $id, 'client_logo', true ) );
    return showmakers_approved_media( $logo ) ? $logo : 0;
}
// Protect mixed searches/feeds too; do not filter the authorized editorial Admin.
add_filter( 'the_posts', function ( $posts ) {
    if ( is_admin() || current_user_can( 'edit_projects' ) ) return $posts;
    return array_values( array_filter( $posts, function ( $post ) {
        return $post->post_type !== 'project' || showmakers_public_project( $post->ID );
    } ) );
} );
