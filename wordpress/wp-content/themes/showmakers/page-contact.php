<?php
defined( 'ABSPATH' ) || exit;
// This page carries short-lived signed form tokens; avoid serving a stale cached form.
nocache_headers();
get_header();
get_template_part( 'template-parts/contact-reference', null, array( 'projects' => showmakers_visible_projects() ) );
get_footer();
