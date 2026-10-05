<?php
defined( 'ABSPATH' ) || exit;
$id = get_queried_object_id();
if ( ! showmakers_project_renderable( $id ) ) {
    global $wp_query;
    $wp_query->set_404();
    status_header( 404 );
    nocache_headers();
    get_template_part( '404' );
    return;
}
$project = get_post( $id );
$services = showmakers_project_services( $id );
$images = showmakers_renderable_project_images( $id );
$presentation = get_post_meta( $id, 'presentation', true );
if ( ! in_array( $presentation, array( 'digital', 'portrait', 'landscape' ), true ) ) $presentation = 'landscape';
$client = showmakers_project_client_name( $id );
$context = array( 'project' => $project->post_name );
if ( count( $services ) === 1 ) $context['service'] = $services[0]->slug;
$projects = showmakers_visible_projects();
$next = null;
foreach ( $projects as $index => $item ) {
    if ( $item->ID === $id && count( $projects ) > 1 ) $next = $projects[ ( $index + 1 ) % count( $projects ) ];
}
get_header();
?>
<div class="shell project-detail detail-<?php echo esc_attr( $presentation ); ?>">
<a class="text-link back-to-work" data-valid-services="<?php echo esc_attr( implode( ' ', wp_list_pluck( showmakers_visible_services(), 'slug' ) ) ); ?>" href="<?php echo esc_url( home_url( '/work/' ) ); ?>">Back to Work</a>
<div class="detail-heading"><span class="editorial-number"><?php echo esc_html( sprintf( '%02d', (int) get_post_meta( $id, 'sort_order', true ) ) ); ?></span>
<?php if ( $client && $client !== $project->post_title ) : ?><div class="project-client"><?php echo esc_html( $client ); ?></div><?php endif; ?>
<h1><?php echo esc_html( $project->post_title ); ?></h1><p><?php echo esc_html( get_post_meta( $id, 'short_summary', true ) ); ?></p>
<?php if ( $services ) : ?><div class="project-services"><span>Services</span><br>
<?php foreach ( $services as $index => $service ) : if ( $index ) echo ' · '; ?><a href="<?php echo esc_url( home_url( '/services/#' . $service->slug ) ); ?>"><?php echo esc_html( $service->name ); ?></a><?php endforeach; ?>
</div><?php endif; ?></div>
<?php
$video_mode = showmakers_project_media_type( $id ) === 'video';
$video = $video_mode ? showmakers_project_video( $id ) : null;
$poster = $video_mode ? showmakers_video_poster( $id ) : 0;
if ( $video && $poster ) :
    $poster_src = wp_get_attachment_image_src( $poster, 'full' );
    $width = $video['width'] ?: $poster_src[1]; $height = $video['height'] ?: $poster_src[2];
?>
<figure class="detail-hero detail-video" data-project-video style="--video-ratio:<?php echo absint( $width ); ?>/<?php echo absint( $height ); ?><?php if ( $height > $width ) echo ';max-width:min(100%,' . absint( min( $width, 540 ) ) . 'px)'; ?>">
<video id="project-video" width="<?php echo absint( $width ); ?>" height="<?php echo absint( $height ); ?>" poster="<?php echo esc_url( $poster_src[0] ); ?>" muted playsinline controls preload="metadata" aria-label="<?php echo esc_attr( $project->post_title . ' project video' ); ?>">
<source src="<?php echo esc_url( $video['url'] ); ?>" type="video/mp4">
Your browser does not support this video.
</video>
<div class="video-fallback" hidden><?php echo showmakers_cms_image( $poster, false ); ?></div>
<button class="video-toggle" type="button" aria-controls="project-video" hidden>Play video</button>
<span class="video-status screen-reader-text" role="status"></span>
</figure>
<?php elseif ( $video_mode && $poster ) : ?><figure class="detail-hero"><?php echo showmakers_cms_image( $poster, false ); ?></figure>
<?php else : ?><?php if ( isset( $images['hero_media'] ) ) : ?><figure class="detail-hero"><?php echo showmakers_cms_image( $images['hero_media'], false ); $caption = wp_get_attachment_caption( $images['hero_media'] ); if ( $caption ) : ?><figcaption><?php echo esc_html( $caption ); ?></figcaption><?php endif; ?></figure><?php endif; ?><?php endif; ?>
<?php
$extras = array_unique( array_filter( array_intersect_key( $images, array_flip( array( 'project_image_1', 'project_image_2', 'project_image_3', 'project_image_4', 'project_image_5' ) ) ), function ( $image ) use ( $images ) { return $image !== ( $images['hero_media'] ?? 0 ); } ) );
if ( $extras ) : ?><div class="detail-gallery"><?php foreach ( $extras as $image ) : ?><figure><?php echo showmakers_cms_image( $image ); $caption = wp_get_attachment_caption( $image ); if ( $caption ) : ?><figcaption><?php echo esc_html( $caption ); ?></figcaption><?php endif; ?></figure><?php endforeach; ?></div><?php endif; ?>
<?php $note = get_post_meta( $id, 'documentation_note', true ); if ( $note ) : ?><p class="detail-documentation"><?php echo esc_html( $note ); ?></p><?php endif; ?>
<div class="detail-route">
<?php if ( $next ) : ?><a class="arrow-link cta-nudge" href="<?php echo esc_url( get_permalink( $next ) ); ?>">Next Project<?php echo showmakers_arrow_icon( true ); ?></a><?php endif; ?>
<a class="arrow-link" <?php if ( ! $next ) echo 'style="margin-inline-start:auto"'; ?> href="<?php echo esc_url( add_query_arg( $context, home_url( '/contact/' ) ) ); ?>" data-event="project_start_inquiry">Start a project<?php echo showmakers_arrow_icon(); ?></a>
</div></div>
<?php get_footer(); ?>
