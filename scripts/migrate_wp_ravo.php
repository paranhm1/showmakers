<?php
/** Explicit one-record LOCAL pilot. Run with LocalWP: wp eval-file scripts/migrate_wp_ravo.php. */
if ( ! defined( 'ABSPATH' ) || ! defined( 'WP_CLI' ) || ! WP_CLI ) exit;
if ( wp_parse_url( home_url(), PHP_URL_HOST ) !== 'showmakers-local.local' ) WP_CLI::error( 'Local pilot only.' );
if ( ! function_exists( 'update_field' ) || ! function_exists( 'showmakers_service_definitions' ) ) WP_CLI::error( 'Activate free ACF and ShowMakers Content first.' );
$root = dirname( __DIR__ );
$records = json_decode( file_get_contents( $root . '/data/projects.json' ), true );
$source = null;
foreach ( $records as $record ) if ( $record['id'] === 'ravo-film' ) $source = $record;
if ( ! $source || $source['services'] !== array( 'website-digital-solutions' ) || $source['mediaStatus'] !== 'approved' ) WP_CLI::error( 'Verified Ravo source mismatch.' );
$term = get_term_by( 'slug', 'website-digital-solutions', 'service' );
if ( ! $term ) WP_CLI::error( 'Expected existing service term is missing; do not create a duplicate.' );
foreach ( array( 'project', 'client' ) as $type ) {
    $existing = get_posts( array( 'post_type' => $type, 'post_status' => array( 'draft', 'publish', 'private', 'pending', 'trash' ), 'meta_key' => 'source_id', 'meta_value' => 'ravo-film', 'fields' => 'ids' ) );
    $slug = get_page_by_path( 'ravo-film', OBJECT, $type );
    if ( $existing || $slug ) WP_CLI::error( 'Ravo record already exists: refusing to overwrite staff edits. Inspect IDs before another run.' );
}
// Resolve stable ACF field keys from the versioned free JSON, not inferred key names.
$keys = array();
foreach ( array( 'projects', 'clients', 'media' ) as $group ) {
    $definition = json_decode( file_get_contents( $root . '/wordpress/wp-content/plugins/showmakers-content/acf-json/group_showmakers_' . $group . '.json' ), true );
    foreach ( $definition['fields'] as $field ) if ( ! empty( $field['name'] ) ) $keys[$group][$field['name']] = $field['key'];
}
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';
$relative = 'assets/images/work/ravo-film.webp';
if ( $source['listingThumbnail']['src'] !== $relative || $source['heroMedia']['src'] !== $relative ) WP_CLI::error( 'Unexpected source media.' );
$attachment = get_posts( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'meta_key' => '_showmakers_source_asset', 'meta_value' => $relative, 'fields' => 'ids', 'numberposts' => 1 ) );
if ( $attachment ) $attachment = $attachment[0];
else {
    $temporary = wp_tempnam( 'ravo-film.webp' );
    if ( ! copy( $root . '/' . $relative, $temporary ) ) WP_CLI::error( 'Unable to copy approved source.' );
    $attachment = media_handle_sideload( array( 'name' => 'ravo-film.webp', 'tmp_name' => $temporary ), 0, 'Ravo Film website screenshot' );
    if ( is_wp_error( $attachment ) ) { @unlink( $temporary ); WP_CLI::error( $attachment->get_error_message() ); }
    update_post_meta( $attachment, '_showmakers_source_asset', $relative );
    update_post_meta( $attachment, '_wp_attachment_image_alt', $source['heroMedia']['alt'] );
    update_field( $keys['media']['media_status'], 'approved', $attachment );
}
if ( ! showmakers_approved_media( $attachment ) || hash_file( 'sha256', get_attached_file( $attachment ) ) !== hash_file( 'sha256', $root . '/' . $relative ) ) WP_CLI::error( 'Attachment approval/original integrity mismatch.' );
$client = wp_insert_post( array( 'post_type' => 'client', 'post_status' => 'publish', 'post_title' => 'Ravo Film', 'post_name' => 'ravo-film' ), true );
if ( is_wp_error( $client ) ) WP_CLI::error( $client->get_error_message() );
update_post_meta( $client, 'source_id', 'ravo-film' );
// Ravo marquee logo is not documented as Ravo Film: deliberately no logo/URL join.
foreach ( array( 'visible' => 1, 'sort_order' => 0, 'logo_status' => 'pending' ) as $name => $value ) update_field( $keys['clients'][$name], $value, $client );
$project = wp_insert_post( array( 'post_type' => 'project', 'post_status' => 'draft', 'post_title' => $source['title'], 'post_name' => $source['slug'] ), true );
if ( is_wp_error( $project ) ) WP_CLI::error( $project->get_error_message() );
foreach ( array( 'client' => $client, 'short_summary' => $source['summary'], 'sort_order' => $source['order'], 'services' => array( (int) $term->term_id ), 'listing_thumbnail' => $attachment, 'listing_fit' => 'contain', 'hero_media' => $attachment, 'featured' => 0, 'media_status' => 'approved' ) as $name => $value ) update_field( $keys['projects'][$name], $value, $project );
// Source-only metadata: no extra taxonomy or layout controls. Public documentation != private rights note.
update_post_meta( $project, 'source_id', $source['id'] );
update_post_meta( $project, 'presentation', $source['presentation'] );
update_post_meta( $project, 'documentation_note', $source['documentationNote'] );
update_post_meta( $project, '_showmakers_provenance', $source['source'] );
set_post_thumbnail( $project, $attachment );
wp_update_post( array( 'ID' => $attachment, 'post_parent' => $project ) );
WP_CLI::log( wp_json_encode( array( 'project_id' => $project, 'client_id' => $client, 'service_id' => $term->term_id, 'attachment_id' => $attachment, 'status' => 'draft', 'preview' => get_preview_post_link( $project ) ), JSON_PRETTY_PRINT ) );
