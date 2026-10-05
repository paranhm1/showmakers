<?php
/** CMS presentation adapters. Content types and approval rules belong to the plugin. */
defined( 'ABSPATH' ) || exit;
add_filter( 'document_title_parts', function ( $parts ) {
    if ( is_post_type_archive( 'project' ) ) $parts['title'] = 'Work';
    return $parts;
} );
function showmakers_visible_projects() {
    if ( ! function_exists( 'showmakers_project_eligibility_query' ) ) return array();
    $projects = get_posts( array( 'post_type' => 'project', 'post_status' => 'publish', 'posts_per_page' => -1, 'meta_query' => showmakers_project_eligibility_query() ) );
    usort( $projects, function ( $a, $b ) {
        return ( (int) get_post_meta( $a->ID, 'sort_order', true ) <=> (int) get_post_meta( $b->ID, 'sort_order', true ) ) ?: ( $a->ID <=> $b->ID );
    } );
    return $projects;
}
function showmakers_project_services( $id ) {
    $terms = wp_get_object_terms( $id, 'service' );
    if ( is_wp_error( $terms ) ) return array();
    // Hide public labels/links only; actual Project term assignments remain unchanged.
    $terms = array_values( array_filter( $terms, 'showmakers_service_is_visible' ) );
    usort( $terms, function ( $a, $b ) {
        return ( (int) get_term_meta( $a->term_id, 'sort_order', true ) <=> (int) get_term_meta( $b->term_id, 'sort_order', true ) ) ?: ( $a->term_id <=> $b->term_id );
    } );
    return $terms;
}
function showmakers_service_project_count( $slug, $projects = null ) {
    $projects = $projects ?? showmakers_visible_projects();
    return count( array_filter( $projects, function ( $project ) use ( $slug ) { return has_term( $slug, 'service', $project->ID ); } ) );
}
function showmakers_project_client_name( $id ) {
    $client = absint( get_post_meta( $id, 'client', true ) );
    return get_post_type( $client ) === 'client' ? get_the_title( $client ) : (string) get_post_meta( $id, 'client_name', true );
}
function showmakers_project_renderable( $id ) {
    if ( ! function_exists( 'showmakers_public_project' ) ) return false;
    if ( showmakers_public_project( $id ) ) return true;
    // Only the requested, authorized WordPress draft preview can bypass publication.
    return is_preview() && get_queried_object_id() === (int) $id && current_user_can( 'edit_post', $id )
        && get_post_type( $id ) === 'project' && get_post_status( $id ) === 'draft'
        && get_post_meta( $id, 'media_status', true ) === 'approved'
        && ! get_post_meta( $id, 'development_only', true ) && ! get_post_meta( $id, 'is_placeholder', true );
}
function showmakers_renderable_project_images( $id ) {
    if ( ! showmakers_project_renderable( $id ) ) return array();
    $out = array();
    foreach ( array( 'listing_thumbnail', 'hero_media', 'project_image_1', 'project_image_2', 'project_image_3', 'project_image_4', 'project_image_5' ) as $key ) {
        $attachment = absint( get_post_meta( $id, $key, true ) );
        if ( showmakers_approved_media( $attachment ) ) $out[ $key ] = $attachment;
    }
    return $out;
}
function showmakers_cms_image( $id, $lazy = true ) {
    if ( ! function_exists( 'showmakers_approved_media' ) || ! showmakers_approved_media( $id ) ) return '';
    $image = wp_get_attachment_image_src( $id, 'full' );
    if ( ! $image ) return '';
    $native = $image[2] > $image[1] * 1.4 ? ' style="max-width:min(100%,' . absint( $image[1] ) . 'px)"' : '';
    return '<img class="" src="' . esc_url( $image[0] ) . '" alt="' . esc_attr( get_post_meta( $id, '_wp_attachment_image_alt', true ) )
        . '" width="' . absint( $image[1] ) . '" height="' . absint( $image[2] ) . '"'
        . ( $lazy ? ' loading="lazy"' : '' ) . ' decoding="async"' . $native . '>';
}
function showmakers_arrow_icon( $short = false ) {
    $end = $short ? 20 : 60;
    return '<svg class="ui-arrow" viewBox="0 0 ' . ( $end + 4 ) . ' 16" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="square" stroke-linejoin="miter"><path d="M2 8H' . $end . 'M' . ( $end - 6 ) . ' 2L' . $end . ' 8L' . ( $end - 6 ) . ' 14"/></svg>';
}
function showmakers_project_context_data() {
    return array_map( function ( $project ) {
        return array( 'slug' => $project->post_name, 'title' => $project->post_title, 'url' => get_permalink( $project ), 'services' => wp_list_pluck( showmakers_project_services( $project->ID ), 'slug' ) );
    }, showmakers_visible_projects() );
}
