<?php
/**
 * Template Name: Doctor Profile
 * Template Post Type: page
 *
 * @package ceori
 */

get_header();
get_template_part( 'template-parts/page-body', null, array( 'kind' => 'doctor' ) );
get_footer();
