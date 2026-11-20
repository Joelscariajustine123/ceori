<?php
/**
 * Home page only mode (CEORI Clinic → Home page only mode).
 *
 * When switched on, everyone except administrators sees only the home page: every other
 * front-end URL redirects (302) to the home page and every link on the page is disabled.
 * WP Admin, the admin bar, WordPress previews and the Elementor editor keep working.
 *
 * @package ceori
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whether home page only mode applies to the current request.
 */
function ceori_home_only_active() {
	if ( '1' !== (string) ceori_option( 'home_only' ) || is_admin() || is_customize_preview() ) {
		return false;
	}
	// Administrators always see the whole site.
	if ( current_user_can( 'manage_options' ) ) {
		return false;
	}
	// Editors previewing or building pages still need to see them.
	$editing = isset( $_GET['elementor-preview'] ) || isset( $_GET['preview'] ) || isset( $_GET['preview_id'] ); // phpcs:ignore WordPress.Security.NonceVerification
	return ! ( $editing && current_user_can( 'edit_posts' ) );
}

add_action(
	'template_redirect',
	function () {
		// Unknown URLs (e.g. /.well-known/ai-catalog.json) keep their honest 404 instead of
		// redirecting, so crawlers and AI agents do not receive the home page as a file.
		if ( ! ceori_home_only_active() || is_front_page() || is_404() || is_robots() || is_favicon() || wp_doing_ajax() ) {
			return;
		}
		nocache_headers();
		wp_safe_redirect( home_url( '/' ), 302 );
		exit;
	},
	1
);

// Keep other pages out of search results while the mode is on.
add_filter(
	'wp_sitemaps_enabled',
	function ( $enabled ) {
		return '1' === (string) ceori_option( 'home_only' ) ? false : $enabled;
	}
);

add_action(
	'wp_head',
	function () {
		if ( ! ceori_home_only_active() ) {
			return;
		}
		echo "<style id=\"ceori-home-only\">a:not(#wpadminbar a),a:not(#wpadminbar a) *{cursor:default!important}</style>\n";
	},
	99
);

add_action(
	'wp_footer',
	function () {
		if ( ! ceori_home_only_active() ) {
			return;
		}
		?>
<script id="ceori-home-only-js">
(function () {
	// Links keep their href (valid ARIA, crawlable – the pages redirect home anyway)
	// but are marked disabled and every activation is cancelled.
	function off(root) {
		(root.querySelectorAll ? root.querySelectorAll('a[href]:not([aria-disabled])') : []).forEach(function (a) {
			if (a.closest('#wpadminbar')) { return; } // WP Admin bar stays usable.
			a.removeAttribute('target');
			a.setAttribute('aria-disabled', 'true');
			a.setAttribute('tabindex', '-1');
		});
	}
	off(document);
	['click', 'auxclick'].forEach(function (type) {
		document.addEventListener(type, function (e) {
			var a = e.target.closest && e.target.closest('a');
			if (a && !a.closest('#wpadminbar')) { e.preventDefault(); e.stopPropagation(); }
		}, true);
	});
	// Links added later (side panel, scripts): one batched pass per frame, not one per DOM change.
	var queued = false;
	new MutationObserver(function () {
		if (queued) { return; }
		queued = true;
		requestAnimationFrame(function () { queued = false; off(document); });
	}).observe(document.body, { childList: true, subtree: true });
})();
</script>
		<?php
	},
	99
);
