<?php
/**
 * Template Name: Full Width (no banner)
 * Template Post Type: page
 *
 * @package ceori
 */

get_header();
get_template_part( 'template-parts/page-body', null, array( 'kind' => 'full' ) );
get_footer();
