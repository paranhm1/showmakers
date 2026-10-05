<?php
defined( 'ABSPATH' ) || exit;
$services = $args['services'];
?>
<div class="page-intro-surface"><div class="shell page-intro"><h1>Services</h1><p>Marketing thinking, creative content and execution across channels. Eight individual services, connected by the needs of your brand.</p></div></div>
<?php if ( $services ) : ?>
<div class="shell services-layout" id="service-explorer"><nav class="service-navigation" aria-label="Service index"><p>Explore our services</p>
<?php foreach ( $services as $term ) : ?><a href="#<?php echo esc_attr( $term->slug ); ?>"><span><?php echo esc_html( sprintf( '%02d', (int) get_term_meta( $term->term_id, 'sort_order', true ) ) ); ?></span><?php echo esc_html( $term->name ); ?></a><?php endforeach; ?>
</nav><div class="service-panels">
<?php foreach ( $services as $term ) :
    $id = $term->term_id;
    $capabilities = showmakers_service_lines( get_term_meta( $id, 'capabilities', true ) );
    $platforms = showmakers_service_lines( get_term_meta( $id, 'platforms', true ) );
    $formats = showmakers_service_lines( get_term_meta( $id, 'formats', true ) );
    $passages = showmakers_service_passages( $term );
?>
<section class="service-section" id="<?php echo esc_attr( $term->slug ); ?>" aria-labelledby="heading-<?php echo esc_attr( $term->slug ); ?>">
<div class="service-heading"><span class="service-number"><?php echo esc_html( sprintf( '%02d', (int) get_term_meta( $id, 'sort_order', true ) ) ); ?></span><h2 id="heading-<?php echo esc_attr( $term->slug ); ?>"><?php echo esc_html( $term->name ); ?></h2></div>
<?php foreach ( array( 'service_intro' => 'service-positioning', 'service_description' => 'service-description' ) as $field => $class ) : $value = trim( (string) get_term_meta( $id, $field, true ) ); if ( $value !== '' ) : ?><p class="<?php echo esc_attr( $class ); ?>"><?php echo esc_html( $value ); ?></p><?php endif; endforeach; ?>
<?php if ( $capabilities || ( $platforms && ! $passages ) || $formats ) : ?><div class="service-details">
<?php if ( $capabilities ) : ?><div><h3>What we provide</h3><ul><?php foreach ( $capabilities as $value ) : ?><li><?php echo esc_html( $value ); ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<?php if ( $platforms && ! $passages ) : ?><div><h3>Platforms</h3><p><?php echo esc_html( implode( ' · ', $platforms ) ); ?></p></div><?php endif; ?>
<?php if ( $formats ) : ?><div><h3>Formats</h3><p><?php echo esc_html( implode( ' · ', $formats ) ); ?></p></div><?php endif; ?>
</div><?php endif; ?>
<?php if ( $passages ) : ?><div class="platform-passages"><?php foreach ( $passages as $passage ) : ?><div class="platform-entry"><img src="<?php echo esc_url( showmakers_asset_url( $passage['icon'] ) ); ?>" width="26" height="26" alt="" aria-hidden="true"><div><h3><?php echo esc_html( $passage['title'] ); ?></h3><p><?php echo esc_html( $passage['text'] ); ?></p></div></div><?php endforeach; ?></div><?php endif; ?>
<?php
$media = array();
foreach ( array( 'service_media', 'service_media_2' ) as $field ) {
    $attachment = absint( get_term_meta( $id, $field, true ) );
    if ( function_exists( 'showmakers_approved_media' ) && showmakers_approved_media( $attachment ) ) $media[] = array( 'id' => $attachment, 'caption' => get_term_meta( $id, $field . '_caption', true ) );
}
if ( $media ) : ?><div class="service-media <?php echo $term->slug === 'website-digital-solutions' ? 'website-media' : ''; ?>"><?php foreach ( $media as $item ) : ?><figure><?php echo showmakers_cms_image( $item['id'] ); if ( $item['caption'] ) : ?><figcaption><?php echo esc_html( $item['caption'] ); ?></figcaption><?php endif; ?></figure><?php endforeach; ?></div><?php endif; ?>
<div class="service-actions">
<?php if ( showmakers_service_project_count( $term->slug, $args['projects'] ) ) : ?><a class="arrow-link" href="<?php echo esc_url( add_query_arg( 'service', $term->slug, home_url( '/work/' ) ) ); ?>" data-event="service_related_work">Related work<?php echo showmakers_arrow_icon(); ?></a><?php endif; ?>
<a class="arrow-link" href="<?php echo esc_url( add_query_arg( 'service', $term->slug, home_url( '/contact/' ) ) ); ?>" data-event="service_start_inquiry">Start a project<?php echo showmakers_arrow_icon(); ?></a></div>
</section><?php endforeach; ?></div><p class="visually-hidden" id="service-status" role="status" aria-live="polite"></p></div>
<?php endif; ?>
