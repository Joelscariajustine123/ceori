<?php
/**
 * Page not found (the "CEORI 404 page" Elementor template, else the built-in layout).
 *
 * @package ceori
 */

get_header();
if ( ! ceori_render_elementor_template( '404' ) ) {
	echo do_blocks( '<!-- wp:pattern {"slug":"ceori/not-found"} /-->' ); // phpcs:ignore WordPress.Security.EscapeOutput
}
get_footer();
