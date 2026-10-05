<?php
/** Intentionally retained local, one-client importer. Never derives membership from Projects.
 * wp eval-file scripts/migrate_wp_clients.php <source-id> [verified-ravo-film]
 * The second argument is allowed only after explicit human confirmation of Ravo identity.
 */
if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) exit;
if ( wp_parse_url( home_url(), PHP_URL_HOST ) !== 'showmakers-local.local' || ! function_exists( 'update_field' ) ) WP_CLI::error( 'Authorized LocalWP and free ACF required.' );
$root = dirname( __DIR__ ); $key = $args[0] ?? ''; $source = null;
foreach ( json_decode( file_get_contents( $root . '/data/clients.json' ), true ) as $item ) if ( $item['id'] === $key ) $source = $item;
if ( ! $source || ! $source['published'] || $source['image'] !== 'assets/images/clients/' . $key . '.webp' || ! is_file( $root . '/' . $source['image'] ) ) WP_CLI::error( 'Choose one existing approved marquee entry.' );
if ( get_posts( array( 'post_type' => 'client', 'post_status' => 'any', 'numberposts' => -1, 'meta_key' => '_showmakers_marquee_source_id', 'meta_value' => $key ) ) ) WP_CLI::error( 'Already migrated; refusing to overwrite staff edits.' );
$client = null;
if ( $key === 'ravo' ) {
    if ( ( $args[1] ?? '' ) !== 'verified-ravo-film' ) WP_CLI::error( 'Ravo identity confirmation required before joining or creating a Client.' );
    $client = get_page_by_path( 'ravo-film', OBJECT, 'client' );
    if ( ! $client || $client->post_title !== 'Ravo Film' ) WP_CLI::error( 'Expected verified Client missing.' );
} else {
    $matches = array_filter( get_posts( array( 'post_type' => 'client', 'post_status' => 'any', 'numberposts' => -1 ) ), function ( $c ) use ( $source ) { return $c->post_title === $source['name']; } );
    if ( count( $matches ) > 1 ) WP_CLI::error( 'Ambiguous matching Clients.' );
    if ( $matches ) $client = reset( $matches );
    $slug = get_page_by_path( $key, OBJECT, 'client' );
    if ( $slug && ( ! $client || $slug->ID !== $client->ID ) ) WP_CLI::error( 'Conflicting Client slug; review identity first.' );
}
if ( $client && ( get_post_meta( $client->ID, 'client_logo', true ) || get_post_meta( $client->ID, 'show_in_marquee', true ) ) ) WP_CLI::error( 'Existing logo/membership; review before overwriting.' );
$keys = array();
foreach ( array( 'clients', 'media' ) as $group ) {
    $json = json_decode( file_get_contents( $root . '/wordpress/wp-content/plugins/showmakers-content/acf-json/group_showmakers_' . $group . '.json' ), true );
    foreach ( $json['fields'] as $f ) if ( ! empty( $f['name'] ) ) $keys[$group][$f['name']] = $f['key'];
}
$existing = get_posts( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'meta_key' => '_showmakers_source_asset', 'meta_value' => $source['image'], 'fields' => 'ids', 'numberposts' => -1 ) );
if ( count( $existing ) > 1 ) WP_CLI::error( 'Duplicate source attachment.' );
if ( $existing ) {
    $logo = $existing[0];
    if ( ! showmakers_client_logo_asset( $logo ) || hash_file( 'sha256', get_attached_file( $logo ) ) !== hash_file( 'sha256', $root . '/' . $source['image'] ) ) WP_CLI::error( 'Existing logo restriction/integrity mismatch.' );
} else {
    require_once ABSPATH . 'wp-admin/includes/file.php'; require_once ABSPATH . 'wp-admin/includes/media.php'; require_once ABSPATH . 'wp-admin/includes/image.php';
    $temporary = wp_tempnam( basename( $source['image'] ) );
    if ( ! copy( $root . '/' . $source['image'], $temporary ) ) WP_CLI::error( 'Cannot copy original approved logo.' );
    $logo = media_handle_sideload( array( 'name' => basename( $source['image'] ), 'tmp_name' => $temporary ), 0 );
    if ( is_wp_error( $logo ) ) WP_CLI::error( $logo->get_error_message() );
    update_post_meta( $logo, '_showmakers_source_asset', $source['image'] );
    update_post_meta( $logo, '_wp_attachment_image_alt', $source['name'] );
    update_field( $keys['media']['media_status'], 'approved', $logo );
    if ( hash_file( 'sha256', get_attached_file( $logo ) ) !== hash_file( 'sha256', $root . '/' . $source['image'] ) ) WP_CLI::error( 'Original artwork integrity mismatch.' );
}
$reused = (bool) $client;
$id = $client ? $client->ID : wp_insert_post( array( 'post_type' => 'client', 'post_title' => $source['name'], 'post_name' => $key, 'post_status' => 'draft' ), true );
if ( is_wp_error( $id ) ) WP_CLI::error( $id->get_error_message() );
foreach ( array( 'client_logo' => $logo, 'logo_status' => 'approved', 'show_in_marquee' => 1, 'marquee_sort_order' => $source['order'] ) as $name => $value ) update_field( $keys['clients'][$name], $value, $id );
update_post_meta( $id, '_showmakers_marquee_source_id', $key );
if ( ! $reused ) update_post_meta( $id, 'source_id', $key );
update_post_meta( $id, '_showmakers_logo_provenance', 'Existing approved 18-logo Home marquee; references/VERIFIED-CONTENT.md. No Project associations inferred.' );
wp_update_post( array( 'ID' => $id, 'post_status' => 'publish' ) );
WP_CLI::success( wp_json_encode( array( 'client' => $id, 'source' => $key, 'reused' => $reused, 'logo' => $logo, 'order' => $source['order'] ) ) );
