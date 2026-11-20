<?php
/**
 * Breadcrumb trail, shared by the breadcrumb block and BreadcrumbList schema.
 *
 * @package ceori
 */

defined( 'ABSPATH' ) || exit;

/**
 * Build the breadcrumb trail for the current request.
 *
 * @return array[] List of [ 'name' => string, 'url' => string ].
 */
function ceori_breadcrumb_trail() {
	$trail = array(
		array(
			'name' => __( 'Home', 'ceori' ),
			'url'  => home_url( '/' ),
		),
	);

	if ( is_front_page() ) {
		return $trail;
	}

	if ( is_singular( 'page' ) ) {
		$page     = get_queried_object();
		$template = get_page_template_slug( $page );
		$treat    = get_page_by_path( 'treatments' );

		if ( in_array( $template, array( 'page-treatment.php', 'page-treatment-category.php' ), true ) && $treat && $treat->ID !== $page->ID ) {
			$trail[] = array(
				'name' => get_the_title( $treat ),
				'url'  => get_permalink( $treat ),
			);
		}
		if ( 'page-treatment.php' === $template ) {
			$terms = get_the_terms( $page, 'treatment_category' );
			if ( $terms && ! is_wp_error( $terms ) ) {
				$landing = (int) get_term_meta( $terms[0]->term_id, 'ceori_landing_page', true );
				if ( $landing && $landing !== $page->ID ) {
					$trail[] = array(
						'name' => get_the_title( $landing ),
						'url'  => get_permalink( $landing ),
					);
				}
			}
		}
		foreach ( array_reverse( get_post_ancestors( $page ) ) as $ancestor ) {
			$trail[] = array(
				'name' => get_the_title( $ancestor ),
				'url'  => get_permalink( $ancestor ),
			);
		}
		// Rank Math → Advanced → "Breadcrumb Title" can name the page differently in the trail.
		$label   = trim( (string) get_post_meta( $page->ID, 'rank_math_breadcrumb_title', true ) );
		$trail[] = array(
			'name' => '' !== $label ? $label : get_the_title( $page ),
			'url'  => get_permalink( $page ),
		);
		return $trail;
	}

	$blog_id  = (int) get_option( 'page_for_posts' );
	$blog_url = $blog_id ? get_permalink( $blog_id ) : home_url( '/blog/' );
	$blog     = $blog_id ? get_the_title( $blog_id ) : __( 'Blog', 'ceori' );

	if ( is_singular( 'post' ) ) {
		$trail[] = array(
			'name' => $blog,
			'url'  => $blog_url,
		);
		$trail[] = array(
			'name' => get_the_title(),
			'url'  => get_permalink(),
		);
	} elseif ( is_home() ) {
		$trail[] = array(
			'name' => $blog,
			'url'  => $blog_url,
		);
	} elseif ( is_category() || is_tag() || is_author() || is_date() ) {
		$trail[] = array(
			'name' => $blog,
			'url'  => $blog_url,
		);
		$trail[] = array(
			'name' => wp_strip_all_tags( get_the_archive_title() ),
			'url'  => '',
		);
	} elseif ( is_search() ) {
		/* translators: %s: search query */
		$trail[] = array(
			'name' => sprintf( __( 'Search results for “%s”', 'ceori' ), get_search_query() ),
			'url'  => '',
		);
	} elseif ( is_404() ) {
		$trail[] = array(
			'name' => __( 'Page not found', 'ceori' ),
			'url'  => '',
		);
	}

	return apply_filters( 'ceori_breadcrumb_trail', $trail );
}

/**
 * Breadcrumb navigation markup.
 *
 * @return string
 */
function ceori_breadcrumb_html() {
	$trail = ceori_breadcrumb_trail();
	if ( count( $trail ) < 2 ) {
		return '';
	}
	$items = array();
	$last  = count( $trail ) - 1;
	foreach ( $trail as $i => $crumb ) {
		if ( $i === $last || ! $crumb['url'] ) {
			$items[] = '<li><span aria-current="page">' . esc_html( $crumb['name'] ) . '</span></li>';
		} else {
			$items[] = '<li><a href="' . esc_url( $crumb['url'] ) . '">' . esc_html( $crumb['name'] ) . '</a></li>';
		}
	}
	return '<nav class="ceori-breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'ceori' ) . '"><ol>' . implode( '', $items ) . '</ol></nav>';
}
