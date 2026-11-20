<?php
/**
 * Appointment enquiry handling.
 *
 * The form submits to a REST endpoint (JavaScript) or to admin-post.php (no JavaScript).
 * Both paths share one validator and save the request as a private "Enquiry" post and
 * email the clinic. Spam protection: nonce, honeypot field, minimum fill time and an
 * hourly per-visitor rate limit. Email goes through wp_mail(), so any SMTP plugin works.
 *
 * @package ceori
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'rest_api_init',
	function () {
		register_rest_route(
			'ceori/v1',
			'/enquiry',
			array(
				'methods'             => 'POST',
				'permission_callback' => '__return_true',
				'callback'            => function ( WP_REST_Request $request ) {
					$result = ceori_process_enquiry( $request->get_body_params() );
					return new WP_REST_Response( $result, $result['ok'] ? 200 : ( isset( $result['status'] ) ? $result['status'] : 422 ) );
				},
			)
		);
		// Fresh nonce for pages served from a full-page cache.
		register_rest_route(
			'ceori/v1',
			'/nonce',
			array(
				'methods'             => 'GET',
				'permission_callback' => '__return_true',
				'callback'            => function () {
					nocache_headers();
					do_action( 'litespeed_control_set_nocache', 'ceori form nonce' );
					return array( 'nonce' => wp_create_nonce( 'ceori_enquiry' ) );
				},
			)
		);
	}
);

add_action( 'admin_post_nopriv_ceori_enquiry', 'ceori_handle_enquiry_post' );
add_action( 'admin_post_ceori_enquiry', 'ceori_handle_enquiry_post' );

/**
 * No-JavaScript submission: process and redirect back to the form.
 */
function ceori_handle_enquiry_post() {
	$result = ceori_process_enquiry( wp_unslash( $_POST ) ); // phpcs:ignore WordPress.Security.NonceVerification -- verified inside.
	if ( $result['ok'] && ! empty( $result['redirect'] ) ) {
		// WhatsApp / email app: wa.me and mailto: are not "safe" local redirects, so use wp_redirect deliberately.
		wp_redirect( $result['redirect'] ); // phpcs:ignore WordPress.Security.SafeRedirect
		exit;
	}
	$back   = wp_get_referer() ? wp_get_referer() : home_url( '/contact-us/' );
	$back   = remove_query_arg( 'enquiry', $back );
	wp_safe_redirect( add_query_arg( 'enquiry', $result['ok'] ? 'sent' : 'error', $back ) . '#appointment' );
	exit;
}

/**
 * Normalise and validate an Indian phone number (mobile or landline).
 *
 * Accepts e.g. 98765 43210, +91 98765 43210, 091-9876543210, 0495 2387000.
 *
 * @param string $raw Raw input.
 * @return string|false Normalised number or false.
 */
function ceori_normalise_phone( $raw ) {
	$digits = preg_replace( '/[\s\-().]/', '', (string) $raw );
	if ( ! preg_match( '/^\+?\d{10,14}$/', $digits ) ) {
		return false;
	}
	$plain = ltrim( $digits, '+' );
	// Mobile: optional 91 / 0 prefix, then 10 digits starting 6–9.
	if ( preg_match( '/^(?:91|0)?([6-9]\d{9})$/', $plain, $m ) ) {
		return '+91 ' . substr( $m[1], 0, 5 ) . ' ' . substr( $m[1], 5 );
	}
	// Landline: STD code (with leading 0 or 91) plus subscriber number, 10 digits without the 0.
	if ( preg_match( '/^(?:91|0)([1-9]\d{9})$/', $plain, $m ) ) {
		return '+91 ' . $m[1];
	}
	return false;
}

/**
 * Visitor fingerprint for rate limiting (hashed, never stored raw).
 */
function ceori_visitor_key() {
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '0.0.0.0';
	return 'ceori_rl_' . substr( hash_hmac( 'sha256', $ip, wp_salt( 'nonce' ) ), 0, 20 );
}

/**
 * Validate, store and email an enquiry.
 *
 * @param array $data Submitted fields.
 * @return array { ok: bool, message: string, errors?: array }
 */
function ceori_process_enquiry( $data ) {
	$generic = __( 'Sorry, we could not send your request. Please check the form or call us.', 'ceori' );

	if ( empty( $data['ceori_nonce'] ) || ! wp_verify_nonce( $data['ceori_nonce'], 'ceori_enquiry' ) ) {
		return array(
			'ok'      => false,
			'code'    => 'stale_nonce',
			'status'  => 403,
			'message' => __( 'Your session expired. Please submit the form again.', 'ceori' ),
		);
	}

	// Honeypot and minimum fill time (bots submit instantly). Pretend success to bots.
	$started = isset( $data['started'] ) ? (int) $data['started'] : 0;
	if ( ! empty( $data['website'] ) || ( $started && time() - $started < 3 ) ) {
		return array(
			'ok'      => true,
			'message' => __( 'Thank you. Our team will contact you shortly.', 'ceori' ),
		);
	}

	$key   = ceori_visitor_key();
	$count = (int) get_transient( $key );
	if ( $count >= (int) ceori_option( 'form_rate_limit' ) ) {
		return array(
			'ok'      => false,
			'status'  => 429,
			'message' => __( 'You have sent several requests recently. Please call us if your request is urgent.', 'ceori' ),
		);
	}

	$errors = array();
	$name   = isset( $data['full_name'] ) ? sanitize_text_field( $data['full_name'] ) : '';
	if ( mb_strlen( $name ) < 2 || mb_strlen( $name ) > 80 ) {
		$errors['full_name'] = __( 'Please enter your full name.', 'ceori' );
	}

	$phone = ceori_normalise_phone( isset( $data['phone'] ) ? $data['phone'] : '' );
	if ( ! $phone ) {
		$errors['phone'] = __( 'Please enter a valid Indian mobile or landline number, e.g. +91 98765 43210.', 'ceori' );
	}

	$email = isset( $data['email'] ) ? sanitize_email( $data['email'] ) : '';
	if ( ! empty( $data['email'] ) && ! is_email( $email ) ) {
		$errors['email'] = __( 'Please enter a valid email address or leave it empty.', 'ceori' );
	}

	$treatment = isset( $data['treatment'] ) ? sanitize_text_field( $data['treatment'] ) : '';
	if ( '' === $treatment || mb_strlen( $treatment ) > 120 ) {
		$errors['treatment'] = __( 'Please choose a treatment category.', 'ceori' );
	}

	$date = isset( $data['preferred_date'] ) ? sanitize_text_field( $data['preferred_date'] ) : '';
	if ( $date ) {
		$d = DateTime::createFromFormat( 'Y-m-d', $date, wp_timezone() );
		if ( ! $d || $d->format( 'Y-m-d' ) !== $date ) {
			$errors['preferred_date'] = __( 'Please choose a valid date.', 'ceori' );
		} elseif ( $date < wp_date( 'Y-m-d' ) ) {
			$errors['preferred_date'] = __( 'Please choose today or a future date.', 'ceori' );
		}
	}

	$message = isset( $data['message'] ) ? mb_substr( sanitize_textarea_field( $data['message'] ), 0, 1000 ) : '';
	$doctor  = isset( $data['doctor'] ) ? mb_substr( sanitize_text_field( $data['doctor'] ), 0, 120 ) : '';

	if ( empty( $data['consent'] ) ) {
		$errors['consent'] = __( 'Please confirm that we may contact you about your request.', 'ceori' );
	}

	if ( $errors ) {
		return array(
			'ok'      => false,
			'message' => $generic,
			'errors'  => $errors,
		);
	}

	set_transient( $key, $count + 1, HOUR_IN_SECONDS );

	$source  = isset( $data['source_url'] ) ? esc_url_raw( $data['source_url'] ) : '';
	$post_id = wp_insert_post(
		array(
			'post_type'   => 'ceori_enquiry',
			'post_status' => 'private',
			'post_title'  => $name,
		),
		true
	);
	if ( ! is_wp_error( $post_id ) ) {
		$meta = array(
			'phone'          => $phone,
			'email'          => $email,
			'treatment'      => $treatment,
			'doctor'         => $doctor,
			'preferred_date' => $date,
			'message'        => $message,
			'source_url'     => $source,
			'status'         => 'new',
		);
		foreach ( $meta as $k => $v ) {
			update_post_meta( $post_id, $k, $v );
		}
	}

	$method  = ceori_booking_method();
	$details = array(
		__( 'Name:', 'ceori' ) . ' ' . $name,
		__( 'Phone:', 'ceori' ) . ' ' . $phone,
		__( 'Email:', 'ceori' ) . ' ' . ( $email ? $email : '—' ),
		__( 'Treatment:', 'ceori' ) . ' ' . $treatment,
		__( 'Preferred specialist:', 'ceori' ) . ' ' . ( $doctor ? $doctor : __( 'No preference', 'ceori' ) ),
		__( 'Preferred date:', 'ceori' ) . ' ' . ( $date ? $date : '—' ),
		__( 'Message:', 'ceori' ) . ' ' . ( $message ? $message : '—' ),
	);
	if ( ! is_wp_error( $post_id ) ) {
		update_post_meta( $post_id, 'booking_method', $method );
	}

	// 1. Email from the website.
	$sent = false;
	if ( in_array( $method, array( 'email', 'email_whatsapp' ), true ) ) {
		$to      = ceori_option( 'notify_email' ) ? ceori_option( 'notify_email' ) : get_option( 'admin_email' );
		$subject = sprintf( /* translators: 1: name, 2: treatment */ __( 'New appointment request: %1$s (%2$s)', 'ceori' ), $name, $treatment );
		$body    = implode(
			"\n",
			array_merge(
				array( __( 'A new appointment request was submitted on the website.', 'ceori' ), '' ),
				$details,
				array(
					__( 'Page:', 'ceori' ) . ' ' . $source,
					'',
					__( 'This is a request only. Please call the patient to confirm availability.', 'ceori' ),
					is_wp_error( $post_id ) ? '' : __( 'View in dashboard:', 'ceori' ) . ' ' . admin_url( 'post.php?post=' . $post_id . '&action=edit' ),
				)
			)
		);
		$headers = $email ? array( 'Reply-To: ' . $name . ' <' . $email . '>' ) : array();
		$sent    = wp_mail( $to, $subject, $body, $headers );
		if ( ! is_wp_error( $post_id ) ) {
			update_post_meta( $post_id, 'email_sent', $sent ? 1 : 0 );
		}
		if ( is_wp_error( $post_id ) && ! $sent ) {
			return array(
				'ok'      => false,
				'status'  => 500,
				'message' => $generic,
			);
		}
	}

	// 2. WhatsApp or the visitor's email app, opened with the details filled in.
	$redirect = '';
	$intro    = sprintf( /* translators: %s: clinic name */ __( 'Hello %s, I would like to request an appointment.', 'ceori' ), ceori_option( 'clinic_name' ) );
	if ( in_array( $method, array( 'whatsapp', 'email_whatsapp' ), true ) ) {
		$redirect = 'https://wa.me/' . ceori_booking_whatsapp_number() . '?text=' . rawurlencode( $intro . "\n\n" . implode( "\n", $details ) );
	} elseif ( 'mailto' === $method ) {
		$to       = ceori_option( 'booking_email' ) ? ceori_option( 'booking_email' ) : ceori_option( 'email' );
		$redirect = 'mailto:' . $to . '?subject=' . rawurlencode( sprintf( /* translators: %s: name */ __( 'Appointment request – %s', 'ceori' ), $name ) ) . '&body=' . rawurlencode( $intro . "\n\n" . implode( "\n", $details ) );
	}

	$messages = array(
		'email'          => __( 'Thank you – we have received your request. Our team will call you to confirm a suitable appointment time. Your appointment is not confirmed until we contact you.', 'ceori' ),
		'whatsapp'       => __( 'Opening WhatsApp with your details – please tap Send to share them with our team. Your appointment is confirmed only when we reply.', 'ceori' ),
		'email_whatsapp' => __( 'Thank you – we have received your request. WhatsApp is opening with your details; tap Send to message our team directly. Your appointment is confirmed only when we reply.', 'ceori' ),
		'mailto'         => __( 'Opening your email app with your details – please press Send to share them with our team. Your appointment is confirmed only when we reply.', 'ceori' ),
	);

	return array(
		'ok'       => true,
		'message'  => $messages[ $method ],
		'redirect' => $redirect,
	);
}

// Count of new enquiries in the admin menu.
add_action(
	'admin_menu',
	function () {
		global $menu;
		$count = (int) ( new WP_Query(
			array(
				'post_type'      => 'ceori_enquiry',
				'post_status'    => 'private',
				'meta_key'       => 'status',
				'meta_value'     => 'new',
				'fields'         => 'ids',
				'posts_per_page' => 1,
			)
		) )->found_posts;
		if ( ! $count ) {
			return;
		}
		foreach ( $menu as $i => $item ) {
			if ( isset( $item[2] ) && 'edit.php?post_type=ceori_enquiry' === $item[2] ) {
				$menu[ $i ][0] .= ' <span class="awaiting-mod"><span class="pending-count">' . (int) $count . '</span></span>'; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
			}
		}
	},
	99
);
