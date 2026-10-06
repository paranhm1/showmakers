<?php
/** Approved About presentation sourced exclusively from its WordPress Page. */
defined( 'ABSPATH' ) || exit;
get_header();
get_template_part( 'template-parts/about-cms', null, array( 'page' => get_queried_object_id() ) );
get_footer();
