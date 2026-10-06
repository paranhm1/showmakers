<?php
/** Local one-time service content seed; no Projects, Client records or uploads modified. */
if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) exit;
if ( wp_parse_url( home_url(), PHP_URL_HOST ) !== 'showmakers-local.local' || ! function_exists( 'update_field' ) ) WP_CLI::error( 'Authorized LocalWP and free ACF required.' );
$root = dirname( __DIR__ );
$sources = json_decode( file_get_contents( $root . '/data/services.json' ), true );
$definition = json_decode( file_get_contents( $root . '/wordpress/wp-content/plugins/showmakers-content/acf-json/group_showmakers_services.json' ), true );
$keys = array(); foreach ( $definition['fields'] as $field ) if ( ! empty( $field['name'] ) ) $keys[$field['name']] = $field['key'];
$approved = array_keys( showmakers_service_definitions() );
if ( count( $sources ) !== 8 || array_column( $sources, 'slug' ) !== $approved ) WP_CLI::error( 'Source term identities/order mismatch.' );
$maps = array(); $backup = array();
// Validate every field/media mapping before any term write. Existing editorial content is never overwritten.
foreach ( $sources as $source ) {
    $term = get_term_by( 'slug', $source['slug'], 'service' );
    if ( ! $term || html_entity_decode( $term->name, ENT_QUOTES, 'UTF-8' ) !== $source['title'] || get_term_meta( $term->term_id, '_showmakers_service_source', true ) ) WP_CLI::error( 'Missing/mismatched/already migrated term: ' . $source['slug'] );
    foreach ( $keys as $name => $key ) if ( ! in_array( $name, array( 'sort_order', 'visible' ), true ) && get_term_meta( $term->term_id, $name, true ) !== '' ) WP_CLI::error( 'Existing staff content: ' . $source['slug'] . '/' . $name );
    if ( count( $source['media'] ) > 2 ) WP_CLI::error( 'More than two images: review required.' );
    foreach ( $source['subsections'] as $passage ) {
        if ( ! in_array( $passage['title'], array( 'Facebook', 'Instagram', 'XiaoHongShu', 'TikTok' ), true ) ) WP_CLI::error( 'Unreviewed platform passage.' );
    }
    $ids = array();
    foreach ( $source['media'] as $item ) {
        if ( ! in_array( $item['src'], array( 'assets/images/work/brand-content.webp', 'assets/images/work/short-form.webp', 'assets/images/work/ai-content.webp', 'assets/images/work/tid-home.webp', 'assets/images/work/tid-detail.webp' ), true ) ) WP_CLI::error( 'Unreviewed image.' );
        $matches = get_posts( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'meta_key' => '_showmakers_source_asset', 'meta_value' => $item['src'], 'fields' => 'ids', 'numberposts' => -1 ) );
        if ( count( $matches ) !== 1 || ! showmakers_approved_media( $matches[0] ) || hash_file( 'sha256', get_attached_file( $matches[0] ) ) !== hash_file( 'sha256', $root . '/' . $item['src'] ) ) WP_CLI::error( 'Expected approved original attachment missing.' );
        $ids[] = $matches[0];
    }
    $maps[$source['slug']] = array( 'term' => $term, 'images' => $ids );
    $backup[$term->term_id] = get_term_meta( $term->term_id );
}
$backup_path = wp_tempnam( 'showmakers-phase6-term-metadata.json' );
if ( file_put_contents( $backup_path, wp_json_encode( $backup, JSON_PRETTY_PRINT ) ) === false ) WP_CLI::error( 'Cannot save local term metadata checkpoint.' );
WP_CLI::log( 'Local term metadata checkpoint: ' . $backup_path );
foreach ( $sources as $source ) {
    $id = $maps[$source['slug']]['term']->term_id;
    $fields = array( 'service_intro' => $source['shortDescription'], 'service_description' => $source['description'], 'capabilities' => implode( "\n", $source['deliverables'] ), 'platforms' => implode( "\n", $source['platforms'] ), 'formats' => implode( "\n", $source['formats'] ?? array() ), 'sort_order' => $source['order'], 'visible' => (int) $source['published'] );
    foreach ( $source['media'] as $index => $item ) {
        $field = $index === 0 ? 'service_media' : 'service_media_2';
        $fields[$field] = $maps[$source['slug']]['images'][$index];
        $fields[$field . '_caption'] = $item['caption'];
    }
    foreach ( $source['subsections'] as $passage ) {
        $platform = array_search( $passage['title'], array( 'facebook' => 'Facebook', 'instagram' => 'Instagram', 'xiaohongshu' => 'XiaoHongShu', 'tiktok' => 'TikTok' ), true );
        if ( $platform === false ) WP_CLI::error( 'Unreviewed platform passage.' );
        $fields['platform_' . $platform . '_description'] = $passage['text'];
    }
    foreach ( $fields as $name => $value ) update_field( $keys[$name], $value, 'service_' . $id );
    update_term_meta( $id, '_showmakers_service_source', $source['id'] );
    update_term_meta( $id, '_showmakers_provenance', wp_json_encode( $source['source'] ) );
    WP_CLI::log( wp_json_encode( array( 'term_id' => $id, 'slug' => $source['slug'], 'order' => $source['order'], 'images' => $maps[$source['slug']]['images'] ) ) );
}
WP_CLI::success( 'Eight existing Services populated; no project relationships or attachment metadata changed.' );
