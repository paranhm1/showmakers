<?php
/** Safe fallback, without a default WordPress blog/design. */
defined( 'ABSPATH' ) || exit;
get_header();
?>
<div class="page-intro-surface"><div class="shell page-intro"><h1><?php echo esc_html( get_bloginfo( 'name' ) ); ?></h1><p>WordPress theme shell. Content integration follows in the next migration phase.</p></div></div>
<?php get_footer(); ?>
