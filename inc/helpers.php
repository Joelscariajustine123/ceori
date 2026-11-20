<?php
/**
 * Small shared helpers: clinic options, phone links and inline SVG icons.
 *
 * @package ceori
 */

defined( 'ABSPATH' ) || exit;

/**
 * Default clinic details, taken from ceoriskin.com (October 2026).
 */
function ceori_option_defaults() {
	return array(
		'clinic_name'     => 'CEORI Skin Clinic',
		'tagline'         => 'Skin, hair and cosmetic care in Calicut',
		'phone_primary'   => '+91 8891 229 555',
		'phone_secondary' => '0495 2387000',
		'whatsapp'        => '',
		'booking_method'  => 'email',
		'booking_whatsapp' => '',
		'booking_email'   => '',
		'email'           => 'care@ceoriskin.com',
		'notify_email'    => '',
		'street'          => 'Eranhipalam Junction, Civil Station PO',
		'locality'        => 'Kozhikode (Calicut)',
		'region'          => 'Kerala',
		'postcode'        => '673020',
		'country'         => 'IN',
		'map_query'       => 'CEORI Skin Clinic, Eranhipalam, Kozhikode, Kerala 673020',
		'map_url'         => '',
		'hours'           => "Monday – Saturday: 9:00 AM – 6:00 PM\nSunday: Closed",
		'hours_schema'    => 'Mo-Sa 09:00-18:00',
		'instagram'       => 'https://www.instagram.com/ceori_skinclinic/',
		'facebook'        => 'https://www.facebook.com/Ceori.Skinclinic',
		'youtube'         => 'https://www.youtube.com/channel/UCDg3xZEPG9Ysq2wckxywwNg',
		'footer_about'    => 'Skin, hair and cosmetic care in Calicut, led by experienced doctors and planned around you.',
		'gtm_id'          => 'GTM-5VD5HMWK',
		'gtm_output'      => '1',
		'home_only'       => '',
		'redirects'       => '',
		'form_rate_limit' => 5,
	);
}

/**
 * Read one clinic option, falling back to the default.
 *
 * @param string $key Option key.
 * @return mixed
 */
function ceori_option( $key ) {
	static $options = null;
	if ( null === $options ) {
		$options = wp_parse_args( (array) get_option( 'ceori_clinic', array() ), ceori_option_defaults() );
	}
	return isset( $options[ $key ] ) ? $options[ $key ] : '';
}

/**
 * Convert a display phone number into a tel: href.
 *
 * @param string $phone Phone number as displayed.
 * @return string
 */
function ceori_tel_href( $phone ) {
	$digits = preg_replace( '/[^0-9+]/', '', (string) $phone );
	if ( '' === $digits ) {
		return '';
	}
	// Landline numbers written with a leading 0 are given the India country code.
	if ( '+' !== $digits[0] ) {
		$digits = '+91' . ltrim( $digits, '0' );
	}
	return 'tel:' . $digits;
}

/**
 * WhatsApp click-to-chat link, only when a verified number is configured.
 *
 * @return string
 */
function ceori_whatsapp_href() {
	$number = preg_replace( '/\D/', '', (string) ceori_option( 'whatsapp' ) );
	return $number ? 'https://wa.me/' . $number : '';
}

/**
 * Digits of the WhatsApp number used for bookings ('' when none is set).
 *
 * @return string
 */
function ceori_booking_whatsapp_number() {
	$number = preg_replace( '/\D/', '', (string) ceori_option( 'booking_whatsapp' ) );
	if ( ! $number ) {
		$number = preg_replace( '/\D/', '', (string) ceori_option( 'whatsapp' ) );
	}
	// A 10-digit Indian mobile number gets the country code.
	if ( 10 === strlen( $number ) ) {
		$number = '91' . $number;
	}
	return $number;
}

/**
 * WhatsApp chat link for the call-to-action: the WhatsApp/booking number from CEORI Clinic
 * settings, otherwise the primary phone when it is an Indian mobile number.
 *
 * @param string $message Optional pre-filled message.
 * @return string '' when no suitable number exists.
 */
function ceori_chat_whatsapp_href( $message = '' ) {
	$number = ceori_booking_whatsapp_number();
	if ( ! $number ) {
		$digits = preg_replace( '/\D/', '', (string) ceori_option( 'phone_primary' ) );
		if ( preg_match( '/^(?:91|0)?([6-9]\d{9})$/', $digits, $m ) ) {
			$number = '91' . $m[1];
		}
	}
	if ( ! $number ) {
		return '';
	}
	return 'https://wa.me/' . $number . ( '' !== $message ? '?text=' . rawurlencode( $message ) : '' );
}

/**
 * Effective booking method; WhatsApp options fall back to email when no number is set.
 *
 * @return string email|whatsapp|email_whatsapp|mailto
 */
function ceori_booking_method() {
	$method = (string) ceori_option( 'booking_method' );
	if ( ! in_array( $method, array( 'email', 'whatsapp', 'email_whatsapp', 'mailto' ), true ) ) {
		$method = 'email';
	}
	if ( false !== strpos( $method, 'whatsapp' ) && ! ceori_booking_whatsapp_number() ) {
		$method = 'email';
	}
	return $method;
}

/**
 * Google Maps link for the clinic.
 *
 * @return string
 */
function ceori_map_href() {
	$url = ceori_option( 'map_url' );
	return $url ? $url : 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode( ceori_option( 'map_query' ) );
}

/**
 * Full postal address on one line.
 *
 * @return string
 */
function ceori_address_line() {
	return trim( implode( ', ', array_filter( array( ceori_option( 'street' ), ceori_option( 'locality' ), ceori_option( 'region' ) . ' ' . ceori_option( 'postcode' ) ) ) ) );
}

/**
 * Inline SVG icon. All icons are decorative (aria-hidden) unless a label is given.
 *
 * @param string $name  Icon name.
 * @param string $label Optional accessible label.
 * @return string
 */
function ceori_icon( $name, $label = '' ) {
	$paths = array(
		'arrow-up-right' => '<path d="M7 17 17 7M8 7h9v9"/>',
		'arrow-right'    => '<path d="M5 12h14M13 6l6 6-6 6"/>',
		'arrow-left'     => '<path d="M19 12H5M11 18l-6-6 6-6"/>',
		'arrow-up'       => '<path d="M12 19V5M6 11l6-6 6 6"/>',
		'phone'          => '<path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2"/>',
		'mail'           => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
		'pin'            => '<path d="M12 21s-7-6.2-7-11.5A7 7 0 0 1 19 9.5C19 14.8 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/>',
		'clock'          => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
		'plus'           => '<path d="M12 5v14M5 12h14"/>',
		'close'          => '<path d="M6 6l12 12M18 6 6 18"/>',
		'check'          => '<path d="m5 12 5 5L20 7"/>',
		'calendar'       => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/>',
		'search'         => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
		'instagram'      => '<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor"/>',
		'facebook'       => '<path d="M14 8h3V4h-3a4 4 0 0 0-4 4v2H7v4h3v7h4v-7h3l1-4h-4V8.5a.5.5 0 0 1 .5-.5z"/>',
		'youtube'        => '<rect x="2.5" y="5.5" width="19" height="13" rx="4"/><path d="m10 9 5 3-5 3z" fill="currentColor"/>',
		'whatsapp'       => '<path d="M4 20l1.3-4A8 8 0 1 1 8 19z"/><path d="M9 9.5c0 3 2.5 5.5 5.5 5.5l1-1.5-2-1-1 .8a4 4 0 0 1-2.3-2.3l.8-1-1-2z"/>',
		'sparkle'        => '<path d="M12 3c.5 4.5 2.5 6.5 7 7-4.5.5-6.5 2.5-7 7-.5-4.5-2.5-6.5-7-7 4.5-.5 6.5-2.5 7-7z"/>',
		'user'           => '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>',
		'shield'         => '<path d="M12 3 5 6v6c0 4.5 3 7.5 7 9 4-1.5 7-4.5 7-9V6z"/><path d="m9 12 2 2 4-4"/>',
		'layers'         => '<path d="m12 3 9 5-9 5-9-5z"/><path d="m3 13 9 5 9-5"/>',
		'heart'          => '<path d="M12 20s-7.5-4.6-7.5-10A4.3 4.3 0 0 1 12 7.6 4.3 4.3 0 0 1 19.5 10c0 5.4-7.5 10-7.5 10z"/>',
		'stethoscope'    => '<path d="M6 3v6a5 5 0 0 0 10 0V3"/><path d="M11 14v2a5 5 0 0 0 10 0v-2"/><circle cx="21" cy="12" r="0"/><circle cx="21" cy="12" r="1.5"/>',
		'device'         => '<rect x="6" y="2.5" width="12" height="19" rx="3"/><path d="M10 18h4"/>',
		'grid'           => '<rect x="3.5" y="3.5" width="7" height="7" rx="2"/><rect x="13.5" y="3.5" width="7" height="7" rx="2"/><rect x="3.5" y="13.5" width="7" height="7" rx="2"/><rect x="13.5" y="13.5" width="7" height="7" rx="2"/>',
		'share'          => '<circle cx="18" cy="5" r="2.5"/><circle cx="6" cy="12" r="2.5"/><circle cx="18" cy="19" r="2.5"/><path d="m8.2 10.8 7.6-4.4M8.2 13.2l7.6 4.4"/>',
		'link'           => '<path d="M10 14a4 4 0 0 0 5.7 0l3-3a4 4 0 0 0-5.7-5.7l-1 1"/><path d="M14 10a4 4 0 0 0-5.7 0l-3 3a4 4 0 0 0 5.7 5.7l1-1"/>',
		'x'              => '<path d="M4 4l16 16M20 4 4 20"/>',
		'quote'          => '<path d="M9 7H6a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h2v1a3 3 0 0 1-3 3M19 7h-3a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h2v1a3 3 0 0 1-3 3"/>',
	);
	if ( ! isset( $paths[ $name ] ) ) {
		return '';
	}
	$a11y = $label ? 'role="img" aria-label="' . esc_attr( $label ) . '"' : 'aria-hidden="true" focusable="false"';
	return '<svg class="ceori-icon ceori-icon--' . esc_attr( $name ) . '" ' . $a11y . ' width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">' . $paths[ $name ] . '</svg>';
}

/**
 * Treatment category terms in menu order.
 *
 * @return WP_Term[]
 */
function ceori_treatment_categories() {
	$terms = get_terms(
		array(
			'taxonomy'   => 'treatment_category',
			'hide_empty' => false,
			'meta_key'   => 'ceori_order',
			'orderby'    => 'meta_value_num',
			'order'      => 'ASC',
		)
	);
	return is_wp_error( $terms ) ? array() : $terms;
}

/**
 * Landing page linked to a treatment category (stored as term meta).
 *
 * @param WP_Term $term Term.
 * @return string URL.
 */
function ceori_category_url( $term ) {
	$page_id = (int) get_term_meta( $term->term_id, 'ceori_landing_page', true );
	return $page_id ? get_permalink( $page_id ) : home_url( '/treatments/' );
}

/**
 * Image used by a pattern: the Media Library attachment mapped by the content importer,
 * or the small fallback bundled with the theme (assets/images/{key}.webp).
 *
 * @param string $key Media key (e.g. "hero-portrait").
 * @return array { id: int, url: string, alt: string }
 */
function ceori_media( $key ) {
	$map = (array) get_option( 'ceori_media_map', array() );
	$id  = isset( $map[ $key ] ) ? (int) $map[ $key ] : 0;
	if ( $id && wp_attachment_is_image( $id ) ) {
		return array(
			'id'  => $id,
			'url' => (string) wp_get_attachment_image_url( $id, 'large' ),
			'alt' => (string) get_post_meta( $id, '_wp_attachment_image_alt', true ),
		);
	}
	$alts = array(
		'hero-portrait' => __( 'Smiling woman with clear, healthy skin', 'ceori' ),
		'hero-card'     => __( 'Hydra facial treatment in progress', 'ceori' ),
		'about-main'    => __( 'Dermatologist performing a skin treatment at CEORI Skin Clinic', 'ceori' ),
		'why-center'    => __( 'Woman with radiant skin touching her face', 'ceori' ),
	);
	return array(
		'id'  => 0,
		'url' => CEORI_URI . '/assets/images/' . sanitize_file_name( $key ) . '.webp',
		'alt' => isset( $alts[ $key ] ) ? $alts[ $key ] : '',
	);
}

/**
 * Serialized core/image block for a media key (valid block markup for the editor).
 *
 * @param string $key       Media key.
 * @param string $class     Extra class names.
 * @param bool   $decorative Use empty alt text.
 * @return string
 */
function ceori_image_block( $key, $class = '', $decorative = false ) {
	$m     = ceori_media( $key );
	$alt   = $decorative ? '' : $m['alt'];
	$attrs = array(
		'sizeSlug'        => 'large',
		'linkDestination' => 'none',
	);
	if ( $m['id'] ) {
		$attrs = array( 'id' => $m['id'] ) + $attrs;
	}
	if ( $class ) {
		$attrs['className'] = $class;
	}
	$img_class = $m['id'] ? ' class="wp-image-' . $m['id'] . '"' : '';
	return '<!-- wp:image ' . wp_json_encode( $attrs, JSON_UNESCAPED_SLASHES ) . " -->\n"
		. '<figure class="wp-block-image size-large' . ( $class ? ' ' . esc_attr( $class ) : '' ) . '"><img src="' . esc_url( $m['url'] ) . '" alt="' . esc_attr( $alt ) . '"' . $img_class . '/></figure>' . "\n"
		. '<!-- /wp:image -->';
}

/**
 * Group block "style" attribute fragment for a background image (rendered by core block supports).
 *
 * @param string $key Media key.
 * @return array
 */
function ceori_bg_style( $key ) {
	$m  = ceori_media( $key );
	$bg = array(
		'url'    => $m['url'],
		'source' => 'file',
		'title'  => $key,
	);
	if ( $m['id'] ) {
		$bg['id'] = $m['id'];
	}
	return array(
		'background' => array(
			'backgroundImage'    => $bg,
			'backgroundSize'     => 'cover',
			'backgroundPosition' => '50% 50%',
		),
	);
}

/**
 * Whether a published Elementor Header & Footer template is active for this request.
 *
 * @param string $part "header" or "footer".
 * @return bool
 */
function ceori_hfe_active( $part ) {
	if ( 'header' === $part ) {
		return function_exists( 'hfe_header_enabled' ) && function_exists( 'hfe_render_header' ) && hfe_header_enabled();
	}
	return function_exists( 'hfe_footer_enabled' ) && function_exists( 'hfe_render_footer' ) && hfe_footer_enabled();
}

/**
 * Whether a post is built with Elementor (and Elementor is active).
 *
 * @param int $post_id Post ID.
 * @return bool
 */
function ceori_is_elementor_page( $post_id ) {
	if ( ! did_action( 'elementor/loaded' ) ) {
		return false;
	}
	$document = \Elementor\Plugin::$instance->documents->get( $post_id );
	return $document && $document->is_built_with_elementor();
}

/**
 * Escaped site URL for a path, used in patterns so links work in subfolder installs too.
 *
 * @param string $path Path such as "/treatments/".
 * @return string
 */
function ceori_link( $path ) {
	return esc_url( home_url( $path ) );
}

/**
 * Appointment page URL (contact page with the form), optionally preselecting a treatment.
 *
 * @param string $treatment Treatment or category label.
 * @return string
 */
function ceori_appointment_url( $treatment = '' ) {
	$page = get_page_by_path( 'contact-us' );
	$url  = $page ? get_permalink( $page ) : home_url( '/contact-us/' );
	if ( $treatment ) {
		$url = add_query_arg( 'treatment', rawurlencode( $treatment ), $url );
	}
	return $url . '#appointment';
}

/**
 * Escaped link text; generic wording ("Learn more", "Read more"…) gets the
 * subject added for screen readers and search engines.
 *
 * @param string $text    Visible link text.
 * @param string $subject What the link is about.
 * @return string HTML.
 */
function ceori_link_text( $text, $subject ) {
	$generic = array( 'learn more', 'read more', 'more', 'view more', 'see more', 'details', 'click here', 'explore', 'explore treatments', 'view profile' );
	$html    = esc_html( $text );
	if ( $subject && in_array( strtolower( trim( wp_strip_all_tags( $text ) ) ), $generic, true ) ) {
		$html .= '<span class="screen-reader-text"> – ' . esc_html( wp_strip_all_tags( $subject ) ) . '</span>';
	}
	return $html;
}
