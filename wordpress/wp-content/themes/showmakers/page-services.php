<?php
defined( 'ABSPATH' ) || exit;
get_header();
get_template_part( 'template-parts/services-cms', null, array( 'services' => showmakers_visible_services(), 'projects' => showmakers_visible_projects() ) );
get_footer();
