<?php
/** Read-only Phase 4 local pilot checks: wp eval-file scripts/check_wp_pilot.php. */
if ( ! defined( 'ABSPATH' ) || ! defined( 'WP_CLI' ) || ! WP_CLI ) exit;
if ( wp_parse_url( home_url(), PHP_URL_HOST ) !== 'showmakers-local.local' ) WP_CLI::error( 'Local pilot only.' );
function showmakers_pilot_assert( $condition, $label ) { if ( ! $condition ) WP_CLI::error( $label ); WP_CLI::log( 'PASS: ' . $label ); }
$root = dirname( __DIR__ );
$project = get_page_by_path( 'ravo-film', OBJECT, 'project' );
$client = get_page_by_path( 'ravo-film', OBJECT, 'client' );
showmakers_pilot_assert( $project && $client, 'Ravo Project and Client exist' );
showmakers_pilot_assert( ! get_post_type_object( 'client' )->publicly_queryable && ! get_post_type_object( 'client' )->has_archive, 'Client has no public single/archive' );
showmakers_pilot_assert( (int) get_field( 'client', $project->ID ) === $client->ID, 'Stable Client ID relationship' );
$slugs = wp_get_object_terms( $project->ID, 'service', array( 'fields' => 'slugs' ) );
showmakers_pilot_assert( $slugs === array( 'website-digital-solutions' ), 'Only verified service assigned' );
showmakers_pilot_assert( count( get_terms( array( 'taxonomy' => 'service', 'hide_empty' => false ) ) ) === 8, 'Exactly eight structural terms' );
showmakers_pilot_assert( get_post_meta( $project->ID, 'short_summary', true ) === 'ShowMakers created the Ravo Film website.', 'Verified summary' );
showmakers_pilot_assert( ! get_post_meta( $project->ID, 'project_year', true ) && ! get_post_meta( $project->ID, 'featured', true ), 'Unknown year empty and Featured false' );
showmakers_pilot_assert( (int) get_post_meta( $project->ID, 'sort_order', true ) === 4, 'Source ordering retained' );
$image = (int) get_post_meta( $project->ID, 'hero_media', true );
showmakers_pilot_assert( $image === (int) get_post_meta( $project->ID, 'listing_thumbnail', true ) && showmakers_approved_media( $image ), 'One approved image reused for hero/listing' );
$metadata = wp_get_attachment_metadata( $image );
showmakers_pilot_assert( $metadata['width'] === 789 && $metadata['height'] === 1276, 'Native 789 × 1276 screenshot preserved' );
showmakers_pilot_assert( hash_file( 'sha256', get_attached_file( $image ) ) === hash_file( 'sha256', $root . '/assets/images/work/ravo-film.webp' ), 'Original attachment unchanged' );
showmakers_pilot_assert( get_post_meta( $project->ID, 'listing_fit', true ) === 'contain', 'Listing uses contain' );
for ( $slot = 1; $slot <= 5; ++$slot ) showmakers_pilot_assert( ! get_post_meta( $project->ID, 'project_image_' . $slot, true ), 'Unused additional image ' . $slot . ' empty' );
showmakers_pilot_assert( ! get_post_meta( $client->ID, 'client_logo', true ) && ! get_post_meta( $client->ID, 'website_url', true ), 'No undocumented logo/URL association' );
foreach ( array( 'project', 'client' ) as $type ) {
    $all = get_posts( array( 'post_type' => $type, 'post_status' => array( 'draft', 'publish', 'pending', 'private' ), 'posts_per_page' => -1, 'fields' => 'ids' ) );
    showmakers_pilot_assert( count( $all ) === 1, 'Only one real ' . $type . ' migrated' );
}
$expected = get_post_status( $project->ID ) === 'publish' ? 1 : 0;
showmakers_pilot_assert( count( showmakers_visible_projects() ) === $expected, 'Draft excluded / published project counted' );
showmakers_pilot_assert( showmakers_service_project_count( 'website-digital-solutions' ) === $expected, 'Relationship-derived service count' );
showmakers_pilot_assert( showmakers_service_project_count( 'media-production' ) === 0, 'Unmigrated service has zero work' );
// Read-only fault injection: no change to real records, permissions or attachment files.
foreach ( array( 'pending', 'restricted' ) as $state ) {
    $guard = function ( $value, $id, $key ) use ( $project, $state ) { return $id === $project->ID && $key === 'media_status' ? $state : $value; };
    add_filter( 'get_post_metadata', $guard, 10, 3 );
    try {
        showmakers_pilot_assert( ! showmakers_public_project( $project->ID ) && ! showmakers_renderable_project_images( $project->ID ), $state . ' project cannot render media' );
    } finally { remove_filter( 'get_post_metadata', $guard, 10 ); }
    $guard = function ( $value, $id, $key ) use ( $image, $state ) { return $id === $image && $key === 'media_status' ? $state : $value; };
    add_filter( 'get_post_metadata', $guard, 10, 3 );
    try { showmakers_pilot_assert( showmakers_cms_image( $image ) === '', $state . ' attachment omitted by renderer' ); }
    finally { remove_filter( 'get_post_metadata', $guard, 10 ); }
}
foreach ( array( 'is_placeholder', 'development_only' ) as $flag ) {
    $guard = function ( $value, $id, $key ) use ( $project, $flag ) { return $id === $project->ID && $key === $flag ? '1' : $value; };
    add_filter( 'get_post_metadata', $guard, 10, 3 );
    try { showmakers_pilot_assert( ! showmakers_public_project( $project->ID ), $flag . ' excluded from public eligibility' ); }
    finally { remove_filter( 'get_post_metadata', $guard, 10 ); }
}
showmakers_pilot_assert( ! get_post_type_object( 'project' )->query_var && ! get_taxonomy( 'service' )->query_var, 'Inquiry query keys do not hijack WordPress routes' );
showmakers_pilot_assert( get_post_status( $project->ID ) === 'draft' || get_permalink( $project ) === home_url( '/work/ravo-film/' ), 'Clean public permalink after publication' );
WP_CLI::success( 'Read-only pilot validation complete: project ' . $project->ID . ', client ' . $client->ID . ', attachment ' . $image . ', status ' . get_post_status( $project->ID ) );
