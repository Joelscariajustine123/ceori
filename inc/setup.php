<?php
/**
 * Theme supports, assets, image sizes and front-end output tweaks.
 *
 * @package ceori
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'after_setup_theme',
	function () {
		load_theme_textdomain( 'ceori', CEORI_DIR . '/languages' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'custom-logo', array( 'flex-width' => true, 'flex-height' => true ) );
		add_theme_support( 'menus' );
		// The theme prints the Elementor header/footer itself (see header.php / footer.php).
		add_theme_support( 'header-footer-elementor' );
		register_nav_menus(
			array(
				'primary' => __( 'Main menu', 'ceori' ),
			)
		);
		add_theme_support( 'wp-block-styles' );
		add_theme_support( 'editor-styles' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets', 'comment-list', 'comment-form' ) );
		// Shop plugins use their own page layouts inside the site header and footer.
		add_theme_support( 'woocommerce' );
		add_theme_support( 'wc-product-gallery-zoom' );
		add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'wc-product-gallery-slider' );
		add_editor_style( 'assets/css/theme.css' );

		// Pages carry the treatment intro text in their excerpt.
		add_post_type_support( 'page', 'excerpt' );

		add_image_size( 'ceori-card', 800, 600, true );
		add_image_size( 'ceori-portrait', 720, 900, true );
		add_image_size( 'ceori-wide', 1600, 900, true );
	}
);

add_filter(
	'image_size_names_choose',
	function ( $sizes ) {
		return array_merge(
			$sizes,
			array(
				'ceori-card'     => __( 'Card (4:3)', 'ceori' ),
				'ceori-portrait' => __( 'Portrait (4:5)', 'ceori' ),
				'ceori-wide'     => __( 'Wide (16:9)', 'ceori' ),
			)
		);
	}
);

// Serve WebP for uploaded JPEG/PNG photographs where the server supports it.
add_filter(
	'image_editor_output_format',
	function ( $formats ) {
		if ( wp_image_editor_supports( array( 'mime_type' => 'image/webp' ) ) ) {
			$formats['image/jpeg'] = 'image/webp';
		}
		return $formats;
	}
);

add_action(
	'wp_enqueue_scripts',
	function () {
		$css = CEORI_DIR . '/assets/css/theme.css';
		$js  = CEORI_DIR . '/assets/js/theme.js';
		wp_enqueue_style( 'ceori-theme', CEORI_URI . '/assets/css/theme.css', array(), CEORI_VERSION . '.' . filemtime( $css ) );
		wp_enqueue_script(
			'ceori-theme',
			CEORI_URI . '/assets/js/theme.js',
			array(),
			CEORI_VERSION . '.' . filemtime( $js ),
			array(
				'strategy'  => 'defer',
				'in_footer' => true,
			)
		);
		wp_localize_script(
			'ceori-theme',
			'ceoriTheme',
			array(
				'restUrl' => esc_url_raw( rest_url( 'ceori/v1/enquiry' ) ),
				'searchUrl' => esc_url_raw( rest_url( 'ceori/v1/search' ) ),
				'nonce'   => wp_create_nonce( 'wp_rest' ),
				'i18n'    => array(
					'sending' => __( 'Sending…', 'ceori' ),
					'error'   => __( 'Sorry, something went wrong. Please call us or try again.', 'ceori' ),
					'prev'    => __( 'Previous', 'ceori' ),
					'next'    => __( 'Next', 'ceori' ),
					'close'   => __( 'Close', 'ceori' ),
					'copied'  => __( 'Link copied', 'ceori' ),
					'openWhatsapp' => __( 'Open WhatsApp', 'ceori' ),
					/* translators: %s: search words */
					'allResults'   => __( 'See all results for “%s”', 'ceori' ),
					/* translators: %s: search words */
					'noResults'    => __( 'No matches for “%s”. Try another word.', 'ceori' ),
					'openEmail'    => __( 'Open email', 'ceori' ),
				),
			)
		);
	}
);

// Preload the two font files used above the fold, and flag JS support before first paint.
add_action(
	'wp_head',
	function () {
		echo "<script>document.documentElement.classList.add('js');</script>\n";
		foreach ( array( 'manrope-latin.woff2', 'playfair-display-italic-latin.woff2' ) as $font ) {
			printf( '<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n", esc_url( CEORI_URI . '/assets/fonts/' . $font ) );
		}
	},
	2
);

// Load the front-page hero image eagerly with high priority (LCP element).
add_filter(
	'wp_get_attachment_image_attributes',
	function ( $attr ) {
		if ( isset( $attr['class'] ) && false !== strpos( $attr['class'], 'is-lcp' ) ) {
			$attr['loading']       = 'eager';
			$attr['fetchpriority'] = 'high';
		}
		return $attr;
	}
);
add_filter(
	'render_block_core/image',
	function ( $html, $block ) {
		if ( ! empty( $block['attrs']['className'] ) && false !== strpos( $block['attrs']['className'], 'is-lcp' ) ) {
			$html = str_replace( ' loading="lazy"', '', $html );
			$html = preg_replace( '/<img /', '<img fetchpriority="high" ', $html, 1 );
		}
		return $html;
	},
	10,
	2
);

/**
 * Google Tag Manager, unless a tracking plugin already outputs it (avoids duplicate installs).
 */
function ceori_gtm_enabled() {
	$id = trim( (string) ceori_option( 'gtm_id' ) );
	if ( '' === $id || '1' !== (string) ceori_option( 'gtm_output' ) || wp_get_environment_type() === 'local' ) {
		return false;
	}
	// GTM4WP and Site Kit both install the container themselves.
	if ( defined( 'GTM4WP_VERSION' ) || defined( 'GOOGLESITEKIT_VERSION' ) ) {
		return false;
	}
	return (bool) apply_filters( 'ceori_output_gtm', preg_match( '/^GTM-[A-Z0-9]+$/', $id ) );
}

add_action(
	'wp_head',
	function () {
		if ( ! ceori_gtm_enabled() ) {
			return;
		}
		$id = esc_js( ceori_option( 'gtm_id' ) );
		// The dataLayer and start time are set at once (no events are lost); the 116 KB
		// container itself loads on the visitor's first scroll, tap or key press – or 6 s
		// after the page has loaded – so it never competes with the first render on phones.
		echo "<script>(function(w,d,i){w.dataLayer=w.dataLayer||[];w.dataLayer.push({'gtm.start':new Date().getTime(),event:'gtm.js'});var done=0,ev=['scroll','pointerdown','keydown','touchstart'];function l(){if(done){return;}done=1;ev.forEach(function(e){w.removeEventListener(e,l,{passive:true});});var j=d.createElement('script');j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i;d.head.appendChild(j);}ev.forEach(function(e){w.addEventListener(e,l,{passive:true});});function later(){setTimeout(l,6000);}d.readyState==='complete'?later():w.addEventListener('load',later);})(window,document,'{$id}');</script>\n";
	},
	1
);
add_action(
	'wp_body_open',
	function () {
		if ( ceori_gtm_enabled() ) {
			printf( '<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=%s" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>', esc_attr( ceori_option( 'gtm_id' ) ) );
		}
	}
);

// Back-to-top button and the optional WhatsApp button, printed once per page.
add_action(
	'wp_footer',
	function () {
		printf(
			'<a class="ceori-to-top" href="#top" aria-label="%s">%s</a>',
			esc_attr__( 'Back to top', 'ceori' ),
			ceori_icon( 'arrow-up' ) // phpcs:ignore WordPress.Security.EscapeOutput
		);
		$wa = ceori_whatsapp_href();
		if ( $wa ) {
			printf(
				'<a class="ceori-whatsapp" href="%s" target="_blank" rel="noopener" aria-label="%s">%s</a>',
				esc_url( $wa ),
				esc_attr__( 'Chat with CEORI on WhatsApp (opens in a new tab)', 'ceori' ),
				ceori_icon( 'whatsapp' ) // phpcs:ignore WordPress.Security.EscapeOutput
			);
		}
	}
);

add_action(
	'wp_body_open',
	function () {
		echo '<span id="top" class="ceori-top-anchor"></span>';
	},
	0
);

// Local QA only: ?ceori_nolazy=1 disables lazy loading so full-page screenshots render every image.
if ( 'local' === wp_get_environment_type() && isset( $_GET['ceori_nolazy'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
	add_filter( 'wp_lazy_loading_enabled', '__return_false' );
	add_filter(
		'wp_get_attachment_image_attributes',
		function ( $attr ) {
			unset( $attr['loading'] );
			return $attr;
		},
		99
	);
}

// Remove emoji scripts; the theme does not need them.
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );

// Shorter, cleaner excerpts for cards.
add_filter(
	'excerpt_length',
	function () {
		return 22;
	}
);
add_filter(
	'excerpt_more',
	function () {
		return '…';
	}
);

// Register the pattern categories used by the theme's patterns.
add_action(
	'init',
	function () {
		register_block_pattern_category( 'ceori-home', array( 'label' => __( 'CEORI – Homepage sections', 'ceori' ) ) );
		register_block_pattern_category( 'ceori-pages', array( 'label' => __( 'CEORI – Page sections', 'ceori' ) ) );
		register_block_pattern_category( 'ceori-treatment', array( 'label' => __( 'CEORI – Treatment page', 'ceori' ) ) );
	}
);

// Block style variations used across the patterns.
add_action(
	'init',
	function () {
		register_block_style( 'core/button', array( 'name' => 'outline-ceori', 'label' => __( 'Outline', 'ceori' ) ) );
		register_block_style( 'core/button', array( 'name' => 'text-arrow', 'label' => __( 'Text link with arrow', 'ceori' ) ) );
		register_block_style( 'core/heading', array( 'name' => 'eyebrow', 'label' => __( 'Eyebrow label', 'ceori' ) ) );
		register_block_style( 'core/list', array( 'name' => 'checks', 'label' => __( 'Check marks', 'ceori' ) ) );
		register_block_style( 'core/group', array( 'name' => 'card', 'label' => __( 'Card', 'ceori' ) ) );
		register_block_style( 'core/details', array( 'name' => 'faq', 'label' => __( 'FAQ item', 'ceori' ) ) );
		register_block_style( 'core/image', array( 'name' => 'arch', 'label' => __( 'Arched top', 'ceori' ) ) );
	}
);

/*
 * One-page menu on the home page: Treatments scrolls to the home section (#treatments) in the
 * header menu and the side panel. About Us, Contact Us, other pages, Blog and the treatment
 * sub-menu items keep their normal links.
 */

/**
 * Home-section URL for a menu link, or null to leave it unchanged.
 *
 * @param string $url Menu link URL.
 * @return string|null
 */
function ceori_home_section_url( $url ) {
	if ( is_admin() || ! is_front_page() || ! $url ) {
		return null;
	}
	// "Contact Us" always opens the Contact page (form, map and details), even from the home page.
	$sections = array(
		'treatments' => 'treatments',
	);
	$path = trim( (string) wp_parse_url( $url, PHP_URL_PATH ), '/' );
	$home = trim( (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH ), '/' );
	$slug = trim( '' !== $home && 0 === strpos( $path, $home ) ? substr( $path, strlen( $home ) ) : $path, '/' );
	return isset( $sections[ $slug ] ) && ! wp_parse_url( $url, PHP_URL_FRAGMENT ) ? home_url( '/#' . $sections[ $slug ] ) : null;
}

// Header menu: Header & Footer Builder's walker skips nav_menu_link_attributes, so change the
// top-level items themselves (sub-menu items keep their own pages).
add_filter(
	'wp_nav_menu_objects',
	function ( $items ) {
		foreach ( $items as $item ) {
			$section = empty( $item->menu_item_parent ) ? ceori_home_section_url( $item->url ) : null;
			if ( $section ) {
				$item->url = $section;
			}
		}
		return $items;
	}
);

/**
 * Feature/section links stored as home sections (e.g. /#contact) point to the real page when
 * the widget is used anywhere other than the home page.
 *
 * @param string $url Link URL.
 * @return string
 */
function ceori_section_link( $url ) {
	if ( is_front_page() || 0 !== strpos( (string) $url, home_url( '/#' ) ) ) {
		return $url;
	}
	$pages = array(
		'about'      => '/about-us/',
		'treatments' => '/treatments/',
		'contact'    => '/contact-us/#appointment',
	);
	$key = substr( $url, strlen( home_url( '/#' ) ) );
	return isset( $pages[ $key ] ) ? home_url( $pages[ $key ] ) : $url;
}

// Side panel (Navigation block): only the item's own link, not its sub-menu.
add_filter(
	'render_block',
	function ( $html, $block ) {
		if ( ! in_array( $block['blockName'] ?? '', array( 'core/navigation-link', 'core/navigation-submenu' ), true ) ) {
			return $html;
		}
		$section = ceori_home_section_url( $block['attrs']['url'] ?? '' );
		if ( ! $section ) {
			return $html;
		}
		// Only before the sub-menu list: when the parent is a toggle button, the first link is a child's.
		$cut = strpos( $html, '<ul' );
		$own = false === $cut ? $html : substr( $html, 0, $cut );
		$own = preg_replace( '/href="[^"]*"/', 'href="' . esc_url( $section ) . '"', $own, 1 );
		return false === $cut ? $own : $own . substr( $html, $cut );
	},
	10,
	2
);
