<?php
/**
 * Search results (the "CEORI Search results" Elementor template, else the built-in layout).
 *
 * @package ceori
 */

get_header();
if ( ! ceori_render_elementor_template( 'search' ) ) {
	echo do_blocks( '<!-- wp:pattern {"slug":"ceori/search-results"} /-->' ); // phpcs:ignore WordPress.Security.EscapeOutput
}
get_footer();
