<?php
/** Editable native Page content; restrained theme-owned legal presentation. */
defined( 'ABSPATH' ) || exit;
get_header();
while ( have_posts() ) : the_post(); ?>
<article class="shell privacy-page"><h1><?php the_title(); ?></h1><div class="privacy-content"><?php the_content(); ?></div></article>
<?php endwhile; get_footer(); ?>
