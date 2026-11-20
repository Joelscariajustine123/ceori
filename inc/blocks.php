<?php
/**
 * Dynamic blocks. Each block renders on the server from dashboard-managed content
 * (pages, specialists, testimonials, results, clinic settings), so editors never need
 * to touch code. The editor UI is generated from the attribute definitions below by
 * assets/js/editor-blocks.js (server-side rendered previews with sidebar controls).
 *
 * @package ceori
 */

defined( 'ABSPATH' ) || exit;

/**
 * Block definitions: title, description, icon, attributes, render callback.
 * Attribute "options" become select controls in the editor.
 */
function ceori_block_definitions() {
	$category_options = array( '' => __( 'Current page’s category / all', 'ceori' ) );
	$terms            = get_terms(
		array(
			'taxonomy'   => 'treatment_category',
			'hide_empty' => false,
		)
	);
	if ( ! is_wp_error( $terms ) ) {
		foreach ( $terms as $term ) {
			$category_options[ $term->slug ] = $term->name;
		}
	}

	return array(
		'breadcrumbs'         => array(
			'title'       => __( 'Breadcrumbs', 'ceori' ),
			'description' => __( 'Breadcrumb trail for the current page.', 'ceori' ),
			'icon'        => 'arrow-right-alt2',
			'attributes'  => array(),
			'render'      => 'ceori_render_breadcrumbs',
		),
		'treatment-grid'      => array(
			'title'       => __( 'Treatment cards', 'ceori' ),
			'description' => __( 'Cards linking to treatment pages, from one category or a hand-picked list.', 'ceori' ),
			'icon'        => 'grid-view',
			'attributes'  => array(
				'category' => array(
					'type'    => 'string',
					'default' => '',
					'label'   => __( 'Category', 'ceori' ),
					'options' => $category_options,
				),
				'slugs'    => array(
					'type'    => 'string',
					'default' => '',
					'label'   => __( 'Or specific treatments (page slugs, comma-separated)', 'ceori' ),
				),
				'layout'   => array(
					'type'    => 'string',
					'default' => 'grid',
					'label'   => __( 'Layout', 'ceori' ),
					'options' => array(
						'grid'     => __( 'Grid of cards', 'ceori' ),
						'showcase' => __( 'Showcase (asymmetric, image-led)', 'ceori' ),
						'list'     => __( 'Compact list', 'ceori' ),
					),
				),
				'columns'  => array(
					'type'    => 'number',
					'default' => 3,
					'label'   => __( 'Columns', 'ceori' ),
					'min'     => 2,
					'max'     => 4,
				),
				'limit'    => array(
					'type'    => 'number',
					'default' => 0,
					'label'   => __( 'Maximum items (0 = all)', 'ceori' ),
					'min'     => 0,
					'max'     => 40,
				),
				'intro'    => array(
					'type'    => 'string',
					'default' => '',
					'label'   => __( 'Showcase text (shown beside the cards)', 'ceori' ),
				),
			),
			'render'      => 'ceori_render_treatment_grid',
		),
		'treatment-categories' => array(
			'title'       => __( 'Treatment categories', 'ceori' ),
			'description' => __( 'The five CEORI treatment categories with images, summaries and links.', 'ceori' ),
			'icon'        => 'screenoptions',
			'attributes'  => array(
				'layout'        => array(
					'type'    => 'string',
					'default' => 'panels',
					'label'   => __( 'Layout', 'ceori' ),
					'options' => array(
						'panels' => __( 'Full-width panels', 'ceori' ),
						'cards'  => __( 'Cards with treatment lists', 'ceori' ),
					),
				),
				'showTreatments' => array(
					'type'    => 'boolean',
					'default' => false,
					'label'   => __( 'List treatments inside each card', 'ceori' ),
				),
			),
			'render'      => 'ceori_render_treatment_categories',
		),
		'related-treatments'  => array(
			'title'       => __( 'Related treatments', 'ceori' ),
			'description' => __( 'Three related treatments for the current treatment page.', 'ceori' ),
			'icon'        => 'randomize',
			'attributes'  => array(
				'limit' => array(
					'type'    => 'number',
					'default' => 3,
					'label'   => __( 'Number of treatments', 'ceori' ),
					'min'     => 2,
					'max'     => 6,
				),
			),
			'render'      => 'ceori_render_related_treatments',
		),
		'doctors'             => array(
			'title'       => __( 'Specialists', 'ceori' ),
			'description' => __( 'Specialist cards, managed under Specialists in the dashboard.', 'ceori' ),
			'icon'        => 'groups',
			'attributes'  => array(
				'category' => array(
					'type'    => 'string',
					'default' => '',
					'label'   => __( 'Only specialists in category', 'ceori' ),
					'options' => array_merge( array( '' => __( 'All specialists', 'ceori' ) ), array_slice( $category_options, 1, null, true ) ),
				),
				'limit'    => array(
					'type'    => 'number',
					'default' => 0,
					'label'   => __( 'Maximum (0 = all)', 'ceori' ),
					'min'     => 0,
					'max'     => 12,
				),
				'layout'   => array(
					'type'    => 'string',
					'default' => 'carousel',
					'label'   => __( 'Layout', 'ceori' ),
					'options' => array(
						'carousel' => __( 'Scrolling row', 'ceori' ),
						'grid'     => __( 'Grid', 'ceori' ),
						'avatars'  => __( 'Avatar stack (for hero)', 'ceori' ),
					),
				),
				'caption'  => array(
					'type'    => 'string',
					'default' => '',
					'label'   => __( 'Avatar stack caption', 'ceori' ),
				),
			),
			'render'      => 'ceori_render_doctors',
		),
		'testimonials'        => array(
			'title'       => __( 'Patient testimonials', 'ceori' ),
			'description' => __( 'Carousel of approved testimonials. Hidden on the site until at least one testimonial is published with consent confirmed.', 'ceori' ),
			'icon'        => 'format-quote',
			'attributes'  => array(
				'limit' => array(
					'type'    => 'number',
					'default' => 8,
					'label'   => __( 'Maximum testimonials', 'ceori' ),
					'min'     => 1,
					'max'     => 20,
				),
			),
			'render'      => 'ceori_render_testimonials',
		),
		'results-gallery'     => array(
			'title'       => __( 'Results gallery', 'ceori' ),
			'description' => __( 'Filterable before & after gallery with lightbox. Only shows results marked as consented.', 'ceori' ),
			'icon'        => 'format-gallery',
			'attributes'  => array(
				'limit'   => array(
					'type'    => 'number',
					'default' => 12,
					'label'   => __( 'Maximum images', 'ceori' ),
					'min'     => 1,
					'max'     => 48,
				),
				'filters' => array(
					'type'    => 'boolean',
					'default' => true,
					'label'   => __( 'Show category filters', 'ceori' ),
				),
			),
			'render'      => 'ceori_render_results_gallery',
		),
		'appointment-form'    => array(
			'title'       => __( 'Appointment request form', 'ceori' ),
			'description' => __( 'Enquiry form. Submissions are emailed to the clinic and saved under Enquiries.', 'ceori' ),
			'icon'        => 'calendar-alt',
			'attributes'  => array(
				'heading' => array(
					'type'    => 'string',
					'default' => __( 'Request an appointment', 'ceori' ),
					'label'   => __( 'Form heading', 'ceori' ),
				),
				'note'    => array(
					'type'    => 'string',
					'default' => __( 'Share a few details and our team will call you to confirm a suitable time. This is a request – your appointment is confirmed only when we contact you.', 'ceori' ),
					'label'   => __( 'Text under the heading', 'ceori' ),
				),
			),
			'render'      => 'ceori_render_appointment_form',
		),
		'clinic-info'         => array(
			'title'       => __( 'Clinic info', 'ceori' ),
			'description' => __( 'Contact details, hours, social links or map from CEORI Clinic settings.', 'ceori' ),
			'icon'        => 'location',
			'attributes'  => array(
				'field' => array(
					'type'    => 'string',
					'default' => 'contact',
					'label'   => __( 'Show', 'ceori' ),
					'options' => array(
						'contact'  => __( 'Phone, email & address list', 'ceori' ),
						'phone'    => __( 'Phone numbers', 'ceori' ),
						'email'    => __( 'Email', 'ceori' ),
						'address'  => __( 'Address with map link', 'ceori' ),
						'hours'    => __( 'Opening hours', 'ceori' ),
						'social'   => __( 'Social media icons', 'ceori' ),
						'map'      => __( 'Embedded Google map', 'ceori' ),
						'about'    => __( 'Footer introduction text', 'ceori' ),
						'call'     => __( 'Call button', 'ceori' ),
						'copyright' => __( 'Copyright line', 'ceori' ),
					),
				),
			),
			'render'      => 'ceori_render_clinic_info',
		),
		'offcanvas'           => array(
			'title'       => __( 'Side panel menu', 'ceori' ),
			'description' => __( 'Hamburger button that opens a side panel with treatment links, location, contact details, social links and the appointment button.', 'ceori' ),
			'icon'        => 'menu-alt3',
			'attributes'  => array(),
			'render'      => 'ceori_render_offcanvas',
		),
		'related-posts'       => array(
			'title'       => __( 'Related articles', 'ceori' ),
			'description' => __( 'Three articles from the same category as the current post.', 'ceori' ),
			'icon'        => 'admin-post',
			'attributes'  => array(),
			'render'      => 'ceori_render_related_posts',
		),
		'share'               => array(
			'title'       => __( 'Share buttons', 'ceori' ),
			'description' => __( 'Share the current article on WhatsApp, Facebook, X or by copying the link.', 'ceori' ),
			'icon'        => 'share',
			'attributes'  => array(),
			'render'      => 'ceori_render_share',
		),
	);
}

add_action(
	'init',
	function () {
		wp_register_script(
			'ceori-editor-blocks',
			CEORI_URI . '/assets/js/editor-blocks.js',
			array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-block-editor', 'wp-server-side-render', 'wp-i18n' ),
			CEORI_VERSION . '.' . filemtime( CEORI_DIR . '/assets/js/editor-blocks.js' ),
			true
		);

		$editor_defs = array();
		foreach ( ceori_block_definitions() as $slug => $def ) {
			$attributes = array();
			foreach ( $def['attributes'] as $key => $attr ) {
				$attributes[ $key ] = array(
					'type'    => $attr['type'],
					'default' => $attr['default'],
				);
			}
			register_block_type(
				'ceori/' . $slug,
				array(
					'api_version'     => 3,
					'title'           => $def['title'],
					'description'     => $def['description'],
					'category'        => 'ceori',
					'attributes'      => $attributes,
					'supports'        => array(
						'align'   => array( 'wide', 'full' ),
						'html'    => false,
						'spacing' => array( 'margin' => true ),
					),
					'editor_script'   => 'ceori-editor-blocks',
					'render_callback' => function ( $attrs ) use ( $def ) {
						return call_user_func( $def['render'], $attrs );
					},
				)
			);
			$editor_defs[ 'ceori/' . $slug ] = array(
				'title'       => $def['title'],
				'description' => $def['description'],
				'icon'        => $def['icon'],
				'attributes'  => $def['attributes'],
			);
		}
		wp_add_inline_script( 'ceori-editor-blocks', 'window.ceoriBlocks = ' . wp_json_encode( $editor_defs ) . ';', 'before' );
	}
);

add_filter(
	'block_categories_all',
	function ( $categories ) {
		array_unshift(
			$categories,
			array(
				'slug'  => 'ceori',
				'title' => __( 'CEORI', 'ceori' ),
				'icon'  => null,
			)
		);
		return $categories;
	}
);

/**
 * Wrapper attributes for a dynamic block.
 *
 * @param string $class Extra class names.
 * @return string
 */
function ceori_block_wrapper( $class ) {
	return function_exists( 'get_block_wrapper_attributes' ) && WP_Block_Supports::$block_to_render
		? get_block_wrapper_attributes( array( 'class' => $class ) )
		: 'class="' . esc_attr( $class ) . '"';
}

/**
 * Whether the current render is an editor preview (ServerSideRender REST request).
 */
function ceori_is_editor_preview() {
	return defined( 'REST_REQUEST' ) && REST_REQUEST && isset( $_GET['context'] ) && 'edit' === $_GET['context']; // phpcs:ignore WordPress.Security.NonceVerification
}

/**
 * Notice shown only inside the editor when a block has nothing to display.
 *
 * @param string $message Message.
 * @return string
 */
function ceori_editor_notice( $message ) {
	return ceori_is_editor_preview() ? '<div class="ceori-editor-notice">' . esc_html( $message ) . '</div>' : '';
}

/**
 * Card image markup for a post, with a sensible fallback.
 *
 * @param int    $post_id Post ID.
 * @param string $size    Image size.
 * @param array  $attr    Extra attributes.
 * @return string
 */
function ceori_card_image( $post_id, $size = 'ceori-card', $attr = array() ) {
	if ( has_post_thumbnail( $post_id ) ) {
		return get_the_post_thumbnail( $post_id, $size, array_merge( array( 'loading' => 'lazy' ), $attr ) );
	}
	return '<span class="ceori-card__placeholder" aria-hidden="true">' . ceori_icon( 'sparkle' ) . '</span>';
}

/**
 * Treatment pages for a category slug (or every treatment), in menu order.
 *
 * @param string $category Category slug or ''.
 * @param int    $limit    Max items.
 * @param int[]  $exclude  Page IDs to exclude.
 * @return WP_Post[]
 */
function ceori_get_treatments( $category = '', $limit = 0, $exclude = array() ) {
	$args = array(
		'post_type'      => 'page',
		'post_status'    => 'publish',
		'posts_per_page' => $limit > 0 ? $limit : 100,
		'orderby'        => array(
			'menu_order' => 'ASC',
			'title'      => 'ASC',
		),
		'post__not_in'   => $exclude,
		'meta_key'       => '_wp_page_template',
		'meta_value'     => 'page-treatment.php',
		'no_found_rows'  => true,
	);
	if ( $category ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'treatment_category',
				'field'    => 'slug',
				'terms'    => $category,
			),
		);
	}
	return get_posts( $args );
}

/**
 * Category of the page currently being viewed (treatment or category landing page).
 *
 * @return WP_Term|null
 */
function ceori_current_category( $page_id = 0 ) {
	$page_id = $page_id ? (int) $page_id : get_queried_object_id();
	if ( ! $page_id ) {
		return null;
	}
	foreach ( ceori_treatment_categories() as $term ) {
		if ( (int) get_term_meta( $term->term_id, 'ceori_landing_page', true ) === $page_id ) {
			return $term;
		}
	}
	$terms = get_the_terms( $page_id, 'treatment_category' );
	return ( $terms && ! is_wp_error( $terms ) ) ? $terms[0] : null;
}

/*
 * ------------------------------------------------------------------
 * Render callbacks
 * ------------------------------------------------------------------
 */

/**
 * Breadcrumbs block.
 */
function ceori_render_breadcrumbs() {
	if ( ceori_is_editor_preview() ) {
		return '<nav class="ceori-breadcrumbs"><ol><li><a href="#">' . esc_html__( 'Home', 'ceori' ) . '</a></li><li><a href="#">' . esc_html__( 'Treatments', 'ceori' ) . '</a></li><li><span>' . esc_html__( 'Current page', 'ceori' ) . '</span></li></ol></nav>';
	}
	return ceori_breadcrumb_html();
}

/**
 * Treatment card markup.
 *
 * @param WP_Post $post    Treatment page.
 * @param string  $variant Card variant.
 * @param string  $size    Image size.
 * @param array   $img_attr Extra image attributes (e.g. sizes).
 * @return string
 */
function ceori_treatment_card( $post, $variant = 'grid', $size = 'ceori-card', $img_attr = array() ) {
	$url     = get_permalink( $post );
	$title   = get_the_title( $post );
	$excerpt = has_excerpt( $post ) ? get_the_excerpt( $post ) : wp_trim_words( wp_strip_all_tags( $post->post_content ), 20 );

	if ( 'list' === $variant ) {
		return '<li class="ceori-tlist__item"><a href="' . esc_url( $url ) . '"><span>' . esc_html( $title ) . '</span>' . ceori_icon( 'arrow-up-right' ) . '</a></li>';
	}

	$html  = '<article class="ceori-card ceori-card--' . esc_attr( $variant ) . ' reveal">';
	$html .= '<a class="ceori-card__media" href="' . esc_url( $url ) . '" tabindex="-1" aria-hidden="true">' . ceori_card_image( $post->ID, $size, $img_attr ) . '</a>';
	$html .= '<div class="ceori-card__body">';
	$html .= '<h3 class="ceori-card__title"><a href="' . esc_url( $url ) . '">' . esc_html( $title ) . '</a></h3>';
	if ( 'showcase' !== $variant ) {
		$html .= '<p class="ceori-card__text">' . esc_html( $excerpt ) . '</p>';
	}
	$html .= '<a class="ceori-link-arrow" href="' . esc_url( $url ) . '">' . ceori_link_text( __( 'Learn more', 'ceori' ), $title ) . ceori_icon( 'arrow-up-right' ) . '</a>';
	$html .= '</div></article>';
	return $html;
}

/**
 * Treatment grid block.
 *
 * @param array $a Attributes.
 * @return string
 */
function ceori_render_treatment_grid( $a ) {
	$layout = in_array( $a['layout'], array( 'grid', 'showcase', 'list' ), true ) ? $a['layout'] : 'grid';
	$posts  = array();

	if ( trim( (string) $a['slugs'] ) ) {
		foreach ( array_filter( array_map( 'trim', explode( ',', $a['slugs'] ) ) ) as $slug ) {
			$page = get_page_by_path( sanitize_title( $slug ) );
			if ( $page && 'publish' === $page->post_status ) {
				$posts[] = $page;
			}
		}
	} else {
		$category = $a['category'];
		if ( ! $category && ! ceori_is_editor_preview() ) {
			$term     = ceori_current_category( isset( $a['page_id'] ) ? (int) $a['page_id'] : 0 );
			$category = $term ? $term->slug : '';
		}
		$posts = ceori_get_treatments( $category, (int) $a['limit'] );
	}

	if ( ! $posts ) {
		return ceori_editor_notice( __( 'No published treatment pages match these settings yet.', 'ceori' ) );
	}

	if ( 'showcase' === $layout ) {
		$posts = array_slice( $posts, 0, 4 );
		$cols  = array( array(), array(), array() );
		foreach ( $posts as $i => $post ) {
			$cols[ min( $i, 3 ) === 3 ? 1 : min( $i, 2 ) ][] = $post;
		}
		$html  = '<div ' . ceori_block_wrapper( 'ceori-showcase' ) . '>';
		$html .= '<div class="ceori-showcase__col ceori-showcase__col--a">';
		foreach ( $cols[0] as $post ) {
			$html .= ceori_treatment_card( $post, 'showcase', 'ceori-portrait' );
		}
		$html .= '</div><div class="ceori-showcase__col ceori-showcase__col--b">';
		foreach ( $cols[1] as $post ) {
			$html .= ceori_treatment_card( $post, 'showcase', 'ceori-card' );
		}
		$html .= '</div><div class="ceori-showcase__col ceori-showcase__col--c">';
		// Intro and button sit together above the last card, so all three columns end level.
		$html .= '<div class="ceori-showcase__lead">' . ( $a['intro'] ? '<p class="ceori-showcase__intro">' . esc_html( $a['intro'] ) . '</p>' : '' )
			. '<a class="ceori-btn" href="' . esc_url( home_url( '/treatments/' ) ) . '">' . esc_html__( 'View all treatments', 'ceori' ) . ceori_icon( 'arrow-up-right' ) . '</a></div>';
		foreach ( $cols[2] as $post ) {
			$html .= ceori_treatment_card( $post, 'showcase', 'ceori-portrait' );
		}
		return $html . '</div></div>';
	}

	if ( 'list' === $layout ) {
		$items = '';
		foreach ( $posts as $post ) {
			$items .= ceori_treatment_card( $post, 'list' );
		}
		return '<ul ' . ceori_block_wrapper( 'ceori-tlist' ) . '>' . $items . '</ul>';
	}

	$cols  = max( 2, min( 4, (int) $a['columns'] ) );
	$html  = '<div ' . ceori_block_wrapper( 'ceori-grid ceori-grid--' . $cols ) . '>';
	foreach ( $posts as $post ) {
		$html .= ceori_treatment_card( $post, 'grid', 'ceori-card', array( 'sizes' => '(max-width: 599px) 88px, (max-width: 1023px) 46vw, 400px' ) ); // Phones show an 88px thumbnail.
	}
	return $html . '</div>';
}

/**
 * Treatment categories block.
 *
 * @param array $a Attributes.
 * @return string
 */
function ceori_render_treatment_categories( $a ) {
	$terms = ceori_treatment_categories();
	if ( ! $terms ) {
		return ceori_editor_notice( __( 'Add treatment categories under Pages → Treatment categories.', 'ceori' ) );
	}

	if ( 'cards' === $a['layout'] ) {
		$html = '<div ' . ceori_block_wrapper( 'ceori-catcards' ) . '>';
		foreach ( $terms as $i => $term ) {
			$url   = ceori_category_url( $term );
			$img   = (int) get_term_meta( $term->term_id, 'ceori_image', true );
			$html .= '<article class="ceori-catcard reveal" id="' . esc_attr( $term->slug ) . '">';
			$html .= '<a class="ceori-catcard__media" href="' . esc_url( $url ) . '" tabindex="-1" aria-hidden="true">' . ( $img ? wp_get_attachment_image( $img, 'ceori-card', false, array( 'loading' => 'lazy' ) ) : '' ) . '<span class="ceori-catcard__num">' . esc_html( sprintf( '%02d', $i + 1 ) ) . '</span></a>';
			$html .= '<div class="ceori-catcard__body"><h2 class="ceori-catcard__title"><a href="' . esc_url( $url ) . '">' . esc_html( $term->name ) . '</a></h2>';
			$html .= '<p>' . esc_html( (string) get_term_meta( $term->term_id, 'ceori_summary', true ) ) . '</p>';
			if ( $a['showTreatments'] ) {
				$items = '';
				foreach ( ceori_get_treatments( $term->slug ) as $post ) {
					$items .= ceori_treatment_card( $post, 'list' );
				}
				$html .= $items ? '<ul class="ceori-tlist ceori-tlist--compact">' . $items . '</ul>' : '';
			}
			$html .= '<a class="ceori-btn ceori-btn--outline" href="' . esc_url( $url ) . '">' . ceori_link_text( __( 'Explore treatments', 'ceori' ), $term->name ) . ceori_icon( 'arrow-up-right' ) . '</a>';
			$html .= '</div></article>';
		}
		return $html . '</div>';
	}

	// Skintiva-style strip: one shared background that cross-fades to the hovered category,
	// equal columns divided by thin lines, labels at the top, details revealed at the bottom.
	$bg   = '';
	$list = '';
	foreach ( $terms as $i => $term ) {
		$url   = ceori_category_url( $term );
		$img   = (int) get_term_meta( $term->term_id, 'ceori_image', true );
		$src   = $img ? (string) wp_get_attachment_image_url( $img, 'large' ) : '';
		$bg   .= $img ? wp_get_attachment_image(
			$img,
			'full',
			false,
			array(
				'class'      => 'ceori-panels__img' . ( 0 === $i ? ' is-active' : '' ),
				'loading'    => 'lazy',
				'alt'        => '',
				'sizes'      => '100vw',
				'data-panel' => $i,
			)
		) : '';
		$list .= '<li class="ceori-panel' . ( 0 === $i ? ' is-active' : '' ) . '" data-panel="' . (int) $i . '" style="--panel-img:url(' . esc_url( $src ) . ')">';
		$list .= '<a class="ceori-panel__link" href="' . esc_url( $url ) . '">';
		$list .= '<span class="ceori-panel__title">' . esc_html( $term->name ) . '</span>';
		$list .= '<span class="ceori-panel__reveal"><span class="ceori-panel__text">' . esc_html( (string) get_term_meta( $term->term_id, 'ceori_summary', true ) ) . '</span>';
		$list .= '<span class="ceori-panel__cta">' . esc_html__( 'Explore treatments', 'ceori' ) . ceori_icon( 'arrow-up-right' ) . '</span></span>';
		$list .= '</a></li>';
	}
	return '<div ' . ceori_block_wrapper( 'ceori-panels' ) . ' data-panels><div class="ceori-panels__bg" aria-hidden="true">' . $bg . '</div><ul class="ceori-panels__list">' . $list . '</ul></div>';
}

/**
 * Related treatments block.
 *
 * @param array $a Attributes.
 * @return string
 */
function ceori_render_related_treatments( $a ) {
	$limit   = max( 2, min( 6, (int) $a['limit'] ) );
	$page_id = ! empty( $a['page_id'] ) ? (int) $a['page_id'] : get_queried_object_id();
	if ( ceori_is_editor_preview() ) {
		$page_id = isset( $_GET['post_id'] ) ? absint( $_GET['post_id'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification
	}
	$posts = ceori_related_treatment_posts( $page_id, $limit );
	if ( ! $posts ) {
		return ceori_editor_notice( __( 'Related treatments appear here on published treatment pages.', 'ceori' ) );
	}

	$html = '<div ' . ceori_block_wrapper( 'ceori-grid ceori-grid--3' ) . '>';
	foreach ( $posts as $post ) {
		$html .= ceori_treatment_card( $post, 'grid' );
	}
	return $html . '</div>';
}

/**
 * Treatments related to a page: those listed in its ceori_related field, then others from the
 * same category, then any.
 *
 * @param int   $page_id Page ID.
 * @param int   $limit   Maximum.
 * @param int[] $exclude Page IDs to leave out (e.g. already shown as card items).
 * @return WP_Post[]
 */
function ceori_related_treatment_posts( $page_id, $limit, $exclude = array() ) {
	if ( $limit < 1 ) {
		return array();
	}
	$exclude = array_map( 'intval', (array) $exclude );
	$posts   = array();

	$manual = $page_id ? (string) get_post_meta( $page_id, 'ceori_related', true ) : '';
	foreach ( array_filter( array_map( 'trim', explode( ',', $manual ) ) ) as $ref ) {
		$post = is_numeric( $ref ) ? get_post( (int) $ref ) : get_page_by_path( $ref );
		if ( $post && 'publish' === $post->post_status && ! in_array( $post->ID, $exclude, true ) ) {
			$posts[ $post->ID ] = $post;
		}
	}
	if ( count( $posts ) < $limit && $page_id ) {
		$terms = get_the_terms( $page_id, 'treatment_category' );
		if ( $terms && ! is_wp_error( $terms ) ) {
			foreach ( ceori_get_treatments( $terms[0]->slug, $limit, array_merge( array( $page_id ), array_keys( $posts ), $exclude ) ) as $post ) {
				$posts[ $post->ID ] = $post;
			}
		}
	}
	$posts = array_slice( array_values( $posts ), 0, $limit );
	if ( ! $posts && ! $exclude ) {
		$posts = ceori_get_treatments( '', $limit, array( $page_id ) );
	}
	return $posts;
}

/**
 * Specialists block.
 *
 * @param array $a Attributes.
 * @return string
 */
function ceori_render_doctors( $a ) {
	$args = array(
		'post_type'      => 'ceori_doctor',
		'post_status'    => 'publish',
		'posts_per_page' => (int) $a['limit'] > 0 ? (int) $a['limit'] : 50,
		'orderby'        => array(
			'menu_order' => 'ASC',
			'title'      => 'ASC',
		),
		'no_found_rows'  => true,
	);
	if ( $a['category'] ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'treatment_category',
				'field'    => 'slug',
				'terms'    => $a['category'],
			),
		);
	}
	if ( 'avatars' === $a['layout'] ) {
		$args['posts_per_page'] = 50;
	}
	$doctors = get_posts( $args );
	if ( ! $doctors ) {
		return ceori_editor_notice( __( 'Add specialists under Specialists in the dashboard.', 'ceori' ) );
	}

	if ( 'avatars' === $a['layout'] ) {
		$html = '<div ' . ceori_block_wrapper( 'ceori-avatars' ) . '><span class="ceori-avatars__stack">';
		foreach ( array_slice( $doctors, 0, 3 ) as $doctor ) {
			// Portrait crop (not the square one, which cuts off the space above the head), shown
			// from the top so each face has a little room inside the circle.
			$html .= get_the_post_thumbnail(
				$doctor,
				'ceori-portrait-sm',
				array(
					'alt'     => '',
					'loading' => 'eager',
					'sizes'   => '56px',
				)
			);
		}
		$html .= '</span><span class="ceori-avatars__text"><strong>' . esc_html( (string) count( $doctors ) ) . '</strong> ' . esc_html( $a['caption'] ? $a['caption'] : __( 'specialists under one roof', 'ceori' ) ) . '</span></div>';
		return $html;
	}

	$layout = 'grid' === $a['layout'] ? 'grid' : 'carousel';
	$html   = '<div ' . ceori_block_wrapper( 'ceori-doctors ceori-doctors--' . $layout ) . '>';
	if ( 'carousel' === $layout ) {
		$html .= '<div class="ceori-scroller" data-scroller><div class="ceori-scroller__track" role="list">';
	} else {
		$html .= '<div class="ceori-doctors__grid" role="list">';
	}
	foreach ( $doctors as $doctor ) {
		$role    = (string) get_post_meta( $doctor->ID, 'designation', true );
		$quals   = (string) get_post_meta( $doctor->ID, 'qualifications', true );
		$profile = (int) get_post_meta( $doctor->ID, 'profile_page', true );
		$link    = $profile ? get_permalink( $profile ) : ceori_appointment_url();
		$html   .= '<div class="ceori-doctor reveal" role="listitem">';
		$html   .= '<div class="ceori-doctor__media">' . get_the_post_thumbnail( $doctor, 'ceori-portrait', array( 'loading' => 'lazy' ) ) . '</div>';
		$html   .= '<div class="ceori-doctor__body"><h3 class="ceori-doctor__name">' . esc_html( get_the_title( $doctor ) ) . '</h3>';
		$spec    = (string) get_post_meta( $doctor->ID, 'specialisation', true );
		$html   .= $role ? '<p class="ceori-doctor__role">' . esc_html( $role ) . '</p>' : '';
		$html   .= $spec ? '<p class="ceori-doctor__spec">' . esc_html( $spec ) . '</p>' : '';
		$html   .= $quals ? '<p class="ceori-doctor__quals">' . esc_html( $quals ) . '</p>' : '';
		$html   .= '<a class="ceori-link-arrow" href="' . esc_url( $link ) . '">' . ( $profile ? ceori_link_text( __( 'View profile', 'ceori' ), get_the_title( $doctor ) ) : esc_html__( 'Book consultation', 'ceori' ) ) . ceori_icon( 'arrow-up-right' ) . '</a>';
		// Bookings taken elsewhere (e.g. Malabar Hospitals): a direct link, opened in a new tab.
		$booking = (string) get_post_meta( $doctor->ID, 'external_url', true );
		if ( $booking ) {
			/* translators: %s: doctor name */
			$html .= '<a class="ceori-doctor__book" href="' . esc_url( $booking ) . '" target="_blank" rel="noopener">' . esc_html__( 'Book appointment', 'ceori' ) . '<span class="screen-reader-text">' . esc_html( sprintf( __( ' with %s (opens Malabar Hospitals in a new tab)', 'ceori' ), get_the_title( $doctor ) ) ) . '</span>' . ceori_icon( 'arrow-up-right' ) . '</a>';
		}
		$html   .= '</div></div>';
	}
	$html .= '</div>';
	if ( 'carousel' === $layout ) {
		$html .= '</div><div class="ceori-scroller__nav"><button type="button" class="ceori-round-btn" data-scroll="-1" aria-label="' . esc_attr__( 'Previous specialists', 'ceori' ) . '">' . ceori_icon( 'arrow-left' ) . '</button><button type="button" class="ceori-round-btn" data-scroll="1" aria-label="' . esc_attr__( 'Next specialists', 'ceori' ) . '">' . ceori_icon( 'arrow-right' ) . '</button></div>';
	}
	return $html . '</div>';
}

/**
 * Published, consented posts of a type.
 *
 * @param string $type  Post type.
 * @param int    $limit Max.
 * @return WP_Post[]
 */
function ceori_consented_posts( $type, $limit ) {
	return get_posts(
		array(
			'post_type'      => $type,
			'post_status'    => 'publish',
			'posts_per_page' => $limit,
			'orderby'        => array(
				'menu_order' => 'ASC',
				'date'       => 'DESC',
			),
			'meta_query'     => array(
				array(
					'key'   => 'consent',
					'value' => '1',
				),
			),
			'no_found_rows'  => true,
		)
	);
}

/**
 * Testimonials block. Outputs nothing on the site when there are no approved testimonials,
 * so a section wrapping it with the "hide-when-empty" class collapses too.
 *
 * @param array $a Attributes.
 * @return string
 */
function ceori_render_testimonials( $a ) {
	$items = ceori_consented_posts( 'ceori_testimonial', max( 1, (int) $a['limit'] ) );
	if ( ! $items ) {
		return ceori_editor_notice( __( 'No approved testimonials yet. Add genuine patient reviews under Testimonials, tick the consent box and publish. This section stays hidden on the site until then.', 'ceori' ) );
	}

	// Skintiva layout: 2 × 2 grid of image + card pairs; more than four reviews become pages.
	$pages = array_chunk( $items, 4 );
	$html  = '<div ' . ceori_block_wrapper( 'ceori-testimonials' ) . ' data-carousel aria-roledescription="carousel" aria-label="' . esc_attr__( 'Patient testimonials', 'ceori' ) . '"><div class="ceori-testimonials__viewport"><div class="ceori-testimonials__track">';
	foreach ( $pages as $p => $page ) {
		$html .= '<div class="ceori-testimonials__page" role="group" aria-roledescription="slide" aria-label="' . esc_attr( sprintf( '%d / %d', $p + 1, count( $pages ) ) ) . '">';
		foreach ( $page as $item ) {
			$treatment = (string) get_post_meta( $item->ID, 'treatment', true );
			$rating    = (string) get_post_meta( $item->ID, 'rating', true );
			$source    = (string) get_post_meta( $item->ID, 'source', true );
			$html     .= '<figure class="ceori-testimonial' . ( has_post_thumbnail( $item ) ? '' : ' no-media' ) . '">';
			if ( has_post_thumbnail( $item ) ) {
				$html .= '<div class="ceori-testimonial__media">' . get_the_post_thumbnail( $item, 'ceori-portrait', array( 'loading' => 'lazy' ) ) . '</div>';
			}
			$html .= '<div class="ceori-testimonial__card">';
			if ( $treatment || $rating ) {
				$html .= '<div class="ceori-testimonial__meta">';
				$html .= '<span class="ceori-testimonial__col"><span class="ceori-testimonial__label">' . esc_html__( 'Treatment', 'ceori' ) . '</span><span class="ceori-testimonial__value">' . esc_html( $treatment ? $treatment : '—' ) . '</span></span>';
				if ( $rating ) {
					$html .= '<span class="ceori-testimonial__col is-right"><span class="ceori-testimonial__label">' . esc_html__( 'Rating', 'ceori' ) . '</span><span class="ceori-testimonial__value">' . esc_html( $rating ) . '</span></span>';
				}
				$html .= '</div>';
			}
			$html .= '<blockquote class="ceori-testimonial__quote">' . wp_kses_post( wpautop( $item->post_content ) ) . '</blockquote>';
			$html .= '<figcaption class="ceori-testimonial__author">' . esc_html( get_the_title( $item ) ) . ( $source ? ' <span>· ' . esc_html( $source ) . '</span>' : '' ) . '</figcaption>';
			$html .= '</div></figure>';
		}
		$html .= '</div>';
	}
	$html .= '</div></div>';
	if ( count( $pages ) > 1 ) {
		$html .= '<div class="ceori-testimonials__nav"><button type="button" class="ceori-round-btn" data-prev aria-label="' . esc_attr__( 'Previous testimonial', 'ceori' ) . '">' . ceori_icon( 'arrow-left' ) . '</button><span class="ceori-testimonials__count" aria-live="polite"></span><button type="button" class="ceori-round-btn" data-next aria-label="' . esc_attr__( 'Next testimonial', 'ceori' ) . '">' . ceori_icon( 'arrow-right' ) . '</button></div>';
	}
	return $html . '</div>';
}

/**
 * Results gallery block.
 *
 * @param array $a Attributes.
 * @return string
 */
function ceori_render_results_gallery( $a ) {
	$items = ceori_consented_posts( 'ceori_result', max( 1, (int) $a['limit'] ) );
	if ( ! $items ) {
		return ceori_editor_notice( __( 'No results to show yet. Add real CEORI patient photos under Results Gallery, confirm consent and publish. This section stays hidden on the site until then.', 'ceori' ) );
	}

	$cats  = array();
	$cards = '';
	foreach ( $items as $item ) {
		$terms = get_the_terms( $item, 'treatment_category' );
		$slugs = array();
		if ( $terms && ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$cats[ $term->slug ] = $term->name;
				$slugs[]             = $term->slug;
			}
		}
		$label  = (string) get_post_meta( $item->ID, 'treatment', true );
		$note   = (string) get_post_meta( $item->ID, 'note', true );
		$before = (int) get_post_meta( $item->ID, 'before_image', true );
		$after  = (int) get_post_thumbnail_id( $item );
		$full   = wp_get_attachment_image_url( $after, 'large' );
		$alt    = trim( $label . ' – ' . get_the_title( $item ), ' –' );

		$cards .= '<figure class="ceori-result reveal" data-cats="' . esc_attr( implode( ' ', $slugs ) ) . '">';
		if ( $before && $after ) {
			$cards .= '<div class="ceori-compare" data-compare style="--pos:50%">';
			$cards .= wp_get_attachment_image( $after, 'ceori-card', false, array( 'class' => 'ceori-compare__after', 'alt' => $alt . ' – ' . __( 'after', 'ceori' ), 'loading' => 'lazy' ) );
			$cards .= '<div class="ceori-compare__before">' . wp_get_attachment_image( $before, 'ceori-card', false, array( 'alt' => $alt . ' – ' . __( 'before', 'ceori' ), 'loading' => 'lazy' ) ) . '</div>';
			$cards .= '<span class="ceori-compare__tag ceori-compare__tag--before">' . esc_html__( 'Before', 'ceori' ) . '</span><span class="ceori-compare__tag ceori-compare__tag--after">' . esc_html__( 'After', 'ceori' ) . '</span>';
			$cards .= '<input class="ceori-compare__range" type="range" min="0" max="100" value="50" aria-label="' . esc_attr__( 'Drag to compare before and after', 'ceori' ) . '">';
			$cards .= '</div>';
		} else {
			$cards .= '<button type="button" class="ceori-result__open" data-lightbox="' . esc_url( (string) $full ) . '" data-caption="' . esc_attr( $alt ) . '" aria-label="' . esc_attr( sprintf( /* translators: %s: image label */ __( 'Enlarge image: %s', 'ceori' ), $alt ) ) . '">' . wp_get_attachment_image( $after, 'ceori-card', false, array( 'alt' => $alt, 'loading' => 'lazy' ) ) . '</button>';
		}
		$cards .= '<figcaption><strong>' . esc_html( $label ? $label : get_the_title( $item ) ) . '</strong>' . ( $note ? '<span>' . esc_html( $note ) . '</span>' : '' ) . '</figcaption></figure>';
	}

	$html = '<div ' . ceori_block_wrapper( 'ceori-results' ) . ' data-gallery>';
	if ( $a['filters'] && count( $cats ) > 1 ) {
		$html .= '<div class="ceori-filters" role="toolbar" aria-label="' . esc_attr__( 'Filter results by category', 'ceori' ) . '"><button type="button" class="is-active" aria-pressed="true" data-filter="*">' . esc_html__( 'All', 'ceori' ) . '</button>';
		foreach ( $cats as $slug => $name ) {
			$html .= '<button type="button" aria-pressed="false" data-filter="' . esc_attr( $slug ) . '">' . esc_html( $name ) . '</button>';
		}
		$html .= '</div>';
	}
	$html .= '<div class="ceori-results__grid">' . $cards . '</div>';
	$html .= '<p class="ceori-results__disclaimer">' . esc_html__( 'Photos are of CEORI patients and published with their consent. Results vary from person to person and depend on individual assessment.', 'ceori' ) . '</p>';
	return $html . '</div>';
}

/**
 * Appointment form block.
 *
 * @param array $a Attributes.
 * @return string
 */
function ceori_render_appointment_form( $a ) {
	static $instance = 0;
	++$instance;
	$id       = 'ceori-form-' . $instance;
	$selected = isset( $_GET['treatment'] ) ? sanitize_text_field( wp_unslash( $_GET['treatment'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	$options  = array();
	foreach ( ceori_treatment_categories() as $term ) {
		$options[] = $term->name;
	}
	$options[] = __( 'Not sure – I would like advice', 'ceori' );
	if ( $selected && ! in_array( $selected, $options, true ) ) {
		array_unshift( $options, $selected );
	}
	$privacy = get_privacy_policy_url();

	ob_start();
	?>
	<div <?php echo ceori_block_wrapper( 'ceori-form-card' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
		<?php if ( $a['heading'] ) : ?>
			<h2 class="ceori-form-card__title"><?php echo esc_html( $a['heading'] ); ?></h2>
		<?php endif; ?>
		<?php if ( $a['note'] ) : ?>
			<p class="ceori-form-card__note"><?php echo esc_html( $a['note'] ); ?></p>
		<?php endif; ?>
		<form class="ceori-form" id="<?php echo esc_attr( $id ); ?>" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" novalidate data-ceori-form toolname="request_appointment" tooldescription="<?php echo esc_attr__( 'Request a consultation at CEORI Skin Clinic, Calicut. The clinic replies to confirm a time; nothing is booked until then.', 'ceori' ); ?>">
			<input type="hidden" name="action" value="ceori_enquiry">
			<?php wp_nonce_field( 'ceori_enquiry', 'ceori_nonce' ); ?>
			<input type="hidden" name="source_url" value="<?php echo esc_url( home_url( add_query_arg( array() ) ) ); ?>">
			<input type="hidden" name="started" value="<?php echo esc_attr( (string) time() ); ?>">
			<div class="ceori-hp" aria-hidden="true">
				<label for="<?php echo esc_attr( $id ); ?>-website"><?php esc_html_e( 'Leave this field empty', 'ceori' ); ?></label>
				<input type="text" id="<?php echo esc_attr( $id ); ?>-website" name="website" tabindex="-1" autocomplete="off">
			</div>
			<div class="ceori-form__grid">
				<p class="ceori-field">
					<label for="<?php echo esc_attr( $id ); ?>-name"><?php esc_html_e( 'Full name', 'ceori' ); ?> <span aria-hidden="true">*</span></label>
					<input type="text" id="<?php echo esc_attr( $id ); ?>-name" name="full_name" toolparamdescription="Patient full name" autocomplete="name" required maxlength="80" aria-describedby="<?php echo esc_attr( $id ); ?>-name-err">
					<span class="ceori-field__error" id="<?php echo esc_attr( $id ); ?>-name-err"></span>
				</p>
				<p class="ceori-field">
					<label for="<?php echo esc_attr( $id ); ?>-phone"><?php esc_html_e( 'Phone number', 'ceori' ); ?> <span aria-hidden="true">*</span></label>
					<input type="tel" id="<?php echo esc_attr( $id ); ?>-phone" name="phone" toolparamdescription="Indian mobile or landline number the clinic can call back" autocomplete="tel" inputmode="tel" required placeholder="+91 98765 43210" aria-describedby="<?php echo esc_attr( $id ); ?>-phone-err">
					<span class="ceori-field__error" id="<?php echo esc_attr( $id ); ?>-phone-err"></span>
				</p>
				<p class="ceori-field">
					<label for="<?php echo esc_attr( $id ); ?>-email"><?php esc_html_e( 'Email address', 'ceori' ); ?> <span class="ceori-field__optional"><?php esc_html_e( '(optional)', 'ceori' ); ?></span></label>
					<input type="email" id="<?php echo esc_attr( $id ); ?>-email" name="email" toolparamdescription="Optional email address for the reply" autocomplete="email" aria-describedby="<?php echo esc_attr( $id ); ?>-email-err">
					<span class="ceori-field__error" id="<?php echo esc_attr( $id ); ?>-email-err"></span>
				</p>
				<p class="ceori-field">
					<label for="<?php echo esc_attr( $id ); ?>-treatment"><?php esc_html_e( 'Treatment category', 'ceori' ); ?> <span aria-hidden="true">*</span></label>
					<select id="<?php echo esc_attr( $id ); ?>-treatment" name="treatment" toolparamdescription="Speciality or treatment the consultation is about" required aria-describedby="<?php echo esc_attr( $id ); ?>-treatment-err">
						<option value=""><?php esc_html_e( 'Please choose', 'ceori' ); ?></option>
						<?php foreach ( $options as $option ) : ?>
							<option value="<?php echo esc_attr( $option ); ?>" <?php selected( $selected, $option ); ?>><?php echo esc_html( $option ); ?></option>
						<?php endforeach; ?>
					</select>
					<span class="ceori-field__error" id="<?php echo esc_attr( $id ); ?>-treatment-err"></span>
				</p>
				<?php
				$doctors        = get_posts(
					array(
						'post_type'      => 'ceori_doctor',
						'post_status'    => 'publish',
						'posts_per_page' => 50,
						'orderby'        => array(
							'menu_order' => 'ASC',
							'title'      => 'ASC',
						),
					)
				);
				$doctor_choice  = isset( $_GET['doctor'] ) ? sanitize_text_field( wp_unslash( $_GET['doctor'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
				if ( $doctors ) :
					?>
				<p class="ceori-field">
					<label for="<?php echo esc_attr( $id ); ?>-doctor"><?php esc_html_e( 'Preferred specialist', 'ceori' ); ?> <span class="ceori-field__optional"><?php esc_html_e( '(optional)', 'ceori' ); ?></span></label>
					<select id="<?php echo esc_attr( $id ); ?>-doctor" name="doctor" toolparamdescription="Optional preferred specialist">
						<option value=""><?php esc_html_e( 'No preference', 'ceori' ); ?></option>
						<?php
						foreach ( $doctors as $doc ) :
							$doc_name  = get_the_title( $doc );
							$doc_role  = (string) get_post_meta( $doc->ID, 'designation', true );
							$doc_value = $doc_role ? $doc_name . ' – ' . $doc_role : $doc_name;
							?>
							<option value="<?php echo esc_attr( $doc_value ); ?>" <?php selected( $doctor_choice && ( $doctor_choice === $doc_name || $doctor_choice === $doc->post_name ), true ); ?>><?php echo esc_html( $doc_value ); ?></option>
						<?php endforeach; ?>
					</select>
				</p>
				<?php endif; ?>
				<p class="ceori-field">
					<label for="<?php echo esc_attr( $id ); ?>-date"><?php esc_html_e( 'Preferred consultation date', 'ceori' ); ?></label>
					<input type="date" id="<?php echo esc_attr( $id ); ?>-date" name="preferred_date" toolparamdescription="Optional preferred consultation date (YYYY-MM-DD, today or later)" min="<?php echo esc_attr( wp_date( 'Y-m-d' ) ); ?>" aria-describedby="<?php echo esc_attr( $id ); ?>-date-err">
					<span class="ceori-field__error" id="<?php echo esc_attr( $id ); ?>-date-err"></span>
				</p>
				<p class="ceori-field ceori-field--full">
					<label for="<?php echo esc_attr( $id ); ?>-message"><?php esc_html_e( 'Message', 'ceori' ); ?> <span class="ceori-field__optional"><?php esc_html_e( '(optional – please do not include detailed medical history)', 'ceori' ); ?></span></label>
					<textarea id="<?php echo esc_attr( $id ); ?>-message" name="message" toolparamdescription="Optional short message; no detailed medical history" rows="4" maxlength="1000"></textarea>
				</p>
				<p class="ceori-field ceori-field--full ceori-field--check">
					<input type="checkbox" id="<?php echo esc_attr( $id ); ?>-consent" name="consent" value="1" toolparamdescription="Must be checked: the patient agrees to be contacted about this request" required aria-describedby="<?php echo esc_attr( $id ); ?>-consent-err">
					<label for="<?php echo esc_attr( $id ); ?>-consent">
						<?php
						if ( $privacy ) {
							printf(
								/* translators: %s: privacy policy link */
								esc_html__( 'I agree that CEORI Skin Clinic may contact me about this request and store my details as described in the %s.', 'ceori' ),
								'<a href="' . esc_url( $privacy ) . '" target="_blank" rel="noopener">' . esc_html__( 'Privacy Policy', 'ceori' ) . '</a>'
							);
						} else {
							esc_html_e( 'I agree that CEORI Skin Clinic may contact me about this request and store my details for that purpose.', 'ceori' );
						}
						?>
						<span aria-hidden="true">*</span>
					</label>
					<span class="ceori-field__error" id="<?php echo esc_attr( $id ); ?>-consent-err"></span>
				</p>
			</div>
			<div class="ceori-form__footer">
				<?php
				$submit_labels = array(
					'email'          => __( 'Send request', 'ceori' ),
					'whatsapp'       => __( 'Send on WhatsApp', 'ceori' ),
					'email_whatsapp' => __( 'Send request', 'ceori' ),
					'mailto'         => __( 'Send by email', 'ceori' ),
				);
				?>
				<button type="submit" class="ceori-btn ceori-btn--<?php echo esc_attr( ceori_booking_method() ); ?>"><?php echo esc_html( $submit_labels[ ceori_booking_method() ] ); ?><?php echo ceori_icon( 'arrow-up-right' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
				<p class="ceori-form__alt">
					<?php esc_html_e( 'Prefer to talk?', 'ceori' ); ?>
					<a href="<?php echo esc_attr( ceori_tel_href( ceori_option( 'phone_primary' ) ) ); ?>"><?php echo esc_html( ceori_option( 'phone_primary' ) ); ?></a>
				</p>
			</div>
			<?php
			$state = isset( $_GET['enquiry'] ) ? sanitize_key( $_GET['enquiry'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
			?>
			<div class="ceori-form__status<?php echo $state ? ' is-' . ( 'sent' === $state ? 'success' : 'error' ) : ''; ?>" role="status" aria-live="polite" tabindex="-1">
				<?php
				if ( 'sent' === $state ) {
					esc_html_e( 'Thank you – we have received your request. Our team will call you to confirm a suitable appointment time. Your appointment is not confirmed until we contact you.', 'ceori' );
				} elseif ( 'error' === $state ) {
					esc_html_e( 'Sorry, we could not send your request. Please check the form or call us.', 'ceori' );
				}
				?>
			</div>
		</form>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * Social media list items (Instagram, Facebook, YouTube, WhatsApp when set).
 *
 * @return string
 */
function ceori_social_items() {
	$items = '';
	foreach ( array(
		'instagram' => 'Instagram',
		'facebook'  => 'Facebook',
		'youtube'   => 'YouTube',
	) as $key => $label ) {
		$url = ceori_option( $key );
		if ( $url ) {
			/* translators: %s: social network */
			$items .= '<li><a href="' . esc_url( $url ) . '" target="_blank" rel="noopener me" aria-label="' . esc_attr( sprintf( __( 'CEORI on %s (opens in a new tab)', 'ceori' ), $label ) ) . '">' . ceori_icon( $key ) . '</a></li>';
		}
	}
	$wa = ceori_whatsapp_href();
	if ( $wa ) {
		$items .= '<li><a href="' . esc_url( $wa ) . '" target="_blank" rel="noopener" aria-label="' . esc_attr__( 'WhatsApp (opens in a new tab)', 'ceori' ) . '">' . ceori_icon( 'whatsapp' ) . '</a></li>';
	}
	return $items;
}

/**
 * Clinic info block.
 *
 * @param array $a Attributes.
 * @return string
 */
function ceori_render_clinic_info( $a ) {
	$phones = array_filter( array( ceori_option( 'phone_primary' ), ceori_option( 'phone_secondary' ) ) );
	$email  = ceori_option( 'email' );

	switch ( $a['field'] ) {
		case 'phone':
			$links = array();
			foreach ( $phones as $phone ) {
				$links[] = '<a href="' . esc_attr( ceori_tel_href( $phone ) ) . '">' . esc_html( $phone ) . '</a>';
			}
			return '<p ' . ceori_block_wrapper( 'ceori-info ceori-info--phone' ) . '>' . ceori_icon( 'phone' ) . '<span>' . implode( ', ', $links ) . '</span></p>';

		case 'email':
			return '<p ' . ceori_block_wrapper( 'ceori-info ceori-info--email' ) . '>' . ceori_icon( 'mail' ) . '<a href="mailto:' . esc_attr( antispambot( $email ) ) . '">' . esc_html( antispambot( $email ) ) . '</a></p>';

		case 'address':
			return '<p ' . ceori_block_wrapper( 'ceori-info ceori-info--address' ) . '>' . ceori_icon( 'pin' ) . '<a href="' . esc_url( ceori_map_href() ) . '" target="_blank" rel="noopener">' . esc_html( ceori_address_line() ) . '<span class="screen-reader-text"> ' . esc_html__( '(opens Google Maps in a new tab)', 'ceori' ) . '</span></a></p>';

		case 'hours':
			$rows = '';
			foreach ( array_filter( array_map( 'trim', explode( "\n", (string) ceori_option( 'hours' ) ) ) ) as $row ) {
				$parts = array_map( 'trim', explode( ':', $row, 2 ) );
				$rows .= '<li><span>' . esc_html( $parts[0] ) . '</span>' . ( isset( $parts[1] ) ? '<span>' . esc_html( $parts[1] ) . '</span>' : '' ) . '</li>';
			}
			return '<ul ' . ceori_block_wrapper( 'ceori-hours' ) . '>' . $rows . '</ul>';

		case 'social':
			return '<ul ' . ceori_block_wrapper( 'ceori-social' ) . '>' . ceori_social_items() . '</ul>';

		case 'map':
			$src = 'https://www.google.com/maps?q=' . rawurlencode( ceori_option( 'map_query' ) ) . '&output=embed';
			return '<div ' . ceori_block_wrapper( 'ceori-map' ) . '><iframe title="' . esc_attr__( 'Map showing CEORI Skin Clinic, Eranhipalam, Kozhikode', 'ceori' ) . '" src="' . esc_url( $src ) . '" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe></div>';

		case 'about':
			return '<p ' . ceori_block_wrapper( 'ceori-info-about' ) . '>' . esc_html( ceori_option( 'footer_about' ) ) . '</p>';

		case 'call':
			$phone = reset( $phones );
			/* translators: %s: phone number */
			return '<div ' . ceori_block_wrapper( 'ceori-call' ) . '><a class="ceori-btn ceori-btn--outline" href="' . esc_attr( ceori_tel_href( $phone ) ) . '" aria-label="' . esc_attr( sprintf( __( 'Call us – CEORI Skin Clinic, %s', 'ceori' ), $phone ) ) . '">' . ceori_icon( 'phone' ) . esc_html__( 'Call us', 'ceori' ) . '</a></div>';

		case 'copyright':
			/* translators: 1: year, 2: clinic name */
			return '<p ' . ceori_block_wrapper( 'ceori-copyright' ) . '>' . esc_html( sprintf( __( '© %1$s %2$s. All rights reserved.', 'ceori' ), wp_date( 'Y' ), ceori_option( 'clinic_name' ) ) ) . '</p>';

		default:
			$html = '<ul ' . ceori_block_wrapper( 'ceori-contact-list' ) . '>';
			foreach ( $phones as $phone ) {
				$html .= '<li>' . ceori_icon( 'phone' ) . '<a href="' . esc_attr( ceori_tel_href( $phone ) ) . '">' . esc_html( $phone ) . '</a></li>';
			}
			if ( $email ) {
				$html .= '<li>' . ceori_icon( 'mail' ) . '<a href="mailto:' . esc_attr( antispambot( $email ) ) . '">' . esc_html( antispambot( $email ) ) . '</a></li>';
			}
			$html .= '<li>' . ceori_icon( 'pin' ) . '<a href="' . esc_url( ceori_map_href() ) . '" target="_blank" rel="noopener">' . esc_html( ceori_address_line() ) . '</a></li>';
			return $html . '</ul>';
	}
}

/**
 * Side panel (off-canvas) block: hamburger button + dialog panel.
 */
function ceori_render_offcanvas() {
	$links = '';
	foreach ( ceori_treatment_categories() as $term ) {
		$links .= '<li><a href="' . esc_url( ceori_category_url( $term ) ) . '">' . esc_html( $term->name ) . '</a></li>';
	}
	$links .= '<li><a href="' . esc_url( home_url( '/about-us/' ) ) . '">' . esc_html__( 'About Us', 'ceori' ) . '</a></li>';
	$links .= '<li><a href="' . esc_url( home_url( '/blog/' ) ) . '">' . esc_html__( 'Blog', 'ceori' ) . '</a></li>';

	$logo   = get_custom_logo();
	$phones = '';
	foreach ( array_filter( array( ceori_option( 'phone_primary' ), ceori_option( 'phone_secondary' ) ) ) as $phone ) {
		$phones .= '<a href="' . esc_attr( ceori_tel_href( $phone ) ) . '">' . esc_html( $phone ) . '</a>';
	}

	ob_start();
	?>
	<div <?php echo ceori_block_wrapper( 'ceori-offcanvas' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
		<button type="button" class="ceori-offcanvas__toggle" aria-controls="ceori-panel" aria-expanded="false" data-offcanvas-open>
			<span class="screen-reader-text"><?php esc_html_e( 'Open clinic information panel', 'ceori' ); ?></span>
			<span class="ceori-burger" aria-hidden="true"><span></span><span></span><span></span></span>
		</button>
		<div class="ceori-offcanvas__overlay" data-offcanvas-close hidden></div>
		<aside class="ceori-offcanvas__panel" id="ceori-panel" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Clinic information', 'ceori' ); ?>" hidden>
			<button type="button" class="ceori-offcanvas__close" data-offcanvas-close><span class="screen-reader-text"><?php esc_html_e( 'Close panel', 'ceori' ); ?></span><?php echo ceori_icon( 'close' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
			<div class="ceori-offcanvas__logo"><?php echo $logo ? $logo : '<a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html( ceori_option( 'clinic_name' ) ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
			<p class="ceori-offcanvas__about"><?php echo esc_html( ceori_option( 'footer_about' ) ); ?></p>
			<?php
			// Main menu (shown in the panel on tablets and phones, where the header menu is hidden).
			$nav_id = (int) get_option( 'ceori_main_navigation' );
			if ( $nav_id && ! ceori_is_editor_preview() ) {
				echo '<div class="ceori-offcanvas__menu">' . do_blocks( '<!-- wp:navigation {"ref":' . $nav_id . ',"overlayMenu":"never","openSubmenusOnClick":true,"layout":{"type":"flex","orientation":"vertical"}} /-->' ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput
			}
			?>
			<div class="ceori-offcanvas__group ceori-offcanvas__group--explore">
				<h2 class="ceori-offcanvas__title"><?php esc_html_e( 'Explore more', 'ceori' ); ?></h2>
				<ul class="ceori-offcanvas__links"><?php echo $links; // phpcs:ignore WordPress.Security.EscapeOutput ?></ul>
			</div>
			<div class="ceori-offcanvas__group">
				<h2 class="ceori-offcanvas__title"><?php esc_html_e( 'Location', 'ceori' ); ?></h2>
				<p><a href="<?php echo esc_url( ceori_map_href() ); ?>" target="_blank" rel="noopener"><?php echo esc_html( ceori_address_line() ); ?></a></p>
			</div>
			<div class="ceori-offcanvas__group">
				<h2 class="ceori-offcanvas__title"><?php esc_html_e( 'Contact', 'ceori' ); ?></h2>
				<p class="ceori-offcanvas__contact"><?php echo $phones; // phpcs:ignore WordPress.Security.EscapeOutput ?><a href="mailto:<?php echo esc_attr( antispambot( ceori_option( 'email' ) ) ); ?>"><?php echo esc_html( antispambot( ceori_option( 'email' ) ) ); ?></a></p>
			</div>
			<div class="ceori-offcanvas__group">
				<h2 class="ceori-offcanvas__title"><?php esc_html_e( 'Social media', 'ceori' ); ?></h2>
				<ul class="ceori-social"><?php echo ceori_social_items(); // phpcs:ignore WordPress.Security.EscapeOutput ?></ul>
			</div>
			<a class="ceori-btn ceori-offcanvas__cta" href="<?php echo esc_url( ceori_appointment_url() ); ?>"><?php esc_html_e( 'Book an Appointment', 'ceori' ); ?><?php echo ceori_icon( 'arrow-up-right' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
		</aside>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * Related articles block.
 */
function ceori_render_related_posts() {
	$current = ceori_is_editor_preview() ? 0 : get_the_ID();
	$cats    = $current ? wp_get_post_categories( $current ) : array();
	$args    = array(
		'post_type'           => 'post',
		'posts_per_page'      => 3,
		'post__not_in'        => $current ? array( $current ) : array(),
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	);
	if ( $cats ) {
		$args['category__in'] = $cats;
	}
	$posts = get_posts( $args );
	if ( ! $posts ) {
		return ceori_editor_notice( __( 'Related articles appear here once more posts are published.', 'ceori' ) );
	}
	$html = '<div ' . ceori_block_wrapper( 'ceori-posts ceori-posts--related' ) . '><ul class="ceori-posts__grid ceori-grid ceori-grid--3">';
	foreach ( $posts as $post ) {
		$url   = get_permalink( $post );
		$html .= '<li class="ceori-post-card reveal">';
		$html .= '<a class="ceori-post-card__media" href="' . esc_url( $url ) . '" tabindex="-1" aria-hidden="true">' . ceori_card_image( $post->ID ) . '</a>';
		$html .= '<time class="ceori-post-card__date" datetime="' . esc_attr( get_the_date( 'c', $post ) ) . '">' . esc_html( get_the_date( '', $post ) ) . '</time>';
		$html .= '<h3 class="ceori-post-card__title"><a href="' . esc_url( $url ) . '">' . esc_html( get_the_title( $post ) ) . '</a></h3>';
		$html .= '<a class="ceori-link-arrow" href="' . esc_url( $url ) . '">' . esc_html__( 'Read more', 'ceori' ) . '<span class="screen-reader-text">: ' . esc_html( get_the_title( $post ) ) . '</span>' . ceori_icon( 'arrow-up-right' ) . '</a>';
		$html .= '</li>';
	}
	return $html . '</ul></div>';
}

/*
 * Sections marked "ceori-hide-empty" (results, testimonials) disappear entirely on the
 * front end when their dynamic block has nothing approved to show.
 */
add_filter(
	'render_block_core/group',
	function ( $html, $block ) {
		$class = isset( $block['attrs']['className'] ) ? $block['attrs']['className'] : '';
		if ( false === strpos( $class, 'ceori-hide-empty' ) || ceori_is_editor_preview() ) {
			return $html;
		}
		return ( false === strpos( $html, 'ceori-results__grid' ) && false === strpos( $html, 'ceori-testimonials__track' ) ) ? '' : $html;
	},
	10,
	2
);

// The blog banner has a static H1 for the blog home and a query title for archives; show one.
add_filter(
	'render_block_core/heading',
	function ( $html, $block ) {
		$class = isset( $block['attrs']['className'] ) ? $block['attrs']['className'] : '';
		return ( false !== strpos( $class, 'ceori-blog-title' ) && ( is_archive() || is_search() ) ) ? '' : $html;
	},
	10,
	2
);

/**
 * Share buttons block.
 */
function ceori_render_share() {
	$url   = ceori_is_editor_preview() ? home_url( '/' ) : get_permalink();
	$title = ceori_is_editor_preview() ? get_bloginfo( 'name' ) : get_the_title();
	$links = array(
		'whatsapp' => array( 'WhatsApp', 'https://wa.me/?text=' . rawurlencode( $title . ' ' . $url ) ),
		'facebook' => array( 'Facebook', 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode( $url ) ),
		'x'        => array( 'X', 'https://twitter.com/intent/tweet?url=' . rawurlencode( $url ) . '&text=' . rawurlencode( $title ) ),
	);
	$html = '<div ' . ceori_block_wrapper( 'ceori-share' ) . '><span class="ceori-share__label">' . esc_html__( 'Share', 'ceori' ) . '</span><ul>';
	foreach ( $links as $icon => $link ) {
		/* translators: %s: network */
		$html .= '<li><a href="' . esc_url( $link[1] ) . '" target="_blank" rel="noopener" aria-label="' . esc_attr( sprintf( __( 'Share on %s (opens in a new tab)', 'ceori' ), $link[0] ) ) . '">' . ceori_icon( $icon ) . '</a></li>';
	}
	$html .= '<li><button type="button" data-copy="' . esc_url( $url ) . '" aria-label="' . esc_attr__( 'Copy link to this article', 'ceori' ) . '">' . ceori_icon( 'link' ) . '</button></li>';
	return $html . '</ul></div>';
}
