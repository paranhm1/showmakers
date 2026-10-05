<?php
/** Read-only local Client marquee reconciliation and permission/relationship fault injection. */
if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) exit;
if ( wp_parse_url( home_url(), PHP_URL_HOST ) !== 'showmakers-local.local' ) WP_CLI::error( 'Local only.' );
function sm_client_assert( $value, $message ) { if ( ! $value ) WP_CLI::error( $message ); WP_CLI::log( 'PASS: ' . $message ); }
$root = dirname( __DIR__ ); $sources = json_decode( file_get_contents( $root . '/data/clients.json' ), true );
$clients = showmakers_marquee_clients();
sm_client_assert( count( $clients ) === 18 && (int) wp_count_posts( 'client' )->publish === 19, '18 curated marquee Clients; 19 published Clients total' );
foreach ( $clients as $i => $client ) {
    $source = $sources[$i]; $logo = showmakers_public_client_logo( $client->ID );
    sm_client_assert( get_post_meta( $client->ID, '_showmakers_marquee_source_id', true ) === $source['id'] && (int) get_post_meta( $client->ID, 'marquee_sort_order', true ) === $source['order'], 'Exact source membership and order: ' . $source['id'] );
    sm_client_assert( get_post_meta( $logo, '_showmakers_source_asset', true ) === $source['image'] && get_post_meta( $logo, '_wp_attachment_image_alt', true ) === $source['name'] && hash_file( 'sha256', get_attached_file( $logo ) ) === hash_file( 'sha256', $root . '/' . $source['image'] ), 'Approved original artwork/alt: ' . $source['id'] );
}
$ravo = get_page_by_path( 'ravo-film', OBJECT, 'client' ); $tid = get_page_by_path( 'tid-group', OBJECT, 'client' );
sm_client_assert( $ravo->ID === 17 && $tid->ID === 23 && ! showmakers_public_client_logo( $tid->ID ), 'Existing Ravo reused; existing TID never auto-added' );
$samsung = $clients[0];
sm_client_assert( ! get_posts( array( 'post_type' => 'project', 'post_status' => 'any', 'meta_key' => 'client', 'meta_value' => $samsung->ID ) ) && showmakers_public_client_logo( $samsung->ID ), 'Client with zero Projects renders approved logo' );
$logo = showmakers_public_client_logo( $ravo->ID );
foreach ( array( 'show_in_marquee' => '0', 'logo_status' => 'pending', 'client_logo' => '0' ) as $key => $value ) {
    $filter = function ( $old, $id, $meta ) use ( $ravo, $key, $value ) { return $id === $ravo->ID && $meta === $key ? $value : $old; };
    add_filter( 'get_post_metadata', $filter, 10, 3 );
    try {
        sm_client_assert( ! showmakers_public_client_logo( $ravo->ID ) && count( showmakers_marquee_clients() ) === 17, 'Safely omit: ' . $key );
        $project = get_page_by_path( 'ravo-film', OBJECT, 'project' );
        sm_client_assert( showmakers_public_project( $project->ID ) && (int) get_post_meta( $project->ID, 'client', true ) === $ravo->ID && showmakers_project_images( $project->ID ), 'Ravo Project/relationship/media survive omitted logo' );
        ob_start(); get_template_part( 'template-parts/clients-marquee' ); $html = ob_get_clean();
        sm_client_assert( substr_count( $html, '<li>' ) === 17 && strpos( $html, 'alt="Ravo"' ) === false, 'No empty/broken logo slot' );
    } finally { remove_filter( 'get_post_metadata', $filter, 10 ); }
}
$filter = function ( $old, $id, $key ) use ( $ravo ) { return $id === $ravo->ID && $key === 'logo_status' ? 'restricted' : $old; };
add_filter( 'get_post_metadata', $filter, 10, 3 );
try { sm_client_assert( ! showmakers_public_client_logo( $ravo->ID ), 'Restricted Client logo excluded' ); } finally { remove_filter( 'get_post_metadata', $filter, 10 ); }
foreach ( array( 'pending', 'restricted' ) as $state ) {
    $filter = function ( $old, $id, $key ) use ( $logo, $state ) { return $id === $logo && $key === 'media_status' ? $state : $old; };
    add_filter( 'get_post_metadata', $filter, 10, 3 );
    try { sm_client_assert( ! showmakers_public_client_logo( $ravo->ID ), 'Explicit attachment restriction respected: ' . $state ); } finally { remove_filter( 'get_post_metadata', $filter, 10 ); }
}
// Newly uploaded logo may rely on Client approval; this never approves it for a Project.
$filter = function ( $old, $id, $key ) use ( $logo ) { return $id === $logo && $key === 'media_status' ? '' : $old; };
add_filter( 'get_post_metadata', $filter, 10, 3 );
try { sm_client_assert( showmakers_public_client_logo( $ravo->ID ) === $logo && ! showmakers_approved_media( $logo ), 'Client logo approval does not grant Project attachment approval' ); } finally { remove_filter( 'get_post_metadata', $filter, 10 ); }
$project = get_page_by_path( 'ravo-film', OBJECT, 'project' );
$filter = function ( $old, $id, $key ) use ( $project ) { return $id === $project->ID && $key === 'media_status' ? 'restricted' : $old; };
add_filter( 'get_post_metadata', $filter, 10, 3 );
try { sm_client_assert( showmakers_public_client_logo( $ravo->ID ) && ! showmakers_public_project( $project->ID ) && ! showmakers_project_images( $project->ID ), 'Approved logo cannot publish restricted Project media' ); } finally { remove_filter( 'get_post_metadata', $filter, 10 ); }
$filter = function ( $old, $id, $key ) use ( $samsung ) { return $id === $samsung->ID && $key === 'marquee_sort_order' ? '100' : $old; };
add_filter( 'get_post_metadata', $filter, 10, 3 );
try { $sorted = showmakers_marquee_clients(); sm_client_assert( end( $sorted )->ID === $samsung->ID, 'Numeric marquee order independent of Project order' ); } finally { remove_filter( 'get_post_metadata', $filter, 10 ); }
sm_client_assert( count( showmakers_marquee_clients() ) === 18 && count( showmakers_visible_projects() ) === 5 && count( showmakers_visible_services() ) === 8, 'All fault injection removed; approved state intact' );
WP_CLI::success( 'Read-only Client marquee checks complete.' );
