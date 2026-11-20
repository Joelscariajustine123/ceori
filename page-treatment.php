<?php
/**
 * Template Name: Treatment Detail
 * Template Post Type: page
 *
 * @package ceori
 */

get_header();
get_template_part( 'template-parts/page-body', null, array( 'kind' => 'treatment' ) );
get_footer();
