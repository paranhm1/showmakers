<?php
/** One short uploaded Project video; no global upload limits or transcoding. */
defined( 'ABSPATH' ) || exit;
function showmakers_project_media_type( $id ) {
    return get_post_meta( $id, 'project_media_type', true ) === 'video' ? 'video' : 'image';
}
function showmakers_video_file( $id ) {
    $id = absint( $id );
    if ( get_post_type( $id ) !== 'attachment' || get_post_mime_type( $id ) !== 'video/mp4' ) return null;
    $file = get_attached_file( $id );
    if ( ! $file || ! is_file( $file ) || strtolower( pathinfo( $file, PATHINFO_EXTENSION ) ) !== 'mp4' ) return null;
    static $cache = array();
    $key = $file . ':' . filemtime( $file ) . ':' . filesize( $file );
    if ( array_key_exists( $key, $cache ) ) return $cache[$key];
    require_once ABSPATH . 'wp-admin/includes/media.php';
    // Core's stored length rounds seconds. Read its raw parser value for the exact 10s limit.
    $duration = null;
    $capture = function ( $metadata, $path, $format, $data ) use ( &$duration, $file ) {
        if ( $path === $file && isset( $data['playtime_seconds'] ) && is_numeric( $data['playtime_seconds'] ) ) $duration = (float) $data['playtime_seconds'];
        return $metadata;
    };
    add_filter( 'wp_read_video_metadata', $capture, 10, 4 );
    $meta = wp_read_video_metadata( $file );
    remove_filter( 'wp_read_video_metadata', $capture, 10 );
    $mime = ( new finfo( FILEINFO_MIME_TYPE ) )->file( $file );
    if ( ! $meta || ! in_array( $mime, array( 'video/mp4', 'video/quicktime', 'application/mp4' ), true ) ) return $cache[$key] = null;
    return $cache[$key] = array( 'id' => $id, 'url' => wp_get_attachment_url( $id ), 'width' => absint( $meta['width'] ?? 0 ), 'height' => absint( $meta['height'] ?? 0 ), 'duration' => $duration );
}
function showmakers_project_video( $id ) {
    if ( showmakers_project_media_type( $id ) !== 'video' ) return null;
    $preview = is_preview() && get_queried_object_id() === (int) $id && current_user_can( 'edit_post', $id ) && get_post_type( $id ) === 'project' && get_post_status( $id ) === 'draft' && get_post_meta( $id, 'media_status', true ) === 'approved' && ! get_post_meta( $id, 'development_only', true ) && ! get_post_meta( $id, 'is_placeholder', true );
    if ( ! showmakers_public_project( $id ) && ! $preview ) return null;
    $attachment = absint( get_post_meta( $id, 'project_video', true ) );
    if ( ! showmakers_approved_media( $attachment ) ) return null;
    $video = showmakers_video_file( $attachment );
    return $video && ( $video['duration'] === null || $video['duration'] <= 10 ) ? $video : null;
}
function showmakers_video_poster( $id ) {
    $poster = absint( get_post_meta( $id, 'project_video_poster', true ) );
    return showmakers_approved_media( $poster ) && wp_attachment_is_image( $poster ) && is_file( (string) get_attached_file( $poster ) ) ? $poster : 0;
}
/** Pure validation shared by ACF and publication guard. Drafts may omit assets. */
function showmakers_video_errors( $values, $publishing = false ) {
    if ( ( $values['project_media_type'] ?? 'image' ) !== 'video' ) return array();
    $errors = array(); $file = null;
    if ( ! empty( $values['project_video'] ) ) {
        $file = showmakers_video_file( $values['project_video'] );
        if ( ! $file ) $errors['project_video'] = 'Choose a valid uploaded MP4 video.';
        elseif ( $file['duration'] !== null && $file['duration'] > 10 ) $errors['project_video'] = 'Project videos should be 10 seconds or shorter.';
    } elseif ( $publishing ) $errors['project_video'] = 'Choose a Project Video before publishing. You can save a Draft first.';
    $poster = absint( $values['project_video_poster'] ?? 0 );
    if ( $publishing && ( ! $poster || ! wp_attachment_is_image( $poster ) || ! showmakers_approved_media( $poster ) || ! is_file( (string) get_attached_file( $poster ) ) ) ) $errors['project_video_poster'] = 'Choose an approved Video Poster Image before publishing. You can save a Draft first.';
    if ( $publishing && ! empty( $values['project_video'] ) && ! showmakers_approved_media( $values['project_video'] ) ) $errors['project_video'] = $errors['project_video'] ?? 'Approve this video in the Media Library before publishing.';
    return $errors;
}
function showmakers_video_editor_values( $id, $provided = array() ) {
    $values = array();
    $submitted = isset( $_POST['acf'] ) && is_array( $_POST['acf'] ) ? wp_unslash( $_POST['acf'] ) : array();
    foreach ( array( 'project_media_type', 'project_video', 'project_video_poster' ) as $name ) {
        $key = 'field_showmakers_p_' . $name;
        $value = array_key_exists( $key, $submitted ) ? $submitted[$key] : ( $provided[$name] ?? get_post_meta( $id, $name, true ) );
        $values[$name] = is_scalar( $value ) ? ( $name === 'project_media_type' ? (string) $value : absint( $value ) ) : '';
    }
    return $values;
}
add_action( 'acf/validate_save_post', function () {
    $id = absint( $_POST['post_ID'] ?? $_POST['_acf_post_id'] ?? 0 );
    if ( get_post_type( $id ) !== 'project' && ( $_POST['post_type'] ?? '' ) !== 'project' ) return;
    $publishing = isset( $_POST['publish'] ) || in_array( $_POST['post_status'] ?? '', array( 'publish', 'future' ), true ) || ( get_post_status( $id ) === 'publish' && ! isset( $_POST['save'] ) );
    foreach ( showmakers_video_errors( showmakers_video_editor_values( $id ), $publishing ) as $name => $error ) acf_add_validation_error( 'acf[field_showmakers_p_' . $name . ']', $error );
} );
// Server fallback also covers REST/CLI/core publication paths that bypass ACF validation.
add_filter( 'wp_insert_post_data', function ( $data, $postarr ) {
    if ( $data['post_type'] === 'project' && in_array( $data['post_status'], array( 'publish', 'future' ), true ) && showmakers_video_errors( showmakers_video_editor_values( absint( $postarr['ID'] ?? 0 ), $postarr['meta_input'] ?? array() ), true ) ) $data['post_status'] = 'draft';
    return $data;
}, 20, 2 );
add_action( 'admin_notices', function () {
    $id = absint( $_GET['post'] ?? 0 );
    if ( ! $id || get_post_type( $id ) !== 'project' || ! current_user_can( 'edit_post', $id ) ) return;
    $values = showmakers_video_editor_values( $id );
    foreach ( showmakers_video_errors( $values, true ) as $error ) echo '<div class="notice notice-warning"><p>' . esc_html( $error ) . '</p></div>';
    $file = ! empty( $values['project_video'] ) ? showmakers_video_file( $values['project_video'] ) : null;
    if ( $values['project_media_type'] === 'video' && $file && $file['duration'] === null ) echo '<div class="notice notice-warning"><p>Video duration could not be checked. Confirm it is 10 seconds or shorter before publishing.</p></div>';
} );
