<?php
/**
 * Plugin compatibility.
 *
 * The theme's speed and security tweaks were built and tested against the plugins the site
 * already uses. Any other plugin you add must keep working exactly as its author intended, so:
 *
 * - On a page that loads front-end files (scripts/styles) from another plugin, the risky speed
 *   tweaks switch off for that page: jQuery stays where WordPress puts it (with jQuery Migrate),
 *   Elementor's scripts are not removed, Dashicons are not removed. Its stylesheets are never
 *   inlined or altered.
 * - SEO plugins take over titles, meta and sitemaps (see seo.php).
 * - Plugins that need XML-RPC or application passwords (Jetpack, WooCommerce mobile app…) get them.
 *
 * @package ceori
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whether the theme's optimisations are known to work with this plugin.
 *
 * @param string $slug Plugin folder name.
 * @return bool
 */
function ceori_known_plugin( $slug ) {
	$known = array(
		'elementor',
		'elementor-pro',
		'pro-elements',
		'header-footer-elementor',
		'litespeed-cache',
		'seo-by-rank-math',
		'seo-by-rank-math-pro',
		'ceori-content-import',
	);
	$known = (array) apply_filters( 'ceori_known_plugins', $known );
	return in_array( $slug, $known, true ) || 0 === strpos( $slug, 'hostinger' );
}

/**
 * Plugin folder an asset URL belongs to ('' for theme/core/other files).
 *
 * @param string $src Asset URL.
 * @return string
 */
function ceori_asset_plugin( $src ) {
	$path = (string) wp_parse_url( (string) $src, PHP_URL_PATH );
	foreach ( array( plugins_url(), content_url( 'mu-plugins' ) ) as $base ) {
		$base = rtrim( (string) wp_parse_url( $base, PHP_URL_PATH ), '/' ) . '/';
		if ( '/' !== $base && 0 === strpos( $path, $base ) ) {
			return strtok( substr( $path, strlen( $base ) ), '/' );
		}
	}
	return '';
}

/**
 * Whether an asset comes from a plugin the theme does not know (or a third-party script host).
 *
 * @param string $src    Asset URL.
 * @param bool   $script True for scripts: scripts from other domains count as foreign.
 * @return bool
 */
function ceori_is_foreign_asset( $src, $script = false ) {
	if ( ! $src || ! is_string( $src ) ) {
		return false;
	}
	$plugin = ceori_asset_plugin( $src );
	if ( '' !== $plugin ) {
		return ! ceori_known_plugin( $plugin );
	}
	if ( $script && preg_match( '#^(https?:)?//#i', $src ) ) {
		$host = strtolower( (string) wp_parse_url( ( 0 === strpos( $src, '//' ) ? 'https:' : '' ) . $src, PHP_URL_HOST ) );
		if ( ! $host || strtolower( (string) wp_parse_url( home_url(), PHP_URL_HOST ) ) === $host ) {
			return false;
		}
		// Script servers of the plugins/services the site already uses (Hostinger Reach, Google tag).
		$known = (array) apply_filters( 'ceori_known_script_hosts', array( 'hostinger.com', 'googletagmanager.com', 'google-analytics.com' ) );
		foreach ( $known as $domain ) {
			if ( $host === $domain || substr( $host, -strlen( '.' . $domain ) ) === '.' . $domain ) {
				return false;
			}
		}
		return true;
	}
	return false;
}

/**
 * Whether the current page loads front-end files from an unknown plugin.
 * Checked again at each step, since plugins may add files while the page is built.
 *
 * @return bool
 */
function ceori_page_has_foreign_assets() {
	if ( ! empty( $GLOBALS['ceori_foreign_assets'] ) ) {
		return true;
	}
	foreach ( array( wp_scripts(), wp_styles() ) as $i => $deps ) {
		$todo = $deps->queue;
		$seen = array();
		while ( $todo ) {
			$handle = array_shift( $todo );
			if ( isset( $seen[ $handle ] ) || empty( $deps->registered[ $handle ] ) ) {
				continue;
			}
			$seen[ $handle ] = true;
			$item            = $deps->registered[ $handle ];
			if ( ceori_is_foreign_asset( $item->src, 0 === $i ) ) {
				$GLOBALS['ceori_foreign_assets'] = $handle;
				return true;
			}
			$todo = array_merge( $todo, (array) $item->deps );
		}
	}
	return false;
}

/**
 * Content types the site search covers: pages, articles and any public, searchable type a plugin
 * adds (products, events, courses…). Theme-internal types and media files are left out.
 *
 * @return string[]
 */
function ceori_searchable_post_types() {
	$types = array_values(
		array_filter(
			get_post_types( array( 'public' => true, 'exclude_from_search' => false ) ),
			function ( $type ) {
				return ! in_array( $type, array( 'attachment', 'elementor_library', 'elementor-hf', 'e-floating-buttons', 'e-landing-page' ), true ) && 0 !== strpos( $type, 'ceori_' );
			}
		)
	);
	return (array) apply_filters( 'ceori_searchable_post_types', $types ? $types : array( 'page', 'post' ) );
}

/**
 * Comments section (WordPress comments, or whatever a comments/reviews plugin puts in its place).
 * Only where comments are switched on for that page/post, or it already has comments.
 */
function ceori_comments() {
	if ( is_singular() && ! is_front_page() && post_type_supports( get_post_type(), 'comments' ) && ( comments_open() || get_comments_number() ) ) {
		echo '<section class="ceori-section ceori-section--tight ceori-comments-area"><div class="ceori-wrap ceori-comments-wrap">';
		comments_template();
		echo '</div></section>';
	}
}

/**
 * Whether a plugin that relies on XML-RPC / application passwords is active.
 *
 * @return bool
 */
function ceori_remote_access_needed() {
	$needed = defined( 'JETPACK__VERSION' ) || defined( 'WC_VERSION' ) || class_exists( 'WooCommerce' );
	return (bool) apply_filters( 'ceori_remote_access_needed', $needed );
}
