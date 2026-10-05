<?php
/** Phase 2: approved static presentation; no CMS records imported. */
defined( 'ABSPATH' ) || exit;
?>
<?php get_header(); ?>
<section class="shell not-found"><h1>404</h1><p>Looks like this idea wandered off.</p><div><a class="arrow-link" href="<?php echo esc_url( home_url( '/' ) ); ?>">Back Home<svg class="ui-arrow" viewBox="0 0 64 16" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="square" stroke-linejoin="miter"><path d="M2 8H60M54 2L60 8L54 14"/></svg></a><a class="arrow-link" href="<?php echo esc_url( home_url( '/work/' ) ); ?>">View Work<svg class="ui-arrow" viewBox="0 0 64 16" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="square" stroke-linejoin="miter"><path d="M2 8H60M54 2L60 8L54 14"/></svg></a></div></section>
<?php get_footer(); ?>
