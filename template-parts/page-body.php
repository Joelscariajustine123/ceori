<?php
/**
 * Page body shared by all page templates.
 *
 * Pages built with Elementor print their Elementor layout full width. Pages still in the
 * block editor get the theme's banner and sections for their page type.
 *
 * @package ceori
 *
 * @var array $args { kind: default|treatment|category|doctor|full }
 */

$ceori_kind = isset( $args['kind'] ) ? $args['kind'] : 'default';
// The homepage has its own hero, never a page banner.
if ( is_front_page() ) {
	$ceori_kind = 'full';
}

while ( have_posts() ) :
	the_post();

	if ( ceori_is_elementor_page( get_the_ID() ) ) {
		echo '<div class="ceori-el">';
		the_content();
		echo '</div>';
		ceori_comments();
		continue;
	}

	$ceori_blocks = array();
	if ( in_array( $ceori_kind, array( 'treatment', 'category' ), true ) ) {
		$ceori_blocks[] = '<!-- wp:pattern {"slug":"ceori/treatment-hero"} /-->';
	} elseif ( 'full' !== $ceori_kind ) {
		$ceori_blocks[] = '<!-- wp:pattern {"slug":"ceori/page-banner"} /-->';
	}
	$ceori_class    = 'treatment' === $ceori_kind ? 'ceori-page-content ceori-treatment-body' : 'ceori-page-content';
	$ceori_blocks[] = '<!-- wp:post-content {"align":"full","className":"' . $ceori_class . '","layout":{"type":"constrained"}} /-->';
	if ( 'treatment' === $ceori_kind ) {
		$ceori_blocks[] = '<!-- wp:pattern {"slug":"ceori/related-treatments"} /-->';
	}
	if ( in_array( $ceori_kind, array( 'treatment', 'category', 'doctor' ), true ) ) {
		$ceori_blocks[] = '<!-- wp:pattern {"slug":"ceori/cta-appointment"} /-->';
	}
	echo do_blocks( implode( "\n\n", $ceori_blocks ) ); // phpcs:ignore WordPress.Security.EscapeOutput
	ceori_comments();
endwhile;
