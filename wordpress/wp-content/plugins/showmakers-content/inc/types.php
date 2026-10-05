<?php
defined( 'ABSPATH' ) || exit;
function showmakers_service_definitions() {
    return array(
        'business-consulting' => 'Business Consulting',
        'social-media-marketing' => 'Social Media Marketing',
        'media-production' => 'Media Production',
        'ai-enhanced-content-production' => 'AI-Enhanced Content Production',
        'website-digital-solutions' => 'Website & Digital Solutions',
        'event-management' => 'Event Management',
        'seo-sem' => 'SEO & SEM',
        'influencer-marketing' => 'Influencer Marketing',
    );
}
function showmakers_register_content() {
    register_post_type( 'project', array(
        'labels' => array( 'name' => __( 'Projects', 'showmakers-content' ), 'singular_name' => __( 'Project', 'showmakers-content' ), 'add_new_item' => __( 'Add Project', 'showmakers-content' ), 'edit_item' => __( 'Edit Project', 'showmakers-content' ) ),
        'public' => true, 'show_in_rest' => true, 'has_archive' => 'work',
        // Keep ?project= as inquiry context; clean singles use post_type/name rewrites.
        'query_var' => false,
        'rewrite' => array( 'slug' => 'work', 'with_front' => false ),
        'supports' => array( 'title', 'thumbnail', 'revisions' ),
        'capability_type' => array( 'project', 'projects' ), 'map_meta_cap' => true,
        'menu_icon' => 'dashicons-portfolio',
    ) );
    register_post_type( 'client', array(
        'labels' => array( 'name' => __( 'Clients', 'showmakers-content' ), 'singular_name' => __( 'Client', 'showmakers-content' ), 'add_new_item' => __( 'Add Client', 'showmakers-content' ), 'edit_item' => __( 'Edit Client', 'showmakers-content' ) ),
        'public' => false, 'show_ui' => true, 'show_in_rest' => false,
        'publicly_queryable' => false, 'exclude_from_search' => true,
        'has_archive' => false, 'rewrite' => false, 'query_var' => false,
        'supports' => array( 'title', 'revisions' ),
        'capability_type' => array( 'client', 'clients' ), 'map_meta_cap' => true,
        'menu_icon' => 'dashicons-businessperson',
    ) );
    register_taxonomy( 'service', array( 'project' ), array(
        'labels' => array( 'name' => __( 'Services', 'showmakers-content' ), 'singular_name' => __( 'Service', 'showmakers-content' ), 'add_new_item' => __( 'Add Service', 'showmakers-content' ), 'edit_item' => __( 'Edit Service', 'showmakers-content' ), 'search_items' => __( 'Search Services', 'showmakers-content' ), 'all_items' => __( 'All Services', 'showmakers-content' ) ),
        'public' => false, 'show_ui' => true, 'show_in_rest' => true,
        'hierarchical' => false, 'rewrite' => false, 'query_var' => false,
        'meta_box_cb' => false,
        'capabilities' => array( 'manage_terms' => 'manage_showmakers_services', 'edit_terms' => 'edit_showmakers_services', 'delete_terms' => 'delete_showmakers_services', 'assign_terms' => 'assign_showmakers_services' ),
    ) );
    // Typed private meta; public REST returns core project data, never ACF/rights notes.
    foreach ( array( 'project', 'client' ) as $type ) {
        register_post_meta( $type, 'sort_order', array( 'type' => 'integer', 'single' => true, 'default' => 0, 'show_in_rest' => false, 'revisions_enabled' => true, 'sanitize_callback' => 'absint', 'auth_callback' => function ( $allowed, $key, $id ) { return current_user_can( 'edit_post', $id ); } ) );
    }
}
add_action( 'init', 'showmakers_register_content' );
function showmakers_content_activate() {
    showmakers_register_content();
    $admin = get_role( 'administrator' );
    if ( $admin ) {
        foreach ( array( 'project', 'client' ) as $type ) {
            foreach ( (array) get_post_type_object( $type )->cap as $cap ) $admin->add_cap( $cap );
        }
        foreach ( array( 'manage', 'edit', 'delete', 'assign' ) as $action ) $admin->add_cap( $action . '_showmakers_services' );
    }
    $order = 1;
    foreach ( showmakers_service_definitions() as $slug => $name ) {
        $term = get_term_by( 'slug', $slug, 'service' );
        if ( ! $term ) {
            $result = wp_insert_term( $name, 'service', array( 'slug' => $slug ) );
            if ( is_wp_error( $result ) ) continue;
            $id = $result['term_id'];
            update_term_meta( $id, 'sort_order', $order );
            update_term_meta( $id, 'visible', 1 );
        }
        ++$order;
    }
    flush_rewrite_rules();
}
add_filter( 'pre_insert_term', function ( $term, $taxonomy, $args ) {
    if ( $taxonomy === 'service' && ! isset( showmakers_service_definitions()[ $args['slug'] ?? sanitize_title( $term ) ] ) ) {
        return new WP_Error( 'showmakers_service_locked', __( 'Use one of the eight approved services.', 'showmakers-content' ) );
    }
    return $term;
}, 10, 3 );
add_filter( 'wp_update_term_data', function ( $data, $id, $taxonomy ) {
    if ( $taxonomy === 'service' ) {
        $term = get_term( $id, $taxonomy );
        if ( $term && ! is_wp_error( $term ) ) $data['slug'] = $term->slug;
    }
    return $data;
}, 10, 3 );
