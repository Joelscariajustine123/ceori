<?php
/**
 * Template Name: Treatment Category
 * Template Post Type: page
 *
 * @package ceori
 */

get_header();
get_template_part( 'template-parts/page-body', null, array( 'kind' => 'category' ) );
get_footer();
