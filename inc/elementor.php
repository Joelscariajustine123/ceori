<?php
/**
 * Elementor integration.
 *
 * - Registers the "CEORI" widget category and the CEORI widgets.
 * - Manrope and Playfair Display are served locally by the theme, so Elementor lists
 *   them as system fonts and never loads them from Google.
 * - Theme styles load after Elementor's so the design stays consistent, while every
 *   Elementor style control still wins (Elementor's generated CSS is more specific).
 *
 * @package ceori
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'elementor/elements/categories_registered',
	function ( $manager ) {
		$manager->add_category(
			'ceori',
			array(
				'title' => __( 'CEORI', 'ceori' ),
				'icon'  => 'fa fa-plug',
			)
		);
	}
);

add_action(
	'elementor/widgets/register',
	function ( $widgets ) {
		require_once CEORI_DIR . '/inc/elementor/class-widget-base.php';
		require_once CEORI_DIR . '/inc/elementor/widgets-home.php';
		require_once CEORI_DIR . '/inc/elementor/widgets-dynamic.php';
		require_once CEORI_DIR . '/inc/elementor/widgets-content.php';
		require_once CEORI_DIR . '/inc/elementor/widgets-specialist.php';
		require_once CEORI_DIR . '/inc/elementor/widgets-blog.php';
		$classes = array(
			'Hero',
			'Intro',
			'About',
			'Why',
			'Showcase',
			'Specialities',
			'Specialists',
			'Testimonials',
			'Results',
			'Latest_Posts',
			'Cta',
			'Category_Cards',
			'Treatment_Grid',
			'Specialist_Avatars',
			'Related',
			'Contact',
			'Clinic_Info',
			'Side_Panel',
			'Breadcrumbs',
			'Page_Hero',
			'Split',
			'Info_Cards',
			'Steps',
			'Callout',
			'Faq',
			'Principles',
			'Profile',
			'Specialist_Header',
			'Specialist_About',
			'Blog_Banner',
			'Post_List',
			'Post_Header',
			'Post_Body',
			'Related_Posts',
			'Not_Found',
			'Equipment',
			'Videos',
		);
		foreach ( $classes as $class ) {
			$fqcn = '\\Ceori\\Elementor\\' . $class;
			$widgets->register( new $fqcn() );
		}
	}
);

/**
 * Saved Elementor template that lays out a site screen without its own page (404, search,
 * category archive). Edit them under Templates → Saved Templates.
 *
 * @param string $key 404|search|archive.
 * @return int Template ID, or 0 when there is none (the theme's built-in layout is used).
 */
function ceori_elementor_template_id( $key ) {
	if ( ! did_action( 'elementor/loaded' ) ) {
		return 0;
	}
	$ids = get_posts(
		array(
			'post_type'      => 'elementor_library',
			'post_status'    => 'publish',
			'meta_key'       => '_ceori_template', // phpcs:ignore WordPress.DB.SlowDBQuery
			'meta_value'     => $key, // phpcs:ignore WordPress.DB.SlowDBQuery
			'fields'         => 'ids',
			'posts_per_page' => 1,
		)
	);
	return $ids && ceori_is_elementor_page( $ids[0] ) ? (int) $ids[0] : 0;
}

/**
 * Print a site-screen template.
 *
 * @param string $key 404|search|archive.
 * @return bool Whether a template was printed.
 */
function ceori_render_elementor_template( $key ) {
	$id = ceori_elementor_template_id( $key );
	if ( ! $id ) {
		return false;
	}
	echo '<div class="ceori-el">' . \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $id, true ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput
	return true;
}

/**
 * Articles per page of the first CEORI Article List widget in an Elementor layout.
 *
 * @param array $elements Elementor elements.
 * @return int 0 when the layout has no article list.
 */
function ceori_post_list_per_page( $elements ) {
	foreach ( (array) $elements as $el ) {
		if ( isset( $el['widgetType'] ) && 'ceori-post-list' === $el['widgetType'] ) {
			return max( 1, (int) ( $el['settings']['per_page'] ?? 9 ) );
		}
		$found = ceori_post_list_per_page( $el['elements'] ?? array() );
		if ( $found ) {
			return $found;
		}
	}
	return 0;
}

// A page number past the last page of a page's article list (/blog/page/9/) is "not found",
// not an empty page.
add_action(
	'wp',
	function () {
		$paged = (int) get_query_var( 'paged' );
		if ( $paged < 2 || ! is_page() ) {
			return;
		}
		$per_page = ceori_post_list_per_page( json_decode( (string) get_post_meta( get_queried_object_id(), '_elementor_data', true ), true ) );
		if ( ! $per_page || $paged <= (int) ceil( wp_count_posts()->publish / $per_page ) ) {
			return;
		}
		global $wp_query;
		$wp_query->set_404();
		status_header( 404 );
		nocache_headers();
	}
);

// Local fonts: list them as system fonts so Elementor does not request Google Fonts.
add_filter(
	'elementor/fonts/additional_fonts',
	function ( $fonts ) {
		$fonts['Manrope']          = 'system';
		$fonts['Playfair Display'] = 'system';
		return $fonts;
	}
);
// Elementor only requests Google Fonts someone actually picks (e.g. in another plugin's widget);
// the theme's own fonts above are local, so they never trigger a request.

// Load theme CSS after Elementor's frontend CSS.
add_action(
	'wp_enqueue_scripts',
	function () {
		if ( wp_style_is( 'ceori-theme', 'registered' ) && wp_style_is( 'elementor-frontend', 'registered' ) ) {
			global $wp_styles;
			$wp_styles->registered['ceori-theme']->deps[] = 'elementor-frontend';
		}
	},
	20
);

// Theme styles inside the Elementor editor panel preview are loaded via the front end;
// make the editor's own UI aware of the brand fonts too.
add_action(
	'elementor/editor/after_enqueue_styles',
	function () {
		wp_add_inline_style(
			'elementor-editor',
			'.elementor-element .icon .eicon-plug{color:#9a5d42}'
			// Simple style panel for CEORI widgets (see Widget::style_switch()).
			. '#elementor-panel.ceori-simple :is(.ceori-adv,.elementor-group-control-text-shadow,.elementor-group-control-box-shadow,.elementor-group-control-border,.elementor-control-marker_style,[class*="_shadow_type "]){display:none!important}'
		);
	}
);

// While a CEORI widget is open with "Show all style options" off, mark the panel so the CSS above
// hides the less common controls. Hidden controls keep their values and their CSS.
add_action(
	'elementor/editor/after_enqueue_scripts',
	function () {
		wp_add_inline_script(
			'elementor-editor',
			'window.addEventListener("elementor/init",function(){var panel=function(){return document.getElementById("elementor-panel");};'
			. 'var off=function(){var p=panel();if(p){p.classList.remove("ceori-simple");}};'
			. '["section","column","container","page_settings"].forEach(function(t){elementor.hooks.addAction("panel/open_editor/"+t,off);});'
			. 'elementor.hooks.addAction("panel/open_editor/widget",function(p,model){var s=model.get("settings");var sync=function(){var el=panel();if(el){el.classList.toggle("ceori-simple",0===String(model.get("widgetType")||"").indexOf("ceori-")&&"yes"!==s.get("ceori_more"));}};sync();s.on("change:ceori_more",sync);});});'
		);
	}
);
