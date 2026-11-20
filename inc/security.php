<?php
/**
 * Security hardening and front-end clean-up.
 *
 * - Hides the WordPress version and closes XML-RPC, pingbacks and application passwords.
 * - Stops username discovery (?author=N, /wp-json/wp/v2/users, login error messages).
 * - Sends security headers on every front-end and admin page.
 * - Removes unused head links and the front-end Heartbeat/Dashicons for visitors.
 *
 * Server-level rules (.htaccess, wp-config constants) and the malware scanner are
 * described in docs/INSTALLATION.md.
 *
 * @package ceori
 */

defined( 'ABSPATH' ) || exit;

/* Version disclosure ---------------------------------------------------- */

remove_action( 'wp_head', 'wp_generator' );
add_filter( 'the_generator', '__return_empty_string' );

// Drop "?ver=<WordPress version>" from core assets (theme/plugin versions are kept for cache busting).
add_filter( 'style_loader_src', 'ceori_strip_core_version', 20 );
add_filter( 'script_loader_src', 'ceori_strip_core_version', 20 );

/**
 * Remove the ver query argument when it equals the WordPress version.
 *
 * @param string $src Asset URL.
 */
function ceori_strip_core_version( $src ) {
	if ( $src && false !== strpos( $src, 'ver=' . get_bloginfo( 'version' ) ) ) {
		$src = remove_query_arg( 'ver', $src );
	}
	return $src;
}

/* XML-RPC, pingbacks, application passwords ----------------------------- */

// The site uses no XML-RPC clients: refuse every request – unless a plugin that needs remote
// access (Jetpack, WooCommerce's mobile app) is installed, which then works as usual.
if ( ! ceori_remote_access_needed() ) {
	if ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) {
		status_header( 403 );
		exit;
	}
	add_filter( 'xmlrpc_enabled', '__return_false' );
	// Application passwords (WordPress app, connected services): administrators only.
	add_filter(
		'wp_is_application_passwords_available_for_user',
		function ( $available, $user ) {
			return $available && user_can( $user, 'manage_options' );
		},
		10,
		2
	);
}
add_filter( 'pings_open', '__return_false', 20 );
add_filter(
	'wp_headers',
	function ( $headers ) {
		unset( $headers['X-Pingback'] );
		return $headers;
	}
);
add_filter(
	'xmlrpc_methods',
	function ( $methods ) {
		return ceori_remote_access_needed() ? $methods : array();
	}
);

/* Username enumeration -------------------------------------------------- */

add_action(
	'template_redirect',
	function () {
		if ( ! is_user_logged_in() && ( is_author() || isset( $_GET['author'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			wp_safe_redirect( home_url( '/' ), 301 );
			exit;
		}
	},
	0
);

add_filter(
	'rest_endpoints',
	function ( $endpoints ) {
		if ( ! is_user_logged_in() ) {
			unset( $endpoints['/wp/v2/users'], $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );
			// Specialists, testimonials, results and enquiries are dashboard content shown through
			// the theme (which applies the consent checks); only the editor needs their REST routes.
			foreach ( array_keys( $endpoints ) as $route ) {
				if ( 0 === strpos( $route, '/wp/v2/ceori_' ) ) {
					unset( $endpoints[ $route ] );
				}
			}
		}
		return $endpoints;
	}
);

// Author archives and the author sitemap reveal usernames too.
add_filter(
	'wp_sitemaps_add_provider',
	function ( $provider, $name ) {
		return 'users' === $name ? false : $provider;
	},
	10,
	2
);

// Same message for a wrong username and a wrong password.
add_filter(
	'login_errors',
	function () {
		return __( '<strong>Error:</strong> the login details are incorrect.', 'ceori' );
	}
);

/* Security headers ------------------------------------------------------ */

/**
 * Send the security headers.
 */
function ceori_security_headers() {
	if ( headers_sent() ) {
		return;
	}
	header( 'X-Content-Type-Options: nosniff' );
	header( 'X-Frame-Options: SAMEORIGIN' );
	header( 'Referrer-Policy: strict-origin-when-cross-origin' );
	header( 'Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=(), usb=()' );
	if ( is_ssl() && 'production' === wp_get_environment_type() ) {
		header( 'Strict-Transport-Security: max-age=31536000' );
	}
}
add_action( 'send_headers', 'ceori_security_headers' );
add_action( 'admin_init', 'ceori_security_headers' );
add_action( 'login_init', 'ceori_security_headers' );

/* Front-end clean-up (performance) -------------------------------------- */

remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );
remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
remove_action( 'template_redirect', 'wp_shortlink_header', 11 );

add_action(
	'wp_enqueue_scripts',
	function () {
		// Other plugins may use Dashicons on the front end without declaring it.
		if ( is_user_logged_in() || ceori_page_has_foreign_assets() ) {
			return;
		}
		// Visitors never need the admin icon font or the editor heartbeat. Dequeue (not
		// deregister) so anything that genuinely depends on them still loads them.
		wp_dequeue_style( 'dashicons' );
		wp_dequeue_script( 'heartbeat' );
	},
	100
);
