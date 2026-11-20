<?php
/**
 * SEO output.
 *
 * - With Rank Math, Yoast, AIOSEO or SEOPress active, the plugin owns titles, meta,
 *   Open Graph, breadcrumbs schema and sitemaps; the theme only adds clinic-specific
 *   schema (MedicalClinic, Physician, MedicalProcedure) that those plugins don't produce.
 * - Without a plugin, the theme outputs everything itself from the "SEO title" and
 *   "Meta description" fields stored on each page/post (the importer also writes the
 *   same values into Rank Math and Yoast fields, so switching later loses nothing).
 *
 * @package ceori
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whether a dedicated SEO plugin is active.
 */
function ceori_seo_plugin_active() {
	$active = defined( 'RANK_MATH_VERSION' ) || defined( 'WPSEO_VERSION' ) || defined( 'AIOSEO_VERSION' ) || defined( 'SEOPRESS_VERSION' )
		|| defined( 'THE_SEO_FRAMEWORK_VERSION' ) || defined( 'SLIM_SEO_VER' ) || defined( 'SQ_VERSION' ) || defined( 'SMARTCRAWL_VERSION' );
	return (bool) apply_filters( 'ceori_seo_plugin_active', $active );
}

add_action(
	'init',
	function () {
		foreach ( array( 'page', 'post' ) as $type ) {
			foreach ( array( '_ceori_seo_title', '_ceori_seo_description' ) as $key ) {
				register_post_meta(
					$type,
					$key,
					array(
						'type'          => 'string',
						'single'        => true,
						'show_in_rest'  => true,
						'auth_callback' => function () {
							return current_user_can( 'edit_posts' );
						},
					)
				);
			}
		}
	}
);

// Simple SEO box in the editor when no SEO plugin is installed.
add_action(
	'add_meta_boxes',
	function () {
		if ( ceori_seo_plugin_active() ) {
			return;
		}
		foreach ( array( 'page', 'post' ) as $type ) {
			add_meta_box( 'ceori_seo', __( 'Search engine listing', 'ceori' ), 'ceori_render_seo_box', $type, 'normal', 'low' );
		}
	}
);

/**
 * SEO meta box.
 *
 * @param WP_Post $post Post.
 */
function ceori_render_seo_box( $post ) {
	wp_nonce_field( 'ceori_seo', 'ceori_seo_nonce' );
	$title = get_post_meta( $post->ID, '_ceori_seo_title', true );
	$desc  = get_post_meta( $post->ID, '_ceori_seo_description', true );
	echo '<p><label for="ceori_seo_title"><strong>' . esc_html__( 'SEO title', 'ceori' ) . '</strong> ' . esc_html__( '(about 50–60 characters; leave empty to use the page title)', 'ceori' ) . '</label><input class="widefat" id="ceori_seo_title" name="ceori_seo_title" value="' . esc_attr( $title ) . '"></p>';
	echo '<p><label for="ceori_seo_description"><strong>' . esc_html__( 'Meta description', 'ceori' ) . '</strong> ' . esc_html__( '(about 150–160 characters)', 'ceori' ) . '</label><textarea class="widefat" rows="3" id="ceori_seo_description" name="ceori_seo_description">' . esc_textarea( $desc ) . '</textarea></p>';
}

add_action(
	'save_post',
	function ( $post_id ) {
		if ( ! isset( $_POST['ceori_seo_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['ceori_seo_nonce'] ), 'ceori_seo' ) || ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		update_post_meta( $post_id, '_ceori_seo_title', sanitize_text_field( wp_unslash( $_POST['ceori_seo_title'] ?? '' ) ) );
		update_post_meta( $post_id, '_ceori_seo_description', sanitize_textarea_field( wp_unslash( $_POST['ceori_seo_description'] ?? '' ) ) );
	}
);

/**
 * Meta description for the current request.
 */
function ceori_meta_description() {
	if ( is_singular() || is_home() ) {
		$id = is_home() ? (int) get_option( 'page_for_posts' ) : get_queried_object_id();
		return ceori_fit_description( $id ? ceori_post_description( $id ) : '' );
	}
	if ( is_category() || is_tag() ) {
		return wp_strip_all_tags( term_description() );
	}
	return get_bloginfo( 'description' );
}

/**
 * Search description for a post or page: the saved SEO description, unless it is too short to
 * describe the page (e.g. just a job title, or "Load More" carried over from the old site) and
 * the page summary says more.
 *
 * @param int $id Post ID.
 * @return string
 */
function ceori_post_description( $id ) {
	$post = get_post( $id );
	if ( ! $post ) {
		return '';
	}
	$desc    = trim( wp_strip_all_tags( (string) get_post_meta( $id, '_ceori_seo_description', true ) ) );
	$summary = has_excerpt( $post ) ? get_the_excerpt( $post ) : wp_trim_words( wp_strip_all_tags( strip_shortcodes( $post->post_content ) ), 28, '…' );
	$summary = trim( wp_strip_all_tags( $summary ) );
	if ( mb_strlen( $desc ) < 70 && mb_strlen( $summary ) > mb_strlen( $desc ) ) {
		$desc = $summary;
	}
	if ( '' === $desc && is_home() ) {
		$desc = __( 'Skin, hair and cosmetic care tips and treatment guides from the specialists at CEORI Skin Clinic, Calicut.', 'ceori' );
	}
	return $desc;
}

/**
 * Keep a description within the ~160 characters search results show, ending on a whole word.
 *
 * @param string $desc Description.
 * @return string
 */
function ceori_fit_description( $desc ) {
	$desc = trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( $desc ) ) );
	if ( mb_strlen( $desc ) <= 160 ) {
		return $desc;
	}
	$cut = mb_substr( $desc, 0, 157 );
	$cut = preg_replace( '/[\s,;:–—-]+\S*$/u', '', $cut );
	return rtrim( $cut, ' ,;:.–—-' ) . '…';
}

/**
 * Canonical URL for the current request.
 */
function ceori_canonical_url() {
	if ( is_singular() ) {
		return wp_get_canonical_url();
	}
	if ( is_front_page() ) {
		return home_url( '/' );
	}
	$paged = max( 1, (int) get_query_var( 'paged' ) );
	if ( is_home() ) {
		$blog = (int) get_option( 'page_for_posts' );
		$base = $blog ? get_permalink( $blog ) : home_url( '/' );
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$base = get_term_link( get_queried_object() );
	} else {
		return '';
	}
	return $paged > 1 ? trailingslashit( $base ) . 'page/' . $paged . '/' : $base;
}

add_filter(
	'pre_get_document_title',
	function ( $title ) {
		if ( ceori_seo_plugin_active() ) {
			return $title;
		}
		$id = 0;
		if ( is_singular() ) {
			$id = get_queried_object_id();
		} elseif ( is_home() ) {
			$id = (int) get_option( 'page_for_posts' );
		}
		$custom = $id ? (string) get_post_meta( $id, '_ceori_seo_title', true ) : '';
		// Titles carried over from the old site end in " - Best Skin Clinic in Calicut – Ceori Skin
		// Clinic" and often run past the ~65 characters search results show: rebuild them as
		// "Name | CEORI Skin Clinic, Calicut", shortening the suffix only as far as needed.
		if ( $custom && preg_match( '/^(.*?)\s*[|–—-]\s*best skin clinic in calicut\s*([|–—-]\s*ceori skin clinic)?\s*$/iu', $custom, $old ) ) {
			$base = trim( $old[1] );
			foreach ( array( ' | CEORI Skin Clinic, Calicut', ' | CEORI Skin Clinic', '' ) as $suffix ) {
				if ( mb_strlen( $base . $suffix ) <= 65 ) {
					break;
				}
			}
			$custom = $base . $suffix;
		}
		return $custom ? $custom : $title;
	}
);

// Generated titles ("Title | CEORI Skin Clinic"): leave out the clinic name when it would push
// the title past ~65 characters, so the words that describe the page are not cut off.
add_filter(
	'document_title_parts',
	function ( $parts ) {
		if ( ! empty( $parts['title'] ) && ! empty( $parts['site'] ) && mb_strlen( $parts['title'] . ' | ' . $parts['site'] ) > 65 ) {
			unset( $parts['site'] );
		}
		return $parts;
	}
);

add_filter(
	'document_title_separator',
	function () {
		return '|';
	}
);

add_filter(
	'wp_robots',
	function ( $robots ) {
		if ( ceori_seo_plugin_active() ) {
			return $robots;
		}
		$lone_category = is_category() && count( get_categories( array( 'hide_empty' => true ) ) ) < 2; // Repeats /blog/.
		if ( is_search() || is_404() || is_attachment() || is_author() || is_date() || $lone_category ) {
			$robots['noindex'] = true;
			$robots['follow']  = true;
		} else {
			$robots['max-image-preview'] = 'large';
		}
		return $robots;
	}
);

// Later pages of the Blog page's article list (/blog/page/2/) are their own pages.
add_filter(
	'get_canonical_url',
	function ( $url, $post ) {
		$paged = (int) get_query_var( 'paged' );
		return $paged > 1 && is_page() && get_queried_object_id() === $post->ID ? trailingslashit( $url ) . 'page/' . $paged . '/' : $url;
	},
	10,
	2
);

add_action(
	'wp_head',
	function () {
		if ( ceori_seo_plugin_active() ) {
			return;
		}
		$desc = trim( wp_strip_all_tags( ceori_meta_description() ) );
		if ( $desc ) {
			echo '<meta name="description" content="' . esc_attr( wp_html_excerpt( $desc, 300 ) ) . '">' . "\n";
		}
		$canonical = ceori_canonical_url();
		if ( $canonical && ! is_singular() ) {
			echo '<link rel="canonical" href="' . esc_url( $canonical ) . '">' . "\n";
		}

		// Open Graph / Twitter.
		$title = wp_get_document_title();
		$image = '';
		if ( is_singular() && has_post_thumbnail() ) {
			$image = get_the_post_thumbnail_url( get_queried_object_id(), 'large' );
		}
		if ( ! $image ) {
			$image = ceori_media_url( 'og-default' );
		}
		$og = array(
			'og:locale'      => 'en_IN',
			'og:type'        => is_singular( 'post' ) ? 'article' : 'website',
			'og:site_name'   => ceori_option( 'clinic_name' ),
			'og:title'       => $title,
			'og:description' => $desc,
			'og:url'         => $canonical ? $canonical : home_url( add_query_arg( array() ) ),
			'og:image'       => $image,
		);
		foreach ( $og as $property => $content ) {
			if ( $content ) {
				echo '<meta property="' . esc_attr( $property ) . '" content="' . esc_attr( $content ) . '">' . "\n";
			}
		}
		if ( is_singular( 'post' ) ) {
			echo '<meta property="article:published_time" content="' . esc_attr( get_the_date( 'c' ) ) . '">' . "\n";
			echo '<meta property="article:modified_time" content="' . esc_attr( get_the_modified_date( 'c' ) ) . '">' . "\n";
		}
		echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
	},
	3
);

/**
 * URL of a theme media item mapped by the importer (falls back to bundled image).
 *
 * @param string $key Media key.
 * @return string
 */
function ceori_media_url( $key ) {
	$map = (array) get_option( 'ceori_media_map', array() );
	if ( ! empty( $map[ $key ] ) ) {
		$url = wp_get_attachment_image_url( (int) $map[ $key ], 'large' );
		if ( $url ) {
			return $url;
		}
	}
	$file = '/assets/images/' . $key . '.webp';
	return file_exists( CEORI_DIR . $file ) ? CEORI_URI . $file : '';
}

/*
 * ------------------------------------------------------------------
 * Structured data
 * ------------------------------------------------------------------
 */

/**
 * MedicalClinic entity.
 */
function ceori_schema_clinic() {
	$same_as = array_values( array_filter( array( ceori_option( 'instagram' ), ceori_option( 'facebook' ), ceori_option( 'youtube' ) ) ) );
	$logo_id = (int) get_theme_mod( 'custom_logo' );
	$schema  = array(
		'@type'                     => array( 'MedicalClinic', 'LocalBusiness' ),
		'@id'                       => home_url( '/#clinic' ),
		'name'                      => ceori_option( 'clinic_name' ),
		'url'                       => home_url( '/' ),
		'telephone'                 => ceori_tel_href( ceori_option( 'phone_primary' ) ) ? substr( ceori_tel_href( ceori_option( 'phone_primary' ) ), 4 ) : '',
		'email'                     => ceori_option( 'email' ),
		'image'                     => ceori_media_url( 'og-default' ),
		'address'                   => array(
			'@type'           => 'PostalAddress',
			'streetAddress'   => ceori_option( 'street' ),
			'addressLocality' => 'Kozhikode',
			'addressRegion'   => ceori_option( 'region' ),
			'postalCode'      => ceori_option( 'postcode' ),
			'addressCountry'  => ceori_option( 'country' ),
		),
		'hasMap'                    => ceori_map_href(),
		'openingHours'              => ceori_option( 'hours_schema' ),
		'medicalSpecialty'          => array( 'Dermatology', 'PlasticSurgery', 'Dentistry', 'Gynecologic' ),
		'areaServed'                => array( 'Kozhikode', 'Calicut', 'Kerala' ),
		'isAcceptingNewPatients'    => true,
		'sameAs'                    => $same_as,
	);
	if ( $logo_id ) {
		$schema['logo'] = wp_get_attachment_image_url( $logo_id, 'full' );
	}
	return array_filter( $schema );
}

/**
 * Breadcrumb list entity.
 */
function ceori_schema_breadcrumbs() {
	$trail = ceori_breadcrumb_trail();
	if ( count( $trail ) < 2 ) {
		return null;
	}
	$items = array();
	foreach ( $trail as $i => $crumb ) {
		$item = array(
			'@type'    => 'ListItem',
			'position' => $i + 1,
			'name'     => $crumb['name'],
		);
		if ( $crumb['url'] ) {
			$item['item'] = $crumb['url'];
		}
		$items[] = $item;
	}
	return array(
		'@type'           => 'BreadcrumbList',
		'itemListElement' => $items,
	);
}

add_action(
	'wp_head',
	function () {
		$graph  = array();
		$plugin = ceori_seo_plugin_active();

		if ( is_front_page() || is_page( array( 'contact-us', 'about-us' ) ) ) {
			$graph[] = ceori_schema_clinic();
		}

		// Specialists on the About page and on doctor profile pages.
		if ( is_page( 'about-us' ) || ( is_page() && 'page-doctor.php' === get_page_template_slug() ) ) {
			$doctors = get_posts(
				array(
					'post_type'      => 'ceori_doctor',
					'post_status'    => 'publish',
					'posts_per_page' => 50,
				)
			);
			foreach ( $doctors as $doctor ) {
				$profile = (int) get_post_meta( $doctor->ID, 'profile_page', true );
				if ( is_page() && 'page-doctor.php' === get_page_template_slug() && $profile !== get_queried_object_id() ) {
					continue;
				}
				$graph[] = array_filter(
					array(
						'@type'            => 'Physician',
						'name'             => get_the_title( $doctor ),
						'description'      => (string) get_post_meta( $doctor->ID, 'designation', true ),
						'image'            => get_the_post_thumbnail_url( $doctor, 'large' ),
						'url'              => $profile ? get_permalink( $profile ) : '',
						'parentOrganization' => array( '@id' => home_url( '/#clinic' ) ),
					)
				);
			}
		}

		// Treatment pages.
		if ( is_page() && 'page-treatment.php' === get_page_template_slug() ) {
			$id      = get_queried_object_id();
			$graph[] = array(
				'@type'       => 'MedicalWebPage',
				'@id'         => get_permalink( $id ) . '#webpage',
				'url'         => get_permalink( $id ),
				'name'        => get_the_title( $id ),
				'description' => ceori_meta_description(),
				'about'       => array(
					'@type'       => 'MedicalProcedure',
					'name'        => get_the_title( $id ),
					'description' => has_excerpt( $id ) ? get_the_excerpt( $id ) : '',
				),
				'provider'    => array( '@id' => home_url( '/#clinic' ) ),
				'lastReviewed' => get_the_modified_date( 'Y-m-d', $id ),
			);
		}

		if ( ! $plugin ) {
			$crumbs = ceori_schema_breadcrumbs();
			if ( $crumbs ) {
				$graph[] = $crumbs;
			}
			if ( is_singular( 'post' ) ) {
				$id      = get_queried_object_id();
				$graph[] = array_filter(
					array(
						'@type'            => 'BlogPosting',
						'headline'         => get_the_title( $id ),
						'description'      => ceori_meta_description(),
						'image'            => get_the_post_thumbnail_url( $id, 'large' ),
						'datePublished'    => get_the_date( 'c', $id ),
						'dateModified'     => get_the_modified_date( 'c', $id ),
						'mainEntityOfPage' => get_permalink( $id ),
						'author'           => array(
							'@type' => 'Organization',
							'name'  => ceori_option( 'clinic_name' ),
							'url'   => home_url( '/' ),
						),
						'publisher'        => array( '@id' => home_url( '/#clinic' ) ),
					)
				);
				if ( ! is_front_page() ) {
					$graph[] = array_filter( array_intersect_key( ceori_schema_clinic(), array_flip( array( '@type', '@id', 'name', 'url', 'logo' ) ) ) );
				}
			}
		}

		$graph = array_values( array_filter( apply_filters( 'ceori_schema_graph', $graph ) ) );
		if ( $graph ) {
			echo '<script type="application/ld+json">' . wp_json_encode(
				array(
					'@context' => 'https://schema.org',
					'@graph'   => $graph,
				),
				JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
			) . '</script>' . "\n";
		}
	},
	20
);

/*
 * ------------------------------------------------------------------
 * Sitemaps, robots.txt and redirects
 * ------------------------------------------------------------------
 */

// Keep the existing Google Search Console sitemap URL working when Rank Math is not installed.
add_action(
	'template_redirect',
	function () {
		if ( ceori_seo_plugin_active() ) {
			return;
		}
		$path = isset( $_SERVER['REQUEST_URI'] ) ? wp_parse_url( esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ), PHP_URL_PATH ) : '';
		$home = wp_parse_url( home_url( '/' ), PHP_URL_PATH );
		$rel  = '/' . ltrim( substr( (string) $path, strlen( rtrim( (string) $home, '/' ) ) ), '/' );
		if ( preg_match( '#^/(sitemap_index|sitemap|post-sitemap|page-sitemap)\.xml$#', $rel ) ) {
			wp_safe_redirect( home_url( '/wp-sitemap.xml' ), 301 );
			exit;
		}
	},
	0
);

// Keep private content types and users out of the core sitemap.
add_filter(
	'wp_sitemaps_add_provider',
	function ( $provider, $name ) {
		return 'users' === $name ? false : $provider;
	},
	10,
	2
);
add_filter(
	'wp_sitemaps_taxonomies',
	function ( $taxonomies ) {
		unset( $taxonomies['post_tag'], $taxonomies['post_format'] );
		// With a single blog category its archive repeats /blog/: list it only once there are several.
		if ( count( get_categories( array( 'hide_empty' => true ) ) ) < 2 ) {
			unset( $taxonomies['category'] );
		}
		return $taxonomies;
	}
);
// Header/footer and Elementor templates are building blocks, not pages.
add_filter(
	'wp_sitemaps_post_types',
	function ( $types ) {
		unset( $types['elementor-hf'], $types['elementor_library'] );
		return $types;
	}
);

add_filter(
	'robots_txt',
	function ( $output, $public ) {
		if ( ! $public ) {
			return $output;
		}
		$output .= "Disallow: /?s=\nDisallow: /search/\n";
		if ( ! ceori_seo_plugin_active() && false === strpos( $output, 'Sitemap:' ) ) {
			$output .= 'Sitemap: ' . home_url( '/wp-sitemap.xml' ) . "\n";
		}
		return $output;
	},
	20,
	2
);

/**
 * Parse the redirects setting into [ '/old/' => '/new/' ].
 */
function ceori_redirect_map() {
	$map = array();
	foreach ( preg_split( '/\r\n|\r|\n/', (string) ceori_option( 'redirects' ) ) as $line ) {
		$parts = preg_split( '/\s+/', trim( $line ) );
		if ( count( $parts ) >= 2 && '#' !== $parts[0][0] ) {
			$map[ '/' . trim( $parts[0], '/' ) . '/' ] = $parts[1];
		}
	}
	return $map;
}

add_action(
	'template_redirect',
	function () {
		if ( ! is_404() ) {
			return;
		}
		$path = isset( $_SERVER['REQUEST_URI'] ) ? wp_parse_url( esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ), PHP_URL_PATH ) : '';
		$home = rtrim( (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH ), '/' );
		$rel  = '/' . trim( substr( (string) $path, strlen( $home ) ), '/' ) . '/';
		$map  = ceori_redirect_map();
		if ( isset( $map[ $rel ] ) ) {
			$target = $map[ $rel ];
			$target = preg_match( '#^https?://#', $target ) ? $target : home_url( '/' . ltrim( $target, '/' ) );
			wp_safe_redirect( $target, 301 );
			exit;
		}
	},
	1
);

// "Read more" under post excerpts (Query Loop): name the article for screen readers and search
// engines, which otherwise see ten identical "Read more" links on the blog page.
add_filter(
	'render_block_core/post-excerpt',
	function ( $html, $block, $instance ) {
		$post_id = isset( $instance->context['postId'] ) ? (int) $instance->context['postId'] : 0;
		if ( ! $post_id || false === strpos( $html, 'wp-block-post-excerpt__more-link' ) ) {
			return $html;
		}
		return preg_replace_callback(
			'#(<a[^>]*wp-block-post-excerpt__more-link[^>]*>)(.*?)(</a>)#s',
			function ( $m ) use ( $post_id ) {
				return $m[1] . ceori_link_text( html_entity_decode( wp_strip_all_tags( $m[2] ) ), get_the_title( $post_id ) ) . $m[3];
			},
			$html,
			1
		);
	},
	10,
	3
);

// Article outline: the page title is the only H1, so an article whose first subheading is an
// H3 or lower (common in posts carried over from the old site) is shifted up to start at H2.
add_filter(
	'the_content',
	function ( $content ) {
		if ( ! is_singular( 'post' ) || get_the_ID() !== get_queried_object_id() || ! preg_match_all( '#<h([2-6])[\s>]#i', $content, $m ) ) {
			return $content;
		}
		$shift = (int) min( $m[1] ) - 2;
		if ( $shift < 1 ) {
			return $content;
		}
		return preg_replace_callback(
			'#<(/?)h([2-6])([\s>])#i',
			function ( $h ) use ( $shift ) {
				return '<' . $h[1] . 'h' . max( 2, (int) $h[2] - $shift ) . $h[3];
			},
			$content
		);
	},
	20
);
