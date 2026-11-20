<?php
/**
 * Single blog article (block layout; posts can also be opened in Elementor).
 * Content types added by plugins (events, products, courses…) use the plain page layout, so the
 * plugin's own content shows as its author intended, without the blog's article extras.
 *
 * @package ceori
 */

get_header();
if ( 'post' !== get_post_type() ) {
	get_template_part( 'template-parts/page-body', null, array( 'kind' => 'default' ) );
	get_footer();
	return;
}
while ( have_posts() ) :
	the_post();
	if ( ceori_is_elementor_page( get_the_ID() ) ) {
		echo '<div class="ceori-el">';
		the_content();
		echo '</div>';
	} else {
		echo do_blocks( '<!-- wp:pattern {"slug":"ceori/single-post"} /-->' ); // phpcs:ignore WordPress.Security.EscapeOutput
	}
	ceori_comments();
endwhile;
get_footer();
