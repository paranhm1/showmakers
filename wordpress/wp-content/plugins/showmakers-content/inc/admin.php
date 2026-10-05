<?php
defined( 'ABSPATH' ) || exit;
function showmakers_content_columns( $columns, $type ) {
    $out = array( 'cb' => $columns['cb'], 'title' => $columns['title'] );
    $labels = $type === 'project'
        ? array( 'client' => 'Client', 'services' => 'Services', 'status' => 'Status', 'featured' => 'Featured', 'sort_order' => 'Sort Order', 'media_status' => 'Media Status', 'modified' => 'Last Modified' )
        : array( 'logo' => 'Logo', 'visible' => 'Visible', 'sort_order' => 'Sort Order', 'modified' => 'Last Modified' );
    foreach ( $labels as $key => $label ) $out[ 'sm_' . $key ] = $label;
    return $out;
}
foreach ( array( 'project', 'client' ) as $type ) {
    add_filter( 'manage_' . $type . '_posts_columns', function ( $columns ) use ( $type ) { return showmakers_content_columns( $columns, $type ); } );
    add_action( 'manage_' . $type . '_posts_custom_column', function ( $column, $id ) {
        $key = substr( $column, 3 );
        if ( $key === 'logo' ) {
            $logo = absint( get_post_meta( $id, 'client_logo', true ) );
            if ( $logo ) echo wp_get_attachment_image( $logo, array( 48, 32 ), false, array( 'style' => 'max-width:48px;max-height:32px;object-fit:contain' ) );
            return;
        }
        if ( $key === 'client' ) {
            $client = absint( get_post_meta( $id, 'client', true ) );
            $value = $client ? get_the_title( $client ) : ( get_post_meta( $id, 'client_name', true ) ?: '—' );
        } elseif ( $key === 'services' ) {
            $names = wp_get_object_terms( $id, 'service', array( 'fields' => 'names' ) );
            $value = is_wp_error( $names ) ? '—' : implode( ', ', $names );
        } elseif ( $key === 'status' ) $value = get_post_status( $id );
        elseif ( $key === 'modified' ) $value = get_post_modified_time( 'Y-m-d H:i', false, $id );
        elseif ( in_array( $key, array( 'featured', 'visible' ), true ) ) $value = get_post_meta( $id, $key, true ) ? 'Yes' : 'No';
        else $value = get_post_meta( $id, $key, true ) ?: ( $key === 'media_status' ? 'pending' : '0' );
        echo esc_html( $value );
    }, 10, 2 );
    add_filter( 'manage_edit-' . $type . '_sortable_columns', function ( $columns ) { $columns['sm_sort_order'] = 'sm_sort_order'; return $columns; } );
}
add_action( 'pre_get_posts', function ( $query ) {
    if ( is_admin() && $query->is_main_query() && $query->get( 'orderby' ) === 'sm_sort_order' ) {
        $query->set( 'meta_key', 'sort_order' ); $query->set( 'orderby', 'meta_value_num' );
    }
} );
