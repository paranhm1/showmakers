<?php
/** Narrow SEO adapter: native titles/robots/sitemaps, existing free ACF editor. */
defined( 'ABSPATH' ) || exit;
function showmakers_seo_local() {
    $host = strtolower( (string) wp_parse_url( home_url(), PHP_URL_HOST ) );
    return in_array( wp_get_environment_type(), array( 'local', 'development', 'staging' ), true ) || $host === 'localhost' || $host === '127.0.0.1' || str_ends_with( $host, '.local' );
}
function showmakers_seo_pages() {
    $pages = array( 'home' => absint( get_option( 'page_on_front' ) ) );
    foreach ( array( 'work', 'services', 'about', 'contact' ) as $slug ) {
        $page = get_page_by_path( $slug, OBJECT, 'page' );
        if ( $page && $page->post_status === 'publish' ) $pages[$slug] = $page->ID;
    }
    return array_filter( $pages, function ( $id ) { return get_post_status( $id ) === 'publish'; } );
}
function showmakers_seo_entity() {
    if ( is_404() || is_search() || is_feed() || is_preview() || is_attachment() ) return null;
    $pages = showmakers_seo_pages();
    if ( is_front_page() && isset( $pages['home'] ) ) return array( 'kind' => 'home', 'id' => $pages['home'], 'url' => home_url( '/' ) );
    if ( is_post_type_archive( 'project' ) && isset( $pages['work'] ) ) return array( 'kind' => 'work', 'id' => $pages['work'], 'url' => home_url( '/work/' ) );
    if ( is_singular( 'project' ) && showmakers_public_project( get_queried_object_id() ) ) return array( 'kind' => 'project', 'id' => get_queried_object_id(), 'url' => get_permalink( get_queried_object_id() ) );
    if ( is_page() ) foreach ( $pages as $kind => $id ) if ( get_queried_object_id() === $id ) return array( 'kind' => $kind, 'id' => $id, 'url' => $kind === 'home' ? home_url( '/' ) : home_url( '/' . $kind . '/' ) );
    return null;
}
function showmakers_seo_text( $text ) {
    return trim( preg_replace( '/\s+/u', ' ', sanitize_text_field( html_entity_decode( (string) $text, ENT_QUOTES, 'UTF-8' ) ) ) );
}
function showmakers_seo_metadata( $entity ) {
    $id = $entity['id'];
    $titles = array( 'home' => 'ShowMakers | Marketing Agency', 'work' => 'Our Work | ShowMakers', 'services' => 'Marketing Services | ShowMakers', 'about' => 'About ShowMakers | Marketing Agency', 'contact' => 'Contact ShowMakers' );
    $title = showmakers_seo_text( get_post_meta( $id, 'seo_title', true ) ) ?: ( $titles[$entity['kind']] ?? showmakers_seo_text( get_the_title( $id ) ) . ' | ShowMakers' );
    $description = showmakers_seo_text( get_post_meta( $id, 'seo_description', true ) );
    if ( $description === '' ) {
        $fields = array( 'home' => array( 'home_hero_supporting_copy' ), 'services' => array( 'services_page_intro' ), 'about' => array( 'about_hero_intro' ), 'contact' => array( 'contact_intro_line_1', 'contact_intro_line_2' ), 'project' => array( 'short_summary' ) );
        $parts = array();
        foreach ( $fields[$entity['kind']] ?? array() as $field ) $parts[] = get_post_meta( $id, $field, true );
        $description = showmakers_seo_text( implode( ' ', $parts ) );
        if ( $entity['kind'] === 'work' ) $description = 'Selected projects from ShowMakers.';
    }
    $image = absint( get_post_meta( $id, 'seo_social_image', true ) );
    if ( ! showmakers_seo_image( $image ) && $entity['kind'] === 'project' ) $image = absint( get_post_meta( $id, 'hero_media', true ) );
    $media = showmakers_seo_image( $image );
    if ( ! $media ) $media = array( 'url' => get_theme_file_uri( '/assets/images/showmakers-logo.webp' ), 'width' => 838, 'height' => 342, 'alt' => 'ShowMakers' );
    return array( 'title' => $title, 'description' => $description, 'image' => $media );
}
function showmakers_seo_image( $id ) {
    if ( ! showmakers_approved_media( $id ) || ! is_file( (string) get_attached_file( $id ) ) ) return null;
    $image = wp_get_attachment_image_src( $id, 'full' );
    if ( ! $image ) return null;
    return array( 'url' => $image[0], 'width' => $image[1], 'height' => $image[2], 'alt' => showmakers_seo_text( get_post_meta( $id, '_wp_attachment_image_alt', true ) ) );
}
add_filter( 'pre_get_document_title', function ( $title ) {
    $entity = showmakers_seo_entity();
    return $entity ? showmakers_seo_metadata( $entity )['title'] : $title;
} );
add_action( 'init', function () { remove_action( 'wp_head', 'rel_canonical' ); } );
add_filter( 'wp_robots', function ( $robots ) {
    if ( showmakers_seo_local() || ! get_option( 'blog_public' ) || ! showmakers_seo_entity() ) {
        unset( $robots['index'], $robots['follow'] );
        $robots['noindex'] = true;
        $robots['nofollow'] = true;
    }
    return $robots;
} );
add_filter( 'robots_txt', function ( $text ) {
    return showmakers_seo_local() || ! get_option( 'blog_public' ) ? "User-agent: *\nDisallow: /\n" : $text;
} );
add_action( 'wp_head', function () {
    $entity = showmakers_seo_entity();
    if ( ! $entity ) return;
    $meta = showmakers_seo_metadata( $entity );
    echo '<link rel="canonical" href="' . esc_url( $entity['url'] ) . '">' . "\n";
    if ( $meta['description'] !== '' ) echo '<meta name="description" content="' . esc_attr( $meta['description'] ) . '">' . "\n";
    $values = array( 'og:title' => $meta['title'], 'og:description' => $meta['description'], 'og:url' => $entity['url'], 'og:type' => 'website', 'og:site_name' => 'ShowMakers', 'og:image' => $meta['image']['url'], 'og:image:width' => $meta['image']['width'], 'og:image:height' => $meta['image']['height'], 'og:image:alt' => $meta['image']['alt'] );
    foreach ( $values as $key => $value ) if ( (string) $value !== '' ) echo '<meta property="' . esc_attr( $key ) . '" content="' . esc_attr( $value ) . '">' . "\n";
    $home = home_url( '/' );
    $organization = array( '@type' => 'Organization', '@id' => $home . '#organization', 'name' => 'ShowMakers', 'url' => $home, 'logo' => get_theme_file_uri( '/assets/images/showmakers-logo.webp' ) );
    foreach ( array( 'email' => 'contact_email', 'telephone' => 'phone', 'address' => 'address' ) as $key => $setting ) {
        $value = showmakers_site_setting( $setting );
        if ( $value !== '' ) $organization[$key] = $value;
    }
    $graph = array( '@context' => 'https://schema.org', '@graph' => array( $organization, array( '@type' => 'WebSite', '@id' => $home . '#website', 'name' => 'ShowMakers', 'url' => $home, 'publisher' => array( '@id' => $home . '#organization' ) ) ) );
    echo '<script type="application/ld+json">' . wp_json_encode( $graph, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}, 5 );
// One core sitemap provider, constrained to the actual approved public entities.
add_filter( 'wp_sitemaps_enabled', function ( $enabled ) { return $enabled && ! showmakers_seo_local(); } );
add_filter( 'wp_sitemaps_add_provider', function ( $provider, $name ) { return $name === 'posts' ? $provider : false; }, 10, 2 );
add_filter( 'wp_sitemaps_post_types', function ( $types ) { return array_intersect_key( $types, array_flip( array( 'page', 'project' ) ) ); } );
add_filter( 'wp_sitemaps_posts_query_args', function ( $args, $type ) {
    if ( $type === 'page' ) $args['post__in'] = array_values( showmakers_seo_pages() ) ?: array( 0 );
    return $args; // Existing Project eligibility filter remains authoritative.
}, 20, 2 );
function showmakers_legacy_routes() {
    return array(
        '/index.html' => '/', '/work.html' => '/work/', '/services.html' => '/services/', '/about.html' => '/about/', '/contact.html' => '/contact/',
        '/work/short-form-brand-content.html' => '/work/short-form-brand-content/', '/work/tid-group.html' => '/work/tid-group/', '/work/stories-in-the-moment.html' => '/work/stories-in-the-moment/', '/work/ravo-film.html' => '/work/ravo-film/', '/work/ai-assisted-content.html' => '/work/ai-assisted-content/',
    );
}
add_action( 'template_redirect', function () {
    if ( ! in_array( $_SERVER['REQUEST_METHOD'] ?? 'GET', array( 'GET', 'HEAD' ), true ) ) return;
    $path = wp_parse_url( $_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH );
    $routes = showmakers_legacy_routes();
    if ( ! isset( $routes[$path] ) ) return;
    $target = $routes[$path];
    $query = array();
    foreach ( array( 'service', 'from' ) as $key ) {
        $value = isset( $_GET[$key] ) && is_string( $_GET[$key] ) ? wp_unslash( $_GET[$key] ) : '';
        $term = get_term_by( 'slug', $value, 'service' );
        if ( $term && $term->slug === $value && get_term_meta( $term->term_id, 'visible', true ) && ( $key === 'service' && in_array( $target, array( '/work/', '/contact/' ), true ) || $key === 'from' && str_starts_with( $target, '/work/' ) && $target !== '/work/' ) ) $query[$key] = $value;
    }
    if ( $target === '/contact/' && isset( $_GET['service'] ) && $_GET['service'] === 'not-sure' ) $query['service'] = 'not-sure';
    if ( $target === '/contact/' && isset( $_GET['project'] ) && is_string( $_GET['project'] ) ) {
        $slug = wp_unslash( $_GET['project'] );
        $project = get_page_by_path( $slug, OBJECT, 'project' );
        if ( $project && $project->post_name === $slug && showmakers_public_project( $project->ID ) ) $query['project'] = $slug;
    }
    wp_safe_redirect( add_query_arg( $query, home_url( $target ) ), 301, 'ShowMakers' );
    exit;
}, 1 );
