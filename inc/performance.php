<?php
/**
 * Front-end performance for visitors (logged-in editors get the defaults, so the
 * Elementor editor is never affected).
 *
 * - Smaller image sizes so phones get right-sized images through srcset.
 * - jQuery moves to the footer (no render-blocking script in <head>); jQuery Migrate is dropped.
 * - Icon-font and duplicate widget stylesheets the site does not use are not loaded.
 * - The block-library stylesheet is skipped on Elementor-built pages.
 * - Only the hero image is high priority; logos get explicit sizes.
 * - /llms.txt is generated from live content for AI agents.
 *
 * @package ceori
 */

defined( 'ABSPATH' ) || exit;

/* Responsive image sizes ------------------------------------------------ */

add_action(
	'after_setup_theme',
	function () {
		// Same ratios as ceori-portrait (4:5) and ceori-card (4:3), so WordPress lists them in srcset.
		add_image_size( 'ceori-portrait-sm', 384, 480, true );
		add_image_size( 'ceori-card-sm', 480, 360, true );
		add_image_size( 'ceori-avatar', 160, 160, true );
	},
	20
);

/*
 * Realistic "sizes" for the theme's image sizes. WordPress' default ("100vw up to the file
 * width") makes phones download e.g. an 800px card for a 340px slot, a 300px avatar for a
 * 50px circle or the 622px logo for a 140px header logo. Images that pass their own sizes
 * (wp_get_attachment_image attributes) are left alone.
 */
add_filter(
	'wp_calculate_image_sizes',
	function ( $sizes, $size, $src, $meta, $attachment_id ) {
		if ( (int) $attachment_id && (int) get_theme_mod( 'custom_logo' ) === (int) $attachment_id ) {
			return '140px';
		}
		$w = is_array( $size ) ? (int) $size[0] : 0;
		$h = is_array( $size ) ? (int) $size[1] : 0;
		if ( ! $w || ! $h ) {
			return $sizes;
		}
		$ratio = $w / $h;
		if ( $w <= 160 && abs( $ratio - 1 ) < .02 ) {
			return '56px'; // ceori-avatar.
		}
		if ( $w <= 800 && abs( $ratio - 4 / 3 ) < .02 ) {
			return '(max-width: 599px) 88vw, (max-width: 1023px) 46vw, 400px'; // ceori-card.
		}
		if ( $w <= 720 && abs( $ratio - 4 / 5 ) < .02 ) {
			return '(max-width: 599px) 90vw, 400px'; // ceori-portrait.
		}
		return $sizes;
	},
	10,
	5
);

/**
 * Whether front-end optimisations apply to this request.
 */
function ceori_optimise_frontend() {
	return ! is_admin() && ! is_user_logged_in() && ! is_customize_preview() && ! isset( $_GET['elementor-preview'] ); // phpcs:ignore WordPress.Security.NonceVerification
}

/* Scripts --------------------------------------------------------------- */

add_action(
	'wp_enqueue_scripts',
	function () {
		// Another plugin's scripts may expect jQuery (and Migrate) in the usual place.
		if ( ! ceori_optimise_frontend() || ceori_page_has_foreign_assets() ) {
			return;
		}
		$scripts = wp_scripts();
		foreach ( array( 'jquery', 'jquery-core' ) as $handle ) {
			$scripts->add_data( $handle, 'group', 1 );
		}
		if ( isset( $scripts->registered['jquery'] ) ) {
			$scripts->registered['jquery']->deps = array_values( array_diff( $scripts->registered['jquery']->deps, array( 'jquery-migrate' ) ) );
		}
		// Header & Footer Builder adds scroll-to-top and reading-progress handlers to every
		// page; the site uses neither (the theme has its own back-to-top button).
		$after = $scripts->get_data( 'jquery', 'after' );
		if ( is_array( $after ) ) {
			$after = array_filter(
				$after,
				function ( $code ) {
					return false === strpos( (string) $code, 'hfe-scroll-to-top' ) && false === strpos( (string) $code, 'hfe-reading-progress' );
				}
			);
			$scripts->add_data( 'jquery', 'after', array_values( $after ) );
		}
	},
	99
);

/*
 * Script-free pages --------------------------------------------------------
 *
 * The CEORI widgets are driven by the theme's own theme.js (no jQuery). When every
 * widget on a page is one that needs no Elementor/jQuery JavaScript, visitors skip
 * jQuery, Elementor's frontend bundle and the Header & Footer Builder script – the
 * largest share of main-thread work on phones. Any other widget keeps them all.
 */

/**
 * Widgets that render fully without Elementor's frontend JavaScript.
 *
 * @param string $name Widget name.
 */
function ceori_widget_needs_no_js( $name ) {
	$static = array( 'heading', 'image', 'icon', 'icon-list', 'social-icons', 'text-editor', 'button', 'spacer', 'divider', 'nested-accordion', 'navigation-menu', 'site-logo', 'copyright', 'hfe-site-logo', 'hfe-copyright', 'hfe-navigation-menu' );
	return 0 === strpos( $name, 'ceori-' ) || in_array( $name, $static, true );
}

add_action(
	'elementor/frontend/widget/before_render',
	function ( $widget ) {
		if ( ! ceori_widget_needs_no_js( $widget->get_name() ) ) {
			$GLOBALS['ceori_needs_elementor_js'] = true;
		}
	}
);

add_action(
	'wp_print_footer_scripts',
	function () {
		if ( ! ceori_optimise_frontend() || ! empty( $GLOBALS['ceori_needs_elementor_js'] ) || ceori_page_has_foreign_assets() ) {
			return;
		}
		// Elementor Pro / Pro Elements add their own bundles on top of Elementor's; drop them too.
		foreach ( array( 'elementor-pro-frontend', 'pro-elements-handlers', 'elementor-pro-webpack-runtime', 'elementor-frontend', 'elementor-frontend-modules', 'elementor-webpack-runtime', 'hfe-frontend-js', 'jquery-ui-core', 'jquery' ) as $handle ) {
			wp_dequeue_script( $handle ); // jQuery still prints if anything left depends on it.
		}
	},
	1
);

// The side panel's Navigation block only needs its submenu toggle, which theme.js handles:
// visitors skip the Interactivity runtime (~0.25 s of main-thread work on a mid-range phone).
add_action(
	'wp_footer',
	function () {
		if ( ceori_optimise_frontend() && function_exists( 'wp_dequeue_script_module' ) ) {
			wp_dequeue_script_module( '@wordpress/block-library/navigation/view' );
		}
	},
	1
);

/* Styles ---------------------------------------------------------------- */

// Header & Footer Builder asks Elementor to print the header/footer CSS again inside <body>,
// although the same stylesheet is already in <head> (~29 KB of duplicate CSS to parse on phones).
add_filter(
	'elementor/frontend/builder_content/before_print_css',
	function ( $with_css ) {
		if ( ! $with_css || ! ceori_optimise_frontend() || wp_doing_ajax() || ! class_exists( '\Elementor\Plugin' ) ) {
			return $with_css;
		}
		$document = \Elementor\Plugin::$instance->documents->get_current();
		return $document && wp_style_is( 'elementor-post-' . $document->get_main_id(), 'done' ) ? false : $with_css;
	}
);

/**
 * Remove stylesheets visitors never need.
 */
function ceori_dequeue_unused_styles() {
	if ( ! ceori_optimise_frontend() ) {
		return;
	}
	// Icons are inline SVG (Elementor "Inline Font Icons"); only the menu caret uses Font Awesome Solid.
	$handles = array(
		'hfe-elementor-icons',
		'hfe-social-share-icons-brands',
		'hfe-social-share-icons-fontawesome',
		'hfe-icons-list',   // Duplicate of Elementor's widget-icon-list.
		'hfe-social-icons', // Duplicate of Elementor's widget-social-icons.
		'elementor-icons',
		// Elementor Pro widget styles HFE adds whenever it detects Pro; the site uses no Pro
		// widgets, and without the Pro files they 404 and block the first paint for seconds.
		'hfe-widget-blockquote',
		'hfe-mega-menu',
		'hfe-nav-menu-widget',
	);
	if ( is_singular() && ceori_is_elementor_page( get_queried_object_id() ) && wp_style_is( 'wp-block-library', 'enqueued' ) ) {
		// The CEORI widgets reuse a few core block layouts (columns, buttons, image, FAQ
		// details) and the side panel uses the Navigation block: load just those block
		// styles (~38 KB) instead of the whole block library (141 KB), before the theme CSS.
		$handles[] = 'wp-block-library';
		$deps      = array();
		foreach ( array( 'columns', 'image', 'button', 'buttons', 'details', 'navigation', 'navigation-link', 'list', 'group', 'search', 'categories' ) as $block ) {
			$handle = 'ceori-block-' . $block;
			wp_register_style( $handle, includes_url( 'blocks/' . $block . '/style.min.css' ), array(), get_bloginfo( 'version' ) );
			$deps[] = $handle;
		}
		$theme = wp_styles()->query( 'ceori-theme', 'registered' );
		if ( $theme ) {
			$theme->deps = array_values( array_unique( array_merge( $deps, $theme->deps ) ) );
		}
	}
	foreach ( $handles as $handle ) {
		wp_dequeue_style( $handle );
	}
}
add_action( 'wp_enqueue_scripts', 'ceori_dequeue_unused_styles', 100 );
add_action( 'wp_print_styles', 'ceori_dequeue_unused_styles', 0 );
add_action( 'wp_print_footer_scripts', 'ceori_dequeue_unused_styles', 0 );

/*
 * Inline stylesheets ------------------------------------------------------
 *
 * The first paint has to wait for every render-blocking stylesheet. On this hosting any
 * single request can stall for seconds, so a dozen small CSS files kept the page blank
 * (Speed Index). For visitors the site's own stylesheets are embedded in the (cached)
 * HTML instead: the first paint then needs one request. Relative url()s are rewritten
 * so fonts and images still resolve.
 */
add_filter(
	'style_loader_tag',
	function ( $tag, $handle, $href, $media ) {
		// Other plugins' stylesheets load exactly as their authors ship them.
		if ( ! ceori_optimise_frontend() || 'print' === $media || ceori_is_foreign_asset( $href ) ) {
			return $tag;
		}
		// The side panel (the only Navigation block) is hidden until opened: its ~21 KB of
		// styles load without blocking, so phones parse less CSS before the first paint.
		if ( in_array( $handle, array( 'ceori-block-navigation', 'ceori-block-navigation-link' ), true ) ) {
			return sprintf(
				'<link rel="stylesheet" id="%1$s-css" href="%2$s" media="print" onload="this.media=\'all\'"><noscript><link rel="stylesheet" href="%2$s"></noscript>' . "\n",
				esc_attr( $handle ),
				esc_url( $href )
			);
		}
		$css = ceori_inline_css_for( $href );
		if ( null === $css ) {
			return $tag;
		}
		if ( $media && ! in_array( $media, array( 'all', 'screen' ), true ) ) {
			$css = '@media ' . $media . '{' . $css . '}';
		}
		return '<style id="' . esc_attr( $handle ) . '-css">' . $css . "</style>\n";
	},
	10,
	4
);

/**
 * Minified contents of a local stylesheet, cached per file version.
 *
 * @param string $href Stylesheet URL.
 * @return string|null CSS, or null when the file is not local or too large.
 */
function ceori_inline_css_for( $href ) {
	$url  = strtok( (string) $href, '?' );
	$url  = set_url_scheme( $url, 'https' === wp_parse_url( home_url(), PHP_URL_SCHEME ) ? 'https' : 'http' );
	$map  = array(
		content_url()          => WP_CONTENT_DIR,
		includes_url()         => ABSPATH . WPINC . '/',
	);
	$file = '';
	foreach ( $map as $base => $dir ) {
		$base = set_url_scheme( $base, wp_parse_url( $url, PHP_URL_SCHEME ) );
		if ( 0 === strpos( $url, $base ) ) {
			$file = rtrim( $dir, '/' ) . '/' . ltrim( substr( $url, strlen( $base ) ), '/' );
			break;
		}
	}
	if ( ! $file || '.css' !== substr( $file, -4 ) || ! is_readable( $file ) || filesize( $file ) > 150 * KB_IN_BYTES ) {
		return null;
	}
	$keep = get_option( 'ceori_hfe_classes' );
	$keep = is_array( $keep ) && $keep ? array_flip( $keep ) : array();
	$key  = 'ceori_icss_' . md5( $file . filemtime( $file ) . $url . implode( ',', array_keys( $keep ) ) );
	$css = get_transient( $key );
	if ( false !== $css ) {
		return $css;
	}
	$css = (string) file_get_contents( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	$dir = trailingslashit( dirname( $url ) );
	// Relative url(...) → absolute, so fonts/images keep working from the page.
	$css = preg_replace_callback(
		'/url\(\s*([\'"]?)(?!data:|https?:|\/\/|\/|#)([^\'")]+)\1\s*\)/i',
		function ( $m ) use ( $dir ) {
			return 'url("' . $dir . $m[2] . '")';
		},
		$css
	);
	// Light minification: comments and surplus whitespace only.
	$css = preg_replace( '#/\*.*?\*/#s', '', $css );
	// Header & Footer Builder ships one stylesheet for all of its widgets and variants
	// (81 KB); keep only rules for HFE classes the site really renders.
	if ( $keep && false !== strpos( $file, 'header-footer-elementor/inc/widgets-css/frontend.css' ) ) {
		$css = ceori_css_keep_classes( $css, 'hfe-', $keep );
	}
	$css = preg_replace( '/\s+/', ' ', $css );
	$css = preg_replace( '/\s*([{};,>])\s*/', '$1', $css );
	$css = str_replace( '</style', '<\/style', trim( $css ) );
	set_transient( $key, $css, WEEK_IN_SECONDS );
	return $css;
}

/**
 * Drop CSS selectors that use a class with the given prefix that is not in $keep.
 * Rules inside @media/@supports are filtered too; @font-face, @keyframes etc. are kept.
 *
 * @param string $css    Stylesheet (comments already removed).
 * @param string $prefix Class prefix to filter, e.g. "hfe-".
 * @param array  $keep   Class names to keep (as array keys).
 * @return string
 */
function ceori_css_keep_classes( $css, $prefix, $keep ) {
	$out = '';
	$i   = 0;
	$n   = strlen( $css );
	$pattern = '/\.(' . preg_quote( $prefix, '/' ) . '[a-zA-Z0-9_-]+)/';
	while ( $i < $n ) {
		$open = strpos( $css, '{', $i );
		if ( false === $open ) {
			break;
		}
		$prelude = trim( substr( $css, $i, $open - $i ) );
		$depth   = 1;
		$j       = $open + 1;
		while ( $j < $n && $depth ) {
			if ( '{' === $css[ $j ] ) {
				++$depth;
			} elseif ( '}' === $css[ $j ] ) {
				--$depth;
			}
			++$j;
		}
		$body = substr( $css, $open + 1, $j - $open - 2 );
		if ( '' !== $prelude && '@' === $prelude[0] ) {
			if ( preg_match( '/^@(media|supports|layer|container)\b/i', $prelude ) ) {
				$inner = ceori_css_keep_classes( $body, $prefix, $keep );
				$out  .= '' !== trim( $inner ) ? $prelude . '{' . $inner . '}' : '';
			} else {
				$out .= $prelude . '{' . $body . '}';
			}
		} else {
			$selectors = array_filter(
				array_map( 'trim', explode( ',', $prelude ) ),
				function ( $selector ) use ( $pattern, $keep ) {
					preg_match_all( $pattern, $selector, $m );
					foreach ( $m[1] as $class ) {
						if ( ! isset( $keep[ $class ] ) ) {
							return false;
						}
					}
					return '' !== $selector;
				}
			);
			$out .= $selectors ? implode( ',', $selectors ) . '{' . $body . '}' : '';
		}
		$i = $j;
	}
	return $out;
}

/*
 * Learn which Header & Footer Builder classes the site renders (header, footer and any
 * page content). header.php starts the capture after <body>, footer.php ends it before
 * wp_footer(). The list only grows; a new class clears the page cache so the next view
 * gets the matching CSS, and saving a header/footer template resets the list.
 */

/**
 * Start capturing the page body.
 */
function ceori_body_capture_start() {
	if ( ceori_optimise_frontend() ) {
		$GLOBALS['ceori_body_capture'] = ob_start();
	}
}

/**
 * Print the captured body and record its HFE classes.
 */
function ceori_body_capture_end() {
	if ( empty( $GLOBALS['ceori_body_capture'] ) ) {
		return;
	}
	$html = (string) ob_get_clean();
	unset( $GLOBALS['ceori_body_capture'] );
	echo $html; // phpcs:ignore WordPress.Security.EscapeOutput -- already-rendered page markup.

	preg_match_all( '/\bclass\s*=\s*["\']([^"\']*)["\']/', $html, $attrs );
	preg_match_all( '/\bhfe-[a-zA-Z0-9_-]+/', implode( ' ', $attrs[1] ), $m );
	$known = get_option( 'ceori_hfe_classes' );
	$known = is_array( $known ) ? $known : array();
	$new   = array_diff( array_unique( $m[0] ), $known );
	if ( ! $new ) {
		return;
	}
	$all = array_values( array_unique( array_merge( $known, $new ) ) );
	sort( $all );
	update_option( 'ceori_hfe_classes', $all, true );
	// This response was built with the old list: do not cache it, and refresh cached pages.
	do_action( 'litespeed_control_set_nocache', 'ceori: new header/footer classes' );
	do_action( 'litespeed_purge_all' );
}

add_action(
	'save_post_elementor-hf',
	function () {
		delete_option( 'ceori_hfe_classes' );
	}
);

/* Images ---------------------------------------------------------------- */

// The site logo also sits in the hidden side panel: it must not compete with the hero image.
add_filter(
	'get_custom_logo_image_attributes',
	function ( $attr ) {
		unset( $attr['fetchpriority'] );
		$attr['loading'] = 'lazy';
		return $attr;
	}
);

// Home hero built from Elementor widgets: the portrait is the largest thing on the first screen, so
// it loads first (not lazily); the card photo is also on the first screen.
add_filter(
	'elementor/widget/render_content',
	function ( $content, $widget ) {
		if ( 'image' !== $widget->get_name() ) {
			return $content;
		}
		$classes = (string) $widget->get_settings( '_css_classes' );
		if ( false !== strpos( $classes, 'ceori-nhero__portrait' ) ) {
			$content = preg_replace( '/\sloading="lazy"/', '', $content );
			$content = preg_replace( '/<img\b(?![^>]*fetchpriority)/', '<img fetchpriority="high" loading="eager"', $content, 1 );
		} elseif ( false !== strpos( $classes, 'ceori-nhero__card-media' ) ) {
			$content = preg_replace( '/\sloading="lazy"/', ' loading="eager"', $content );
		}
		return $content;
	},
	10,
	2
);

// Icon-only "#top" links (Elementor Icon widget in the footer) need an accessible name.
add_filter(
	'elementor/widget/render_content',
	function ( $content ) {
		if ( false === strpos( $content, 'href="#top"' ) ) {
			return $content;
		}
		return preg_replace( '/<a\b(?![^>]*aria-label)([^>]*href="#top"[^>]*)>/', '<a aria-label="' . esc_attr__( 'Back to top', 'ceori' ) . '"$1>', $content );
	}
);

// Header & Footer Builder prints its logo without dimensions and with alt="default-logo".
add_filter(
	'elementor/widget/render_content',
	function ( $content ) {
		if ( false === strpos( $content, 'hfe-site-logo-img' ) ) {
			return $content;
		}
		return preg_replace_callback(
			'/<img\b[^>]*\bhfe-site-logo-img\b[^>]*>/',
			function ( $m ) {
				$tag = $m[0];
				if ( ! preg_match( '/\bwidth=/', $tag ) && preg_match( '/src="([^"]+)"/', $tag, $src ) ) {
					$size = ceori_image_size_from_url( $src[1] );
					if ( $size ) {
						$tag = str_replace( '<img ', '<img width="' . (int) $size[0] . '" height="' . (int) $size[1] . '" ', $tag );
					}
				}
				$tag = preg_replace( '/\salt="(default-logo)?"/', ' alt="' . esc_attr( get_bloginfo( 'name' ) ) . '"', $tag );
				return $tag;
			},
			$content
		);
	}
);

/**
 * Pixel size of an uploaded image from its URL (file name suffix, else the file itself).
 *
 * @param string $url Image URL.
 * @return array|false [width, height]
 */
function ceori_image_size_from_url( $url ) {
	if ( preg_match( '/-(\d+)x(\d+)\.[a-z0-9]+$/i', wp_parse_url( $url, PHP_URL_PATH ), $m ) ) {
		return array( (int) $m[1], (int) $m[2] );
	}
	$uploads = wp_get_upload_dir();
	if ( 0 === strpos( $url, $uploads['baseurl'] ) ) {
		$file = $uploads['basedir'] . substr( $url, strlen( $uploads['baseurl'] ) );
		$size = is_readable( $file ) ? wp_getimagesize( $file ) : false;
		if ( $size ) {
			return array( $size[0], $size[1] );
		}
	}
	return false;
}

/* llms.txt -------------------------------------------------------------- */

add_action(
	'parse_request',
	function ( $wp ) {
		if ( 'llms.txt' !== $wp->request ) {
			return;
		}
		nocache_headers();
		header( 'Content-Type: text/plain; charset=utf-8' );
		header( 'X-Robots-Tag: noindex' );
		echo ceori_llms_txt(); // phpcs:ignore WordPress.Security.EscapeOutput -- plain text.
		exit;
	},
	0
);

/**
 * Markdown summary of the site for large language models (https://llmstxt.org/).
 */
function ceori_llms_txt() {
	$name  = ceori_option( 'clinic_name' ) ? ceori_option( 'clinic_name' ) : get_bloginfo( 'name' );
	$lines = array(
		'# ' . $name,
		'',
		'> ' . wp_strip_all_tags( (string) ceori_option( 'footer_about' ) ),
		'',
		'Dermatologist-led clinic in Eranhipalam, Kozhikode (Calicut), Kerala, India, offering clinical dermatology, cosmetic dermatology, cosmetic gynaecology, cosmetic dentistry and cosmetic surgery. Consultations are by appointment.',
		'',
		'## Contact',
		'',
		'- [Home page](' . home_url( '/' ) . '): overview of the clinic, specialities and specialists',
		'- [Book an appointment](' . ceori_appointment_url() . '): request a consultation; the clinic replies to confirm a time',
		'- [Phone ' . ceori_option( 'phone_primary' ) . '](' . ceori_tel_href( ceori_option( 'phone_primary' ) ) . ')' . ( ceori_option( 'phone_secondary' ) ? ', [' . ceori_option( 'phone_secondary' ) . '](' . ceori_tel_href( ceori_option( 'phone_secondary' ) ) . ')' : '' ),
		'- [Email ' . ceori_option( 'email' ) . '](mailto:' . ceori_option( 'email' ) . ')',
		'- Address: ' . ceori_address_line(),
		'- Opening hours: ' . str_replace( array( "\r\n", "\n" ), '; ', (string) ceori_option( 'hours' ) ),
	);
	$link = function ( $post ) {
		$excerpt = wp_strip_all_tags( get_the_excerpt( $post ) );
		return '- [' . html_entity_decode( get_the_title( $post ), ENT_QUOTES ) . '](' . get_permalink( $post ) . ')' . ( $excerpt ? ': ' . wp_trim_words( $excerpt, 24, '…' ) : '' );
	};

	// While "Home page only" mode is on, other pages redirect home: do not advertise them.
	if ( '1' !== (string) ceori_option( 'home_only' ) ) {
		$sections = array(
			'Specialities'         => array( 'post_type' => 'page', 'meta_key' => '_wp_page_template', 'meta_value' => 'page-treatment-category.php' ), // phpcs:ignore WordPress.DB.SlowDBQuery
			'Specialists'          => array( 'post_type' => 'page', 'meta_key' => '_wp_page_template', 'meta_value' => 'page-doctor.php' ), // phpcs:ignore WordPress.DB.SlowDBQuery
			'Treatments'           => array( 'post_type' => 'page', 'meta_key' => '_wp_page_template', 'meta_value' => 'page-treatment.php' ), // phpcs:ignore WordPress.DB.SlowDBQuery
			'Skin care articles'   => array( 'post_type' => 'post', 'posts_per_page' => 20 ),
		);
		foreach ( $sections as $title => $args ) {
			$posts = get_posts(
				array_merge(
					array(
						'post_status'    => 'publish',
						'posts_per_page' => 100,
						'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
					),
					$args
				)
			);
			if ( $posts ) {
				$lines[] = '';
				$lines[] = '## ' . $title;
				$lines[] = '';
				foreach ( $posts as $post ) {
					$lines[] = $link( $post );
				}
			}
		}
		$lines[] = '';
		$lines[] = '## Optional';
		$lines[] = '';
		foreach ( array( 'about-us', 'contact-us' ) as $slug ) {
			$page = get_page_by_path( $slug );
			if ( $page ) {
				$lines[] = $link( $page );
			}
		}
	}
	$lines[] = '';
	$lines[] = 'Medical information on this site is general guidance, not a diagnosis. Treatment suitability is decided at a consultation.';
	return implode( "\n", $lines ) . "\n";
}
