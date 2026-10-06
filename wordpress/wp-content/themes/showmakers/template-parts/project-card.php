<?php
defined( 'ABSPATH' ) || exit;
$project = $args['project'];
if ( ! showmakers_public_project( $project->ID ) ) return;
$services = showmakers_project_services( $project->ID );
$images = showmakers_renderable_project_images( $project->ID );
$fit = get_post_meta( $project->ID, 'listing_fit', true ) === 'contain' ? 'contain' : 'cover';
?>
<article class="portfolio-project" id="<?php echo esc_attr( $project->post_name ); ?>" data-services="<?php echo esc_attr( implode( ' ', wp_list_pluck( $services, 'slug' ) ) ); ?>" data-placeholder="false" data-client="<?php echo esc_attr( showmakers_project_client_name( $project->ID ) ); ?>">
<a class="portfolio-card" href="<?php echo esc_url( get_permalink( $project ) ); ?>" data-event="project_open" data-project="<?php echo esc_attr( $project->post_name ); ?>">
<div class="portfolio-media fit-<?php echo esc_attr( $fit ); ?>"><?php if ( isset( $images['listing_thumbnail'] ) ) echo showmakers_cms_image( $images['listing_thumbnail'], $args['index'] > 2 ); ?></div>
<div class="portfolio-info"><div class="portfolio-title"><h2><?php echo esc_html( $project->post_title ); ?></h2><span class="project-arrow" aria-hidden="true"><?php echo showmakers_arrow_icon( true ); ?></span></div>
<div class="portfolio-meta"><p><?php echo esc_html( implode( ' · ', wp_list_pluck( $services, 'name' ) ) ); ?></p></div></div>
</a></article>
