<?php
/** Exclusive CMS archive: never merge JSON projects into WordPress results. */
defined( 'ABSPATH' ) || exit;
$projects = showmakers_visible_projects();
$services = showmakers_visible_services();
get_header();
?>
<div class="page-intro-surface"><div class="shell page-intro"><h1>Work</h1><p>Selected projects from ShowMakers.</p></div></div>
<div class="shell work-index">
<div class="work-filters" data-valid-services="<?php echo esc_attr( implode( ' ', wp_list_pluck( $services, 'slug' ) ) ); ?>" role="group" aria-label="Filter projects by service" hidden>
<button type="button" data-event="work_filter" data-service="all" aria-pressed="true">All <span><?php echo esc_html( sprintf( '%02d', count( $projects ) ) ); ?></span></button>
<?php foreach ( $services as $service ) : $count = showmakers_service_project_count( $service->slug, $projects ); if ( ! $count ) continue; ?>
<button type="button" data-event="work_filter" data-service="<?php echo esc_attr( $service->slug ); ?>" aria-pressed="false"><?php echo esc_html( $service->name ); ?> <span><?php echo esc_html( sprintf( '%02d', $count ) ); ?></span></button>
<?php endforeach; ?>
</div><p class="visually-hidden" id="work-status" role="status" aria-live="polite"></p>
<section class="work-collection" aria-label="Project collection">
<?php foreach ( $projects as $index => $project ) :
    get_template_part( 'template-parts/project-card', null, array( 'project' => $project, 'index' => $index ) );
endforeach; ?>
</section><p id="work-empty" <?php if ( $projects ) echo 'hidden'; ?>>No published projects are available for this service yet.</p>
<noscript><p>All projects are shown. Service filters require JavaScript.</p></noscript>
</div>
<?php get_footer(); ?>
