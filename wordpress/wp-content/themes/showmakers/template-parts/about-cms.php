<?php
/** One-to-one approved About layout; no static/JSON runtime fallback. */
defined( 'ABSPATH' ) || exit;
$page = $args['page'];
$heading = showmakers_about_text( $page, 'about_hero_heading' ) ?: get_the_title( $page );
$intro = showmakers_about_text( $page, 'about_hero_intro' );
?>
<div class="page-intro-surface"><div class="shell page-intro"><h1><?php echo esc_html( $heading ); ?></h1><?php if ( $intro !== '' ) : ?><p><?php echo esc_html( $intro ); ?></p><?php endif; ?></div></div>
<?php
$heading = showmakers_about_text( $page, 'about_philosophy_heading' );
$body = showmakers_about_text( $page, 'about_philosophy_body' );
if ( $heading !== '' || $body !== '' ) : ?>
<section class="shell about-philosophy"><?php if ( $heading !== '' ) : ?><h2><?php echo esc_html( $heading ); ?></h2><?php endif; if ( $body !== '' ) : ?><p><?php echo esc_html( $body ); ?></p><?php endif; ?></section>
<?php endif;
$heading = showmakers_about_text( $page, 'about_approach_heading' );
$body = showmakers_about_text( $page, 'about_approach_body' );
$image = showmakers_about_image( $page );
if ( $heading !== '' || $body !== '' || $image !== '' ) : ?>
<section class="approach"><div class="shell approach-inner"><?php if ( $heading !== '' || $body !== '' ) : ?><div class="approach-copy"><?php if ( $heading !== '' ) : ?><h2><?php echo showmakers_about_heading( $heading ); ?></h2><?php endif; if ( $body !== '' ) : ?><p><?php echo esc_html( $body ); ?></p><?php endif; ?></div><?php endif; echo $image; ?></div></section>
<?php endif;
$steps = array();
for ( $i = 1; $i <= 4; $i++ ) {
    $heading = showmakers_about_text( $page, 'about_step_' . $i . '_heading' );
    $body = showmakers_about_text( $page, 'about_step_' . $i . '_body' );
    if ( $heading !== '' || $body !== '' ) $steps[$i] = array( 'heading' => $heading, 'body' => $body );
}
if ( $steps ) : ?>
<section class="shell working-steps" aria-label="Our working approach"><?php foreach ( $steps as $i => $step ) : ?><div><span class="editorial-number"><?php echo esc_html( sprintf( '%02d', $i ) ); ?></span><?php if ( $step['heading'] !== '' ) : ?><h2><?php echo esc_html( $step['heading'] ); ?></h2><?php endif; if ( $step['body'] !== '' ) : ?><p><?php echo esc_html( $step['body'] ); ?></p><?php endif; ?></div><?php endforeach; ?></section>
<?php endif; ?>
