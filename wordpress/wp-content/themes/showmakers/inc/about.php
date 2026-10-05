<?php
/** Plain-text Page content; approved About presentation stays in the theme. */
defined( 'ABSPATH' ) || exit;
function showmakers_about_text( $page, $field ) {
    return trim( sanitize_textarea_field( (string) get_post_meta( $page, $field, true ) ) );
}
function showmakers_about_heading( $text ) {
    // Preserve the approved word grouping, without storing markup or desktop breaks.
    return str_replace( 'Hands-on', '<span class="keep-word">Hands-on</span>', esc_html( $text ) );
}
function showmakers_about_image( $page ) {
    $id = absint( get_post_meta( $page, 'about_approach_image', true ) );
    if ( ! function_exists( 'showmakers_approved_media' ) || ! showmakers_approved_media( $id ) || ! is_file( (string) get_attached_file( $id ) ) ) return '';
    $image = wp_get_attachment_image_src( $id, 'full' );
    if ( ! $image ) return '';
    $alt = showmakers_about_text( $page, 'about_approach_image_alt' ) ?: (string) get_post_meta( $id, '_wp_attachment_image_alt', true );
    return '<img class="approach-image" src="' . esc_url( $image[0] ) . '" alt="' . esc_attr( $alt ) . '" width="' . absint( $image[1] ) . '" height="' . absint( $image[2] ) . '" loading="lazy" decoding="async" style="max-width:' . absint( $image[1] ) . 'px">';
}
