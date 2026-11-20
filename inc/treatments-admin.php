<?php
/**
 * "Treatments" in the dashboard.
 *
 * Treatments stay ordinary Pages with the Treatment template (so their URLs never change);
 * this gives them their own menu: a list of just the treatment pages with photo and category,
 * "Add New Treatment" (a draft page with the treatment template and the standard Elementor
 * sections ready to fill in), and Treatment categories.
 *
 * @package ceori
 */

defined( 'ABSPATH' ) || exit;

const CEORI_TREATMENT_TEMPLATE = 'page-treatment.php';

/**
 * Whether the current admin request is the Treatments list.
 */
function ceori_is_treatment_list() {
	global $pagenow;
	return is_admin() && 'edit.php' === $pagenow && isset( $_GET['ceori_type'] ) && 'treatment' === $_GET['ceori_type'] // phpcs:ignore WordPress.Security.NonceVerification
		&& isset( $_GET['post_type'] ) && 'page' === $_GET['post_type']; // phpcs:ignore WordPress.Security.NonceVerification
}

/**
 * Whether a page is a treatment page.
 *
 * @param int $post_id Post ID.
 */
function ceori_is_treatment( $post_id ) {
	return 'page' === get_post_type( $post_id ) && CEORI_TREATMENT_TEMPLATE === get_page_template_slug( $post_id );
}

/**
 * Treatments list URL.
 *
 * @param array $args Extra query args.
 */
function ceori_treatments_url( $args = array() ) {
	return add_query_arg(
		array_merge(
			array(
				'post_type'  => 'page',
				'ceori_type' => 'treatment',
			),
			$args
		),
		admin_url( 'edit.php' )
	);
}

add_action(
	'admin_menu',
	function () {
		$list = 'edit.php?post_type=page&ceori_type=treatment';
		add_menu_page( __( 'Treatments', 'ceori' ), __( 'Treatments', 'ceori' ), 'edit_pages', $list, '', 'dashicons-heart', '20.5' );
		add_submenu_page( $list, __( 'All Treatments', 'ceori' ), __( 'All Treatments', 'ceori' ), 'edit_pages', $list );
		add_submenu_page( $list, __( 'Add New Treatment', 'ceori' ), __( 'Add New Treatment', 'ceori' ), 'publish_pages', 'ceori-new-treatment', '__return_null' );
		add_submenu_page( $list, __( 'Treatment categories', 'ceori' ), __( 'Categories', 'ceori' ), 'manage_categories', 'edit-tags.php?taxonomy=treatment_category&post_type=page' );
		// Categories now live under Treatments only.
		remove_submenu_page( 'edit.php?post_type=page', 'edit-tags.php?taxonomy=treatment_category&amp;post_type=page' );
	},
	20
);

// Highlight the Treatments menu on its list, its categories and while editing a treatment.
add_filter(
	'parent_file',
	function ( $parent ) {
		global $pagenow, $taxonomy, $post;
		$list = 'edit.php?post_type=page&ceori_type=treatment';
		if ( ceori_is_treatment_list() || ( 'edit-tags.php' === $pagenow || 'term.php' === $pagenow ) && 'treatment_category' === $taxonomy ) {
			return $list;
		}
		if ( 'post.php' === $pagenow && $post && ceori_is_treatment( $post->ID ) ) {
			return $list;
		}
		return $parent;
	},
	999
);
add_filter(
	'submenu_file',
	function ( $submenu ) {
		global $pagenow, $taxonomy, $post;
		if ( ( 'edit-tags.php' === $pagenow || 'term.php' === $pagenow ) && 'treatment_category' === $taxonomy ) {
			return 'edit-tags.php?taxonomy=treatment_category&post_type=page';
		}
		if ( ceori_is_treatment_list() || ( 'post.php' === $pagenow && $post && ceori_is_treatment( $post->ID ) ) ) {
			return 'edit.php?post_type=page&ceori_type=treatment';
		}
		return $submenu;
	},
	999
);

// The list shows only treatment pages, under Treatments wording.
add_action(
	'pre_get_posts',
	function ( $query ) {
		if ( ! ceori_is_treatment_list() || ! $query->is_main_query() ) {
			return;
		}
		$query->set( 'meta_key', '_wp_page_template' );
		$query->set( 'meta_value', CEORI_TREATMENT_TEMPLATE );
		if ( ! $query->get( 'orderby' ) ) {
			$query->set( 'orderby', 'title' );
			$query->set( 'order', 'ASC' );
		}
	}
);
add_action(
	'load-edit.php',
	function () {
		if ( ! ceori_is_treatment_list() ) {
			return;
		}
		$labels                = get_post_type_object( 'page' )->labels;
		$labels->name          = __( 'Treatments', 'ceori' );
		$labels->add_new_item  = __( 'Add New Treatment', 'ceori' );
		$labels->search_items  = __( 'Search Treatments', 'ceori' );
		$labels->not_found     = __( 'No treatments found.', 'ceori' );
		$labels->all_items     = __( 'All Treatments', 'ceori' );
		$labels->items_list    = __( 'Treatments list', 'ceori' );
	}
);

// Keep the list's status links, search and "Add New" button inside the Treatments view.
add_filter(
	'views_edit-page',
	function ( $views ) {
		if ( ! ceori_is_treatment_list() ) {
			return $views;
		}
		foreach ( $views as $status => $link ) {
			// Count treatments only, not every page.
			$any   = array( 'publish', 'draft', 'pending', 'private', 'future' );
			$count = ( new WP_Query(
				array(
					'post_type'      => 'page',
					'post_status'    => in_array( $status, array( 'all', 'mine' ), true ) ? $any : $status,
					'author'         => 'mine' === $status ? get_current_user_id() : '',
					'meta_key'       => '_wp_page_template', // phpcs:ignore WordPress.DB.SlowDBQuery
					'meta_value'     => CEORI_TREATMENT_TEMPLATE, // phpcs:ignore WordPress.DB.SlowDBQuery
					'fields'         => 'ids',
					'posts_per_page' => 1,
				)
			) )->found_posts;
			if ( ! $count && 'all' !== $status ) {
				unset( $views[ $status ] );
				continue;
			}
			$link             = preg_replace( '#\(\d[\d,.\s]*\)#', '(' . number_format_i18n( $count ) . ')', $link );
			$views[ $status ] = preg_replace_callback(
				'#href="([^"]+)"#',
				fn( $m ) => 'href="' . esc_url( add_query_arg( 'ceori_type', 'treatment', html_entity_decode( $m[1] ) ) ) . '"',
				$link
			);
		}
		return $views;
	}
);
add_action(
	'admin_footer-edit.php',
	function () {
		if ( ! ceori_is_treatment_list() ) {
			return;
		}
		$new = admin_url( 'admin.php?page=ceori-new-treatment' );
		// WordPress re-selects "Pages" for any page list after the parent_file filter, so move the
		// highlight to Treatments here.
		echo '<script>(function(){var p=document.getElementById("menu-pages"),t=document.getElementById("toplevel_page_edit-post_type-page-ceori_type-treatment");if(p&&t){p.className=p.className.replace("wp-has-current-submenu wp-menu-open","wp-not-current-submenu");p.querySelectorAll(".current").forEach(function(e){e.classList.remove("current");});t.className=t.className.replace("wp-not-current-submenu","wp-has-current-submenu wp-menu-open");var l=t.querySelector(".wp-submenu li.wp-first-item")||t.querySelector(".wp-submenu li+li");if(l){l.classList.add("current");}t.querySelector("a").classList.add("wp-has-current-submenu");}var a=document.querySelector(".wrap .page-title-action");if(a){a.href=' . wp_json_encode( $new ) . ';}var f=document.getElementById("posts-filter");if(f&&!f.querySelector("[name=ceori_type]")){var i=document.createElement("input");i.type="hidden";i.name="ceori_type";i.value="treatment";f.appendChild(i);}})();</script>';
	}
);

// Photo column, and the summary under each title.
add_filter(
	'manage_pages_columns',
	function ( $cols ) {
		if ( ! ceori_is_treatment_list() ) {
			return $cols;
		}
		$out = array();
		foreach ( $cols as $key => $label ) {
			if ( 'title' === $key ) {
				$out['ceori_photo'] = __( 'Photo', 'ceori' );
			}
			if ( 'author' !== $key && 'comments' !== $key ) {
				$out[ $key ] = $label;
			}
		}
		return $out;
	}
);
add_action(
	'manage_pages_custom_column',
	function ( $col, $post_id ) {
		if ( 'ceori_photo' === $col ) {
			echo has_post_thumbnail( $post_id ) ? get_the_post_thumbnail( $post_id, array( 56, 56 ), array( 'style' => 'width:56px;height:56px;object-fit:cover;border-radius:8px' ) ) : '<span aria-hidden="true">—</span><span class="screen-reader-text">' . esc_html__( 'No photo', 'ceori' ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput
		}
	},
	10,
	2
);
add_action(
	'admin_head-edit.php',
	function () {
		if ( ceori_is_treatment_list() ) {
			echo '<style>.column-ceori_photo{width:64px}</style>';
		}
	}
);

/**
 * Standard Elementor sections of a treatment page, filled with the widgets' starter text.
 *
 * @param string $title Treatment name.
 * @return array
 */
function ceori_treatment_layout( $title ) {
	$section = function ( $widget, $settings, $label ) {
		$id = fn() => substr( md5( uniqid( '', true ) . wp_rand() ), 0, 7 );
		return array(
			'id'       => $id(),
			'elType'   => 'container',
			'isInner'  => false,
			'settings' => array(
				'content_width' => 'full',
				'flex_gap'      => array(
					'column'   => '0',
					'row'      => '0',
					'unit'     => 'px',
					'isLinked' => true,
				),
				'padding'       => array(
					'unit'     => 'px',
					'top'      => '0',
					'right'    => '0',
					'bottom'   => '0',
					'left'     => '0',
					'isLinked' => true,
				),
				'_title'        => $label,
			),
			'elements' => array(
				array(
					'id'         => $id(),
					'elType'     => 'widget',
					'widgetType' => $widget,
					'settings'   => $settings,
					'elements'   => array(),
				),
			),
		);
	};
	$book = array( 'url' => add_query_arg( 'treatment', rawurlencode( $title ), home_url( '/contact-us/' ) ) . '#appointment' );
	return array(
		$section(
			'ceori-page-hero',
			array(
				'text'        => 'One or two sentences on what this treatment does and who it is for.',
				'button_text' => 'Book Consultation',
				'button_link' => $book,
				'show_call'   => 'yes',
			),
			'Treatment hero'
		),
		$section(
			'ceori-split',
			array(
				'title' => 'What is <em>' . esc_html( $title ) . '</em>?',
				'text'  => '<p>Explain the treatment in plain words: what it is, how it works and what it is used for.</p>',
			),
			'About the treatment'
		),
		$section( 'ceori-info-cards', array(), 'Who may benefit & benefits' ),
		$section( 'ceori-steps', array(), 'How the treatment works' ),
		$section( 'ceori-callout', array( 'text' => '<p>Describe what the patient can expect before, during and after the treatment.</p>' ), 'What to expect' ),
		$section( 'ceori-faq', array( 'lead' => 'Still have a question about ' . $title . '? Our team is happy to help.' ), 'FAQ' ),
		$section( 'ceori-related', array(), 'Related treatments' ),
		$section( 'ceori-cta', array(), 'Call to action' ),
	);
}

// "Add New Treatment": create a draft treatment page with the standard sections, then open it.
add_action(
	'load-treatments_page_ceori-new-treatment',
	function () {
		if ( ! current_user_can( 'publish_pages' ) ) {
			wp_die( esc_html__( 'You are not allowed to add treatments.', 'ceori' ) );
		}
		$title = __( 'New treatment', 'ceori' );
		$id    = wp_insert_post(
			array(
				'post_type'     => 'page',
				'post_status'   => 'draft',
				'post_title'    => $title,
				'page_template' => CEORI_TREATMENT_TEMPLATE,
			),
			true
		);
		if ( is_wp_error( $id ) ) {
			wp_die( esc_html( $id->get_error_message() ) );
		}
		if ( did_action( 'elementor/loaded' ) ) {
			update_post_meta( $id, '_elementor_edit_mode', 'builder' );
			update_post_meta( $id, '_elementor_template_type', 'wp-page' );
			update_post_meta( $id, '_elementor_version', defined( 'ELEMENTOR_VERSION' ) ? ELEMENTOR_VERSION : '' );
			update_post_meta( $id, '_elementor_data', wp_slash( wp_json_encode( ceori_treatment_layout( $title ) ) ) );
		}
		wp_safe_redirect( admin_url( 'post.php?post=' . $id . '&action=edit&ceori_new_treatment=1' ) );
		exit;
	}
);

// Checklist on a treatment's edit screen: what the site needs for it to appear everywhere.
add_action(
	'add_meta_boxes_page',
	function ( $post ) {
		if ( ceori_is_treatment( $post->ID ) ) {
			add_meta_box( 'ceori_treatment_check', __( 'Treatment checklist', 'ceori' ), 'ceori_render_treatment_checklist', 'page', 'side', 'high' );
		}
	}
);

/**
 * Treatment checklist meta box.
 *
 * @param WP_Post $post Post.
 */
function ceori_render_treatment_checklist( $post ) {
	$items = array(
		array( 'New treatment' !== $post->post_title, __( 'Treatment name as the page title', 'ceori' ) ),
		array( (bool) get_the_terms( $post, 'treatment_category' ), __( 'Treatment category (shows it on its category page)', 'ceori' ) ),
		array( has_post_thumbnail( $post ), __( 'Featured image (card photo)', 'ceori' ) ),
		array( has_excerpt( $post ), __( 'Excerpt – one-sentence summary for cards', 'ceori' ) ),
		array( '' !== (string) get_post_meta( $post->ID, '_ceori_seo_description', true ), __( 'SEO description', 'ceori' ) ),
	);
	echo '<ul style="margin:0">';
	foreach ( $items as $item ) {
		echo '<li>' . ( $item[0] ? '✔ ' : '<span style="color:#b32d2e">✘</span> ' ) . esc_html( $item[1] ) . '</li>';
	}
	echo '</ul><p>' . esc_html__( 'Then click “Edit with Elementor” to write each section. Save this page first so Elementor uses the new title.', 'ceori' ) . '</p>';
}
