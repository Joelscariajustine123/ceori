<?php
/**
 * Category and other archives (the "CEORI Blog archive" Elementor template), and the
 * built-in blog layout when no template or Elementor Blog page exists.
 *
 * @package ceori
 */

get_header();
if ( ! ceori_render_elementor_template( 'archive' ) ) {
	echo do_blocks( '<!-- wp:pattern {"slug":"ceori/blog-index"} /-->' ); // phpcs:ignore WordPress.Security.EscapeOutput
}
get_footer();
