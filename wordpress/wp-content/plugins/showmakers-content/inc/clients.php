<?php
/** Explicit brand-logo membership; never inferred from Projects. */
defined( 'ABSPATH' ) || exit;
function showmakers_client_logo_asset( $id ) {
    $id = absint( $id );
    if ( get_post_type( $id ) !== 'attachment' || ! in_array( get_post_mime_type( $id ), array( 'image/png', 'image/webp', 'image/jpeg', 'image/avif' ), true ) ) return false;
    // Explicit attachment restrictions still win. A newly uploaded logo is governed
    // by its Client approval; this does not grant any Project-media approval.
    $status = get_post_meta( $id, 'media_status', true );
    if ( $status !== '' && $status !== 'approved' ) return false;
    $file = get_attached_file( $id );
    if ( ! $file || ! is_file( $file ) || preg_match( '/^(brand-showcase|samsung-experience)(?:[-.]|$)/i', basename( $file ) ) ) return false;
    return (bool) wp_get_attachment_image_src( $id, 'full' );
}
function showmakers_marquee_clients() {
    $clients = get_posts( array( 'post_type' => 'client', 'post_status' => 'publish', 'posts_per_page' => -1,
        'meta_query' => array( 'relation' => 'AND',
            array( 'key' => 'show_in_marquee', 'value' => '1' ),
            array( 'key' => 'logo_status', 'value' => 'approved' ),
            array( 'key' => 'client_logo', 'value' => 0, 'compare' => '>', 'type' => 'NUMERIC' ),
        ) ) );
    $clients = array_values( array_filter( $clients, function ( $client ) { return (bool) showmakers_public_client_logo( $client->ID ); } ) );
    usort( $clients, function ( $a, $b ) {
        return ( (int) get_post_meta( $a->ID, 'marquee_sort_order', true ) <=> (int) get_post_meta( $b->ID, 'marquee_sort_order', true ) ) ?: ( $a->ID <=> $b->ID );
    } );
    return $clients;
}
