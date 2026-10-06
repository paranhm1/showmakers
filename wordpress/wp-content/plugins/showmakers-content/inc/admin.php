<?php
defined( 'ABSPATH' ) || exit;
function showmakers_content_columns( $columns, $type ) {
    $out = array( 'cb' => $columns['cb'], 'title' => $type === 'client' ? 'Client' : $columns['title'] );
    $labels = $type === 'project'
        ? array( 'client' => 'Client', 'services' => 'Services', 'status' => 'Status', 'featured' => 'Featured', 'sort_order' => 'Sort Order', 'media_status' => 'Media Status', 'modified' => 'Last Modified' )
        : array( 'logo' => 'Logo Preview', 'logo_status' => 'Logo Approval', 'show_in_marquee' => 'Selected Clients', 'marquee_sort_order' => 'Marquee Order', 'modified' => 'Last Modified' );
    foreach ( $labels as $key => $label ) $out[ 'sm_' . $key ] = $label;
    return $out;
}
foreach ( array( 'project', 'client' ) as $type ) {
    add_filter( 'manage_' . $type . '_posts_columns', function ( $columns ) use ( $type ) { return showmakers_content_columns( $columns, $type ); } );
    add_action( 'manage_' . $type . '_posts_custom_column', function ( $column, $id ) {
        $key = substr( $column, 3 );
        if ( $key === 'logo' ) {
            $logo = absint( get_post_meta( $id, 'client_logo', true ) );
            if ( $logo && wp_get_attachment_image_src( $logo, 'thumbnail' ) ) echo wp_get_attachment_image( $logo, array( 48, 32 ), false, array( 'style' => 'max-width:48px;max-height:32px;object-fit:contain' ) );
            else echo '—';
            if ( get_post_meta( $id, 'show_in_marquee', true ) && ! showmakers_public_client_logo( $id ) ) echo '<br><span>Not shown: check logo, approval and publication.</span>';
            return;
        }
        if ( $key === 'client' ) {
            $client = absint( get_post_meta( $id, 'client', true ) );
            $value = $client ? get_the_title( $client ) : ( get_post_meta( $id, 'client_name', true ) ?: '—' );
        } elseif ( $key === 'services' ) {
            $names = wp_get_object_terms( $id, 'service', array( 'fields' => 'names' ) );
            $value = is_wp_error( $names ) ? '—' : implode( ', ', $names );
        } elseif ( $key === 'status' ) $value = get_post_status( $id );
        elseif ( $key === 'logo_status' ) $value = ucfirst( get_post_meta( $id, 'logo_status', true ) ?: 'pending' );
        elseif ( $key === 'modified' ) $value = get_post_modified_time( 'Y-m-d H:i', false, $id );
        elseif ( in_array( $key, array( 'featured', 'visible', 'show_in_marquee' ), true ) ) $value = get_post_meta( $id, $key, true ) ? 'Yes' : 'No';
        else $value = get_post_meta( $id, $key, true ) ?: ( $key === 'media_status' ? 'pending' : '0' );
        echo esc_html( $value );
    }, 10, 2 );
    add_filter( 'manage_edit-' . $type . '_sortable_columns', function ( $columns ) use ( $type ) { $key = $type === 'client' ? 'marquee_sort_order' : 'sort_order'; $columns['sm_' . $key] = 'sm_' . $key; return $columns; } );
}
add_action( 'pre_get_posts', function ( $query ) {
    if ( is_admin() && $query->is_main_query() && in_array( $query->get( 'orderby' ), array( 'sm_sort_order', 'sm_marquee_sort_order' ), true ) ) {
        if ( $query->get( 'orderby' ) === 'sm_marquee_sort_order' ) {
            // Include relationship-only Clients that do not yet have marquee metadata.
            $query->set( 'meta_query', array( 'relation' => 'OR',
                array( 'key' => 'marquee_sort_order', 'compare' => 'EXISTS', 'type' => 'NUMERIC' ),
                array( 'key' => 'marquee_sort_order', 'compare' => 'NOT EXISTS' ),
            ) );
        } else $query->set( 'meta_key', 'sort_order' );
        $query->set( 'orderby', 'meta_value_num' );
    }
} );

add_action( 'admin_notices', function () {
    $screen = get_current_screen();
    $id = absint( $_GET['post'] ?? 0 );
    if ( $screen && $screen->post_type === 'client' && $id && current_user_can( 'edit_post', $id )
        && get_post_meta( $id, 'show_in_marquee', true ) && ! showmakers_public_client_logo( $id ) ) {
        echo '<div class="notice notice-warning"><p>This client is not shown in Selected Clients. Publish the client and choose an approved, available logo. Any existing image restrictions must be resolved before it can appear.</p></div>';
    }
} );
