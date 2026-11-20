<?php
/**
 * Content model.
 *
 * - Treatments stay ordinary Pages so every existing URL (e.g. /hydra-facial-in-calicut/)
 *   is preserved; the "Treatment category" taxonomy groups them.
 * - Specialists, testimonials, results and enquiries are private post types managed
 *   from the dashboard and displayed through the theme's dynamic blocks.
 *
 * @package ceori
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', 'ceori_register_content_types' );

/**
 * Register taxonomies, post types and meta.
 */
function ceori_register_content_types() {
	register_taxonomy(
		'treatment_category',
		array( 'page', 'ceori_result', 'ceori_doctor' ),
		array(
			'labels'             => array(
				'name'          => __( 'Treatment categories', 'ceori' ),
				'singular_name' => __( 'Treatment category', 'ceori' ),
				'menu_name'     => __( 'Treatment categories', 'ceori' ),
				'all_items'     => __( 'All categories', 'ceori' ),
				'edit_item'     => __( 'Edit category', 'ceori' ),
				'add_new_item'  => __( 'Add new category', 'ceori' ),
			),
			'hierarchical'       => true,
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_rest'       => true,
			'show_admin_column'  => true,
			'rewrite'            => false,
		)
	);

	foreach ( array( 'ceori_order' => 'integer', 'ceori_landing_page' => 'integer', 'ceori_image' => 'integer', 'ceori_summary' => 'string' ) as $key => $type ) {
		register_term_meta(
			'treatment_category',
			$key,
			array(
				'type'         => $type,
				'single'       => true,
				'show_in_rest' => true,
			)
		);
	}

	$common = array(
		'public'              => false,
		'publicly_queryable'  => false,
		'exclude_from_search' => true,
		'show_ui'             => true,
		'show_in_rest'        => true,
		'has_archive'         => false,
		'rewrite'             => false,
	);

	register_post_type(
		'ceori_doctor',
		array_merge(
			$common,
			array(
				'labels'        => ceori_cpt_labels( __( 'Specialist', 'ceori' ), __( 'Specialists', 'ceori' ) ),
				'menu_icon'     => 'dashicons-groups',
				'menu_position' => 21,
				'supports'      => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
			)
		)
	);

	register_post_type(
		'ceori_testimonial',
		array_merge(
			$common,
			array(
				'labels'        => ceori_cpt_labels( __( 'Testimonial', 'ceori' ), __( 'Testimonials', 'ceori' ) ),
				'menu_icon'     => 'dashicons-format-quote',
				'menu_position' => 22,
				'supports'      => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
			)
		)
	);

	register_post_type(
		'ceori_result',
		array_merge(
			$common,
			array(
				'labels'        => ceori_cpt_labels( __( 'Result', 'ceori' ), __( 'Results Gallery', 'ceori' ) ),
				'menu_icon'     => 'dashicons-format-gallery',
				'menu_position' => 23,
				'supports'      => array( 'title', 'thumbnail', 'page-attributes' ),
			)
		)
	);

	register_post_type(
		'ceori_equipment',
		array_merge(
			$common,
			array(
				'labels'        => ceori_cpt_labels( __( 'Equipment', 'ceori' ), __( 'Equipment', 'ceori' ) ),
				'menu_icon'     => 'dashicons-admin-tools',
				'menu_position' => 24,
				'supports'      => array( 'title', 'thumbnail', 'page-attributes' ),
			)
		)
	);

	register_post_type(
		'ceori_enquiry',
		array_merge(
			$common,
			array(
				'labels'        => ceori_cpt_labels( __( 'Enquiry', 'ceori' ), __( 'Enquiries', 'ceori' ) ),
				'menu_icon'     => 'dashicons-email-alt',
				'menu_position' => 4,
				'show_in_rest'  => false,
				'supports'      => array( 'title' ),
				'capabilities'  => array( 'create_posts' => 'do_not_allow' ),
				'map_meta_cap'  => true,
			)
		)
	);

	foreach ( ceori_meta_fields() as $post_type => $fields ) {
		foreach ( $fields as $key => $field ) {
			register_post_meta(
				$post_type,
				$key,
				array(
					'type'              => 'checkbox' === $field['type'] ? 'boolean' : ( 'image' === $field['type'] || 'page' === $field['type'] ? 'integer' : 'string' ),
					'single'            => true,
					'show_in_rest'      => 'ceori_enquiry' !== $post_type,
					'auth_callback'     => function () {
						return current_user_can( 'edit_posts' );
					},
					'sanitize_callback' => 'textarea' === $field['type'] ? 'sanitize_textarea_field' : null,
				)
			);
		}
	}

	// Manually chosen related treatments for a treatment page (comma-separated page IDs).
	register_post_meta(
		'page',
		'ceori_related',
		array(
			'type'          => 'string',
			'single'        => true,
			'show_in_rest'  => true,
			'auth_callback' => function () {
				return current_user_can( 'edit_pages' );
			},
		)
	);
}

/**
 * The Specialists entry whose "Profile page" is this page.
 *
 * @param int $page_id Page ID.
 * @return int Specialist post ID, or 0.
 */
function ceori_doctor_for_page( $page_id ) {
	if ( ! $page_id ) {
		return 0;
	}
	$ids = get_posts(
		array(
			'post_type'      => 'ceori_doctor',
			'meta_key'       => 'profile_page', // phpcs:ignore WordPress.DB.SlowDBQuery
			'meta_value'     => (int) $page_id, // phpcs:ignore WordPress.DB.SlowDBQuery
			'posts_per_page' => 1,
			'fields'         => 'ids',
		)
	);
	return $ids ? (int) $ids[0] : 0;
}

/**
 * Generate post type labels.
 *
 * @param string $singular Singular.
 * @param string $plural   Plural.
 * @return array
 */
function ceori_cpt_labels( $singular, $plural ) {
	return array(
		'name'               => $plural,
		'singular_name'      => $singular,
		'menu_name'          => $plural,
		/* translators: %s: item name */
		'add_new_item'       => sprintf( __( 'Add %s', 'ceori' ), $singular ),
		/* translators: %s: item name */
		'edit_item'          => sprintf( __( 'Edit %s', 'ceori' ), $singular ),
		/* translators: %s: item name */
		'new_item'           => sprintf( __( 'New %s', 'ceori' ), $singular ),
		/* translators: %s: item name */
		'search_items'       => sprintf( __( 'Search %s', 'ceori' ), $plural ),
		/* translators: %s: item name */
		'not_found'          => sprintf( __( 'No %s found', 'ceori' ), strtolower( $plural ) ),
		'all_items'          => $plural,
		'featured_image'     => __( 'Photo', 'ceori' ),
		'set_featured_image' => __( 'Set photo', 'ceori' ),
	);
}

/**
 * Meta fields per post type, used for registration and the edit-screen meta boxes.
 */
function ceori_meta_fields() {
	return array(
		'ceori_doctor'      => array(
			'designation'    => array(
				'label' => __( 'Designation (e.g. Consultant Dermatologist)', 'ceori' ),
				'type'  => 'text',
			),
			'full_name'      => array(
				'label' => __( 'Full name as it should appear on the profile (optional – the title is used if empty)', 'ceori' ),
				'type'  => 'text',
			),
			'specialisation' => array(
				'label' => __( 'Specialised in (short line shown on cards, e.g. Clinical & cosmetic dermatology)', 'ceori' ),
				'type'  => 'text',
			),
			'qualifications' => array(
				'label' => __( 'Qualifications – enter only verified degrees (e.g. MBBS, MD)', 'ceori' ),
				'type'  => 'text',
			),
			'profile_page'   => array(
				'label' => __( 'Profile page (optional)', 'ceori' ),
				'type'  => 'page',
			),
			'external_url'   => array(
				'label' => __( 'Booking link, e.g. this doctor’s Malabar Hospitals appointment page (optional – the profile page’s “Book an Appointment” button and the card’s “Book appointment” link open it; empty = clinic appointment form)', 'ceori' ),
				'type'  => 'url',
			),
			'external_label' => array(
				'label' => __( 'Booking button text on the profile page (optional – default “Book an Appointment”)', 'ceori' ),
				'type'  => 'text',
			),
		),
		'ceori_equipment'   => array(
			'used_for'       => array(
				'label' => __( 'Used for (short line, e.g. Laser hair reduction)', 'ceori' ),
				'type'  => 'text',
			),
			'summary'        => array(
				'label' => __( 'Description (one or two sentences)', 'ceori' ),
				'type'  => 'textarea',
			),
			'treatment_page' => array(
				'label' => __( 'Treatment page to link to (optional)', 'ceori' ),
				'type'  => 'page',
			),
		),
		'ceori_testimonial' => array(
			'treatment' => array(
				'label' => __( 'Treatment received (optional)', 'ceori' ),
				'type'  => 'text',
			),
			'rating'    => array(
				'label' => __( 'Rating (optional – only as given in the original review, e.g. 5/5)', 'ceori' ),
				'type'  => 'text',
			),
			'source'    => array(
				'label' => __( 'Source (e.g. Google review, written feedback form)', 'ceori' ),
				'type'  => 'text',
			),
			'consent'   => array(
				'label' => __( 'This is a genuine patient review and the patient agreed to publication (required to display it)', 'ceori' ),
				'type'  => 'checkbox',
			),
		),
		'ceori_result'      => array(
			'treatment'    => array(
				'label' => __( 'Treatment label shown on the image', 'ceori' ),
				'type'  => 'text',
			),
			'before_image' => array(
				'label' => __( '"Before" image (optional – when set, the Photo is used as "After" and a comparison slider is shown)', 'ceori' ),
				'type'  => 'image',
			),
			'note'         => array(
				'label' => __( 'Short caption (e.g. "Result after 3 sessions. Individual results vary.")', 'ceori' ),
				'type'  => 'text',
			),
			'consent'      => array(
				'label' => __( 'These are real CEORI patient photographs and documented consent for publication is on file (required to display)', 'ceori' ),
				'type'  => 'checkbox',
			),
		),
		'ceori_enquiry'     => array(
			'phone'          => array(
				'label' => __( 'Phone', 'ceori' ),
				'type'  => 'readonly',
			),
			'email'          => array(
				'label' => __( 'Email', 'ceori' ),
				'type'  => 'readonly',
			),
			'treatment'      => array(
				'label' => __( 'Treatment category', 'ceori' ),
				'type'  => 'readonly',
			),
			'doctor'         => array(
				'label' => __( 'Preferred specialist', 'ceori' ),
				'type'  => 'readonly',
			),
			'booking_method' => array(
				'label' => __( 'Sent by', 'ceori' ),
				'type'  => 'readonly',
			),
			'preferred_date' => array(
				'label' => __( 'Preferred date', 'ceori' ),
				'type'  => 'readonly',
			),
			'message'        => array(
				'label'      => __( 'Message', 'ceori' ),
				'type'       => 'readonly',
				'admin_type' => 'textarea',
			),
			'source_url'     => array(
				'label' => __( 'Submitted from', 'ceori' ),
				'type'  => 'readonly',
			),
			'status'         => array(
				'label'   => __( 'Follow-up status', 'ceori' ),
				'type'    => 'select',
				'options' => array(
					'new'       => __( 'New', 'ceori' ),
					'contacted' => __( 'Contacted', 'ceori' ),
					'booked'    => __( 'Appointment confirmed', 'ceori' ),
					'closed'    => __( 'Closed', 'ceori' ),
				),
			),
			'notes'          => array(
				'label' => __( 'Internal notes', 'ceori' ),
				'type'  => 'textarea',
			),
		),
	);
}

add_action(
	'add_meta_boxes',
	function () {
		foreach ( array_keys( ceori_meta_fields() ) as $post_type ) {
			add_meta_box( 'ceori_details', __( 'Details', 'ceori' ), 'ceori_render_meta_box', $post_type, 'normal', 'high' );
		}
	}
);

/**
 * Field type as shown to the current user: details sent through the website form (read-only for
 * the team) stay editable for administrators, who have full control over every record.
 *
 * @param array $field Field definition.
 * @return string
 */
function ceori_meta_field_type( $field ) {
	if ( 'readonly' === $field['type'] && current_user_can( 'manage_options' ) ) {
		return $field['admin_type'] ?? 'text';
	}
	return $field['type'];
}

/**
 * Render the details meta box.
 *
 * @param WP_Post $post Post.
 */
function ceori_render_meta_box( $post ) {
	$fields = ceori_meta_fields()[ $post->post_type ];
	wp_nonce_field( 'ceori_meta', 'ceori_meta_nonce' );
	echo '<table class="form-table" role="presentation">';
	foreach ( $fields as $key => $field ) {
		$value = get_post_meta( $post->ID, $key, true );
		$id    = 'ceori_meta_' . $key;
		$type  = ceori_meta_field_type( $field );
		echo '<tr><th scope="row"><label for="' . esc_attr( $id ) . '">' . esc_html( $field['label'] ) . '</label></th><td>';
		switch ( $type ) {
			case 'readonly':
				echo '<div id="' . esc_attr( $id ) . '">' . nl2br( esc_html( (string) $value ) ) . '</div>';
				break;
			case 'textarea':
				echo '<textarea class="large-text" rows="4" id="' . esc_attr( $id ) . '" name="ceori_meta[' . esc_attr( $key ) . ']">' . esc_textarea( (string) $value ) . '</textarea>';
				break;
			case 'checkbox':
				echo '<input type="checkbox" id="' . esc_attr( $id ) . '" name="ceori_meta[' . esc_attr( $key ) . ']" value="1" ' . checked( (bool) $value, true, false ) . '>';
				break;
			case 'select':
				echo '<select id="' . esc_attr( $id ) . '" name="ceori_meta[' . esc_attr( $key ) . ']">';
				foreach ( $field['options'] as $opt => $label ) {
					echo '<option value="' . esc_attr( $opt ) . '" ' . selected( $value ? $value : 'new', $opt, false ) . '>' . esc_html( $label ) . '</option>';
				}
				echo '</select>';
				break;
			case 'page':
				wp_dropdown_pages(
					array(
						'id'                => esc_attr( $id ),
						'name'              => 'ceori_meta[' . esc_attr( $key ) . ']',
						'selected'          => (int) $value,
						'show_option_none'  => esc_html__( '— None —', 'ceori' ),
						'option_none_value' => 0,
					)
				);
				break;
			case 'image':
				$src = $value ? wp_get_attachment_image_url( (int) $value, 'thumbnail' ) : '';
				echo '<div class="ceori-image-field"><img src="' . esc_url( (string) $src ) . '" alt="" style="max-width:120px;border-radius:8px;display:' . ( $src ? 'block' : 'none' ) . '">';
				echo '<input type="hidden" id="' . esc_attr( $id ) . '" name="ceori_meta[' . esc_attr( $key ) . ']" value="' . esc_attr( (string) $value ) . '">';
				echo '<p><button type="button" class="button ceori-image-pick">' . esc_html__( 'Choose image', 'ceori' ) . '</button> <button type="button" class="button-link ceori-image-clear">' . esc_html__( 'Remove', 'ceori' ) . '</button></p></div>';
				break;
			case 'url':
				echo '<input class="large-text" type="url" placeholder="https://" id="' . esc_attr( $id ) . '" name="ceori_meta[' . esc_attr( $key ) . ']" value="' . esc_attr( (string) $value ) . '">';
				break;
			default:
				echo '<input class="large-text" type="text" id="' . esc_attr( $id ) . '" name="ceori_meta[' . esc_attr( $key ) . ']" value="' . esc_attr( (string) $value ) . '">';
		}
		echo '</td></tr>';
	}
	echo '</table>';
}

add_action(
	'save_post',
	function ( $post_id, $post ) {
		if ( ! isset( $_POST['ceori_meta_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['ceori_meta_nonce'] ), 'ceori_meta' ) ) {
			return;
		}
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		$fields = ceori_meta_fields();
		if ( ! isset( $fields[ $post->post_type ] ) ) {
			return;
		}
		$input = isset( $_POST['ceori_meta'] ) ? wp_unslash( (array) $_POST['ceori_meta'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		foreach ( $fields[ $post->post_type ] as $key => $field ) {
			$type = ceori_meta_field_type( $field );
			if ( 'readonly' === $type || ( 'readonly' === $field['type'] && ! isset( $input[ $key ] ) ) ) {
				continue;
			}
			$raw = isset( $input[ $key ] ) ? $input[ $key ] : '';
			switch ( $type ) {
				case 'checkbox':
					update_post_meta( $post_id, $key, $raw ? 1 : 0 );
					break;
				case 'image':
				case 'page':
					update_post_meta( $post_id, $key, absint( $raw ) );
					break;
				case 'url':
					update_post_meta( $post_id, $key, esc_url_raw( trim( $raw ), array( 'http', 'https' ) ) );
					break;
				case 'textarea':
					update_post_meta( $post_id, $key, sanitize_textarea_field( $raw ) );
					break;
				case 'select':
					update_post_meta( $post_id, $key, array_key_exists( $raw, $field['options'] ) ? $raw : 'new' );
					break;
				default:
					update_post_meta( $post_id, $key, sanitize_text_field( $raw ) );
			}
		}
	},
	10,
	2
);

// Media picker for image meta fields.
add_action(
	'admin_enqueue_scripts',
	function ( $hook ) {
		if ( ! in_array( $hook, array( 'post.php', 'post-new.php', 'term.php', 'edit-tags.php' ), true ) ) {
			return;
		}
		wp_enqueue_media();
		wp_add_inline_script(
			'media-editor',
			"jQuery(function($){\$(document).on('click','.ceori-image-pick',function(e){e.preventDefault();var w=\$(this).closest('.ceori-image-field');var f=wp.media({title:'Choose image',multiple:false,library:{type:'image'}});f.on('select',function(){var a=f.state().get('selection').first().toJSON();w.find('input').val(a.id);w.find('img').attr('src',(a.sizes&&a.sizes.thumbnail?a.sizes.thumbnail.url:a.url)).show();});f.open();});\$(document).on('click','.ceori-image-clear',function(e){e.preventDefault();var w=\$(this).closest('.ceori-image-field');w.find('input').val('');w.find('img').hide();});});"
		);
	}
);

/*
 * Treatment category term fields: order, landing page, card image and summary.
 */
add_action( 'treatment_category_edit_form_fields', 'ceori_term_fields' );

/**
 * Term edit fields.
 *
 * @param WP_Term $term Term.
 */
function ceori_term_fields( $term ) {
	wp_nonce_field( 'ceori_term', 'ceori_term_nonce' );
	$order   = (int) get_term_meta( $term->term_id, 'ceori_order', true );
	$page    = (int) get_term_meta( $term->term_id, 'ceori_landing_page', true );
	$image   = (int) get_term_meta( $term->term_id, 'ceori_image', true );
	$summary = (string) get_term_meta( $term->term_id, 'ceori_summary', true );
	$src     = $image ? wp_get_attachment_image_url( $image, 'thumbnail' ) : '';
	echo '<tr class="form-field"><th><label for="ceori_order">' . esc_html__( 'Display order', 'ceori' ) . '</label></th><td><input type="number" id="ceori_order" name="ceori_term[ceori_order]" value="' . esc_attr( $order ) . '"></td></tr>';
	echo '<tr class="form-field"><th><label for="ceori_summary">' . esc_html__( 'One-sentence summary for cards', 'ceori' ) . '</label></th><td><textarea id="ceori_summary" name="ceori_term[ceori_summary]" rows="2">' . esc_textarea( $summary ) . '</textarea></td></tr>';
	echo '<tr class="form-field"><th><label for="ceori_landing_page">' . esc_html__( 'Category landing page', 'ceori' ) . '</label></th><td>';
	wp_dropdown_pages(
		array(
			'id'               => 'ceori_landing_page',
			'name'             => 'ceori_term[ceori_landing_page]',
			'selected'         => $page,
			'show_option_none' => esc_html__( '— None —', 'ceori' ),
		)
	);
	echo '</td></tr>';
	echo '<tr class="form-field"><th>' . esc_html__( 'Card image', 'ceori' ) . '</th><td><div class="ceori-image-field"><img src="' . esc_url( (string) $src ) . '" alt="" style="max-width:120px;border-radius:8px;display:' . ( $src ? 'block' : 'none' ) . '"><input type="hidden" name="ceori_term[ceori_image]" value="' . esc_attr( $image ) . '"><p><button type="button" class="button ceori-image-pick">' . esc_html__( 'Choose image', 'ceori' ) . '</button> <button type="button" class="button-link ceori-image-clear">' . esc_html__( 'Remove', 'ceori' ) . '</button></p></div></td></tr>';
}

add_action(
	'edited_treatment_category',
	function ( $term_id ) {
		if ( ! isset( $_POST['ceori_term_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['ceori_term_nonce'] ), 'ceori_term' ) || ! current_user_can( 'manage_categories' ) ) {
			return;
		}
		$in = isset( $_POST['ceori_term'] ) ? wp_unslash( (array) $_POST['ceori_term'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		update_term_meta( $term_id, 'ceori_order', isset( $in['ceori_order'] ) ? (int) $in['ceori_order'] : 0 );
		update_term_meta( $term_id, 'ceori_landing_page', isset( $in['ceori_landing_page'] ) ? absint( $in['ceori_landing_page'] ) : 0 );
		update_term_meta( $term_id, 'ceori_image', isset( $in['ceori_image'] ) ? absint( $in['ceori_image'] ) : 0 );
		update_term_meta( $term_id, 'ceori_summary', isset( $in['ceori_summary'] ) ? sanitize_textarea_field( $in['ceori_summary'] ) : '' );
	}
);

/*
 * Enquiries list: useful columns and a "new" count bubble.
 */
add_filter(
	'manage_ceori_enquiry_posts_columns',
	function ( $cols ) {
		return array(
			'cb'             => $cols['cb'],
			'title'          => __( 'Name', 'ceori' ),
			'phone'          => __( 'Phone', 'ceori' ),
			'treatment'      => __( 'Treatment', 'ceori' ),
			'doctor'         => __( 'Specialist', 'ceori' ),
			'preferred_date' => __( 'Preferred date', 'ceori' ),
			'status'         => __( 'Status', 'ceori' ),
			'date'           => __( 'Received', 'ceori' ),
		);
	}
);
add_action(
	'manage_ceori_enquiry_posts_custom_column',
	function ( $col, $post_id ) {
		$value = get_post_meta( $post_id, $col, true );
		if ( 'phone' === $col && $value ) {
			echo '<a href="' . esc_attr( ceori_tel_href( $value ) ) . '">' . esc_html( $value ) . '</a>';
			return;
		}
		if ( 'status' === $col ) {
			$opts = ceori_meta_fields()['ceori_enquiry']['status']['options'];
			echo esc_html( $opts[ $value ? $value : 'new' ] ?? $value );
			return;
		}
		echo esc_html( (string) $value );
	},
	10,
	2
);

add_filter(
	'manage_ceori_testimonial_posts_columns',
	function ( $cols ) {
		$cols['consent'] = __( 'Approved for display', 'ceori' );
		return $cols;
	}
);
add_filter(
	'manage_ceori_result_posts_columns',
	function ( $cols ) {
		$cols['consent'] = __( 'Consent on file', 'ceori' );
		return $cols;
	}
);
foreach ( array( 'ceori_testimonial', 'ceori_result' ) as $ceori_pt ) {
	add_action(
		"manage_{$ceori_pt}_posts_custom_column",
		function ( $col, $post_id ) {
			if ( 'consent' === $col ) {
				echo get_post_meta( $post_id, 'consent', true ) ? '✔' : '<span style="color:#b32d2e">' . esc_html__( 'No – hidden on site', 'ceori' ) . '</span>';
			}
		},
		10,
		2
	);
}
