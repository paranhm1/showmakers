<?php
/** Shared CMS service presentation. No JSON/static content fallback. */
defined( 'ABSPATH' ) || exit;
function showmakers_service_is_visible( $term ) {
    return $term instanceof WP_Term && $term->taxonomy === 'service' && (string) get_term_meta( $term->term_id, 'visible', true ) === '1';
}
function showmakers_visible_services() {
    $terms = get_terms( array( 'taxonomy' => 'service', 'hide_empty' => false ) );
    if ( is_wp_error( $terms ) ) return array();
    $terms = array_values( array_filter( $terms, 'showmakers_service_is_visible' ) );
    usort( $terms, function ( $a, $b ) {
        return ( (int) get_term_meta( $a->term_id, 'sort_order', true ) <=> (int) get_term_meta( $b->term_id, 'sort_order', true ) ) ?: ( $a->term_id <=> $b->term_id );
    } );
    return $terms;
}
function showmakers_service_lines( $value ) {
    return array_values( array_filter( array_map( 'trim', preg_split( '/\R/u', sanitize_textarea_field( (string) $value ) ) ?: array() ), function ( $line ) { return $line !== ''; } ) );
}
function showmakers_service_passages( $term ) {
    $platforms = showmakers_service_lines( get_term_meta( $term->term_id, 'platforms', true ) );
    $out = array();
    // Fixed approved platform identities/artwork; descriptions live in free ACF, no custom repeater.
    foreach ( array( 'facebook' => 'Facebook', 'instagram' => 'Instagram', 'xiaohongshu' => 'XiaoHongShu', 'tiktok' => 'TikTok' ) as $key => $label ) {
        $text = trim( (string) get_term_meta( $term->term_id, 'platform_' . $key . '_description', true ) );
        if ( $text !== '' && in_array( $label, $platforms, true ) ) $out[] = array( 'title' => $label, 'text' => $text, 'icon' => 'images/platforms/' . $key . '.svg' );
    }
    return $out;
}
