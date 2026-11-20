<?php
/**
 * Site search: type labels (Treatment, Specialist, Article…) and instant suggestions.
 *
 * Every search box (blog banner, search results, 404) suggests matching treatments,
 * specialists and articles while the visitor types, via GET /wp-json/ceori/v1/search?q=…
 * Pressing Enter still opens the full results page.
 *
 * @package ceori
 */

defined( 'ABSPATH' ) || exit;

/**
 * What a search result is, for its label.
 *
 * @param WP_Post $post Post.
 * @return string
 */
function ceori_search_type( $post ) {
	if ( 'post' === $post->post_type ) {
		return __( 'Article', 'ceori' );
	}
	if ( 'page' !== $post->post_type ) {
		// Content from other plugins: their own name for it ("Product", "Event"…).
		$type = get_post_type_object( $post->post_type );
		return $type ? $type->labels->singular_name : __( 'Page', 'ceori' );
	}
	switch ( get_page_template_slug( $post ) ) {
		case 'page-treatment.php':
			return __( 'Treatment', 'ceori' );
		case 'page-treatment-category.php':
			return __( 'Speciality', 'ceori' );
		case 'page-doctor.php':
			return __( 'Specialist', 'ceori' );
	}
	return __( 'Page', 'ceori' );
}

/**
 * Small image for a result: its featured image, or for a specialist profile the photo from
 * Specialists in the dashboard.
 *
 * @param WP_Post $post Post.
 * @return string URL or ''.
 */
function ceori_search_image( $post ) {
	return (string) get_the_post_thumbnail_url( ceori_search_image_source( $post ), 'thumbnail' );
}

/**
 * The post whose featured image represents a result: the result itself, or for a specialist
 * profile page without one, the specialist entry under Specialists.
 *
 * @param WP_Post $post Post.
 * @return int Post ID.
 */
function ceori_search_image_source( $post ) {
	if ( has_post_thumbnail( $post ) || 'page-doctor.php' !== get_page_template_slug( $post ) ) {
		return $post->ID;
	}
	$doctor = ceori_doctor_for_page( $post->ID );
	return $doctor ? $doctor : $post->ID;
}

/**
 * Posts matching a search, treatments and specialists first.
 *
 * @param string $q     Search words.
 * @param int    $limit Maximum.
 * @return WP_Post[]
 */
function ceori_search_posts( $q, $limit ) {
	$posts = get_posts(
		array(
			's'                   => $q,
			'post_type'           => ceori_searchable_post_types(),
			'post_status'         => 'publish',
			'posts_per_page'      => 20,
			'post__not_in'        => array_filter( array( (int) get_option( 'page_on_front' ) ) ),
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		)
	);
	$rank = array(
		__( 'Treatment', 'ceori' )  => 0,
		__( 'Speciality', 'ceori' ) => 1,
		__( 'Specialist', 'ceori' ) => 2,
		__( 'Article', 'ceori' )    => 3,
		__( 'Page', 'ceori' )       => 4,
	);
	// A title match beats a text match; then the type order above; otherwise keep WordPress's order.
	$scored = array();
	foreach ( $posts as $i => $post ) {
		$in_title = false !== mb_stripos( $post->post_title, $q );
		$scored[] = array( ( $in_title ? 0 : 10 ) + ( $rank[ ceori_search_type( $post ) ] ?? 5 ), $i, $post );
	}
	usort( $scored, fn( $a, $b ) => $a[0] <=> $b[0] ?: $a[1] <=> $b[1] );
	return array_slice( array_column( $scored, 2 ), 0, $limit );
}

add_action(
	'rest_api_init',
	function () {
		register_rest_route(
			'ceori/v1',
			'/search',
			array(
				'methods'             => 'GET',
				'permission_callback' => '__return_true',
				'args'                => array(
					'q' => array(
						'type'              => 'string',
						'required'          => true,
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'callback'            => function ( WP_REST_Request $request ) {
					$q   = trim( (string) $request['q'] );
					$out = array();
					if ( mb_strlen( $q ) >= 2 ) {
						foreach ( ceori_search_posts( mb_substr( $q, 0, 60 ), 6 ) as $post ) {
							$out[] = array(
								'title' => html_entity_decode( get_the_title( $post ), ENT_QUOTES ),
								'url'   => get_permalink( $post ),
								'type'  => ceori_search_type( $post ),
								'image' => ceori_search_image( $post ),
							);
						}
					}
					$response = rest_ensure_response( $out );
					$response->header( 'Cache-Control', 'public, max-age=600' );
					return $response;
				},
			)
		);
	}
);

// Full results page: pages, articles and searchable content from other plugins (no media files),
// treatments and specialists first. A search a plugin limits to its own type (e.g. a product
// search with ?post_type=product) is left as the plugin asked.
add_action(
	'pre_get_posts',
	function ( $query ) {
		if ( is_admin() || ! $query->is_main_query() || ! $query->is_search() || $query->get( 'post_type' ) ) {
			return;
		}
		$query->set( 'post_type', ceori_searchable_post_types() );
		$query->set( 'post__not_in', array_filter( array( (int) get_option( 'page_on_front' ) ) ) );
	}
);
add_filter(
	'posts_search_orderby',
	function ( $orderby, $query ) {
		global $wpdb;
		if ( ! $query->is_main_query() || ! $query->is_search() || ! $orderby ) {
			return $orderby;
		}
		// Treatment, speciality and specialist pages before articles, after title relevance.
		$template = "(SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id = {$wpdb->posts}.ID AND meta_key = '_wp_page_template' LIMIT 1)";
		return $orderby . ", CASE WHEN {$template} IN ('page-treatment.php','page-treatment-category.php','page-doctor.php') THEN 0 ELSE 1 END";
	},
	10,
	2
);
