<?php
/**
 * "CEORI Clinic" settings screen: contact details, hours, social links,
 * tracking and redirects. Everything here is editable without code.
 *
 * @package ceori
 */

defined( 'ABSPATH' ) || exit;

/**
 * Settings fields grouped by section.
 */
function ceori_settings_fields() {
	return array(
		'contact'  => array(
			'title'  => __( 'Clinic contact details', 'ceori' ),
			'fields' => array(
				'clinic_name'     => array( __( 'Clinic name', 'ceori' ), 'text' ),
				'tagline'         => array( __( 'Short tagline', 'ceori' ), 'text' ),
				'phone_primary'   => array( __( 'Primary phone (mobile)', 'ceori' ), 'text' ),
				'phone_secondary' => array( __( 'Secondary phone (landline)', 'ceori' ), 'text' ),
				'whatsapp'        => array( __( 'WhatsApp number (with country code). Leave empty to hide the WhatsApp button.', 'ceori' ), 'text' ),
				'email'           => array( __( 'Public email address', 'ceori' ), 'email' ),
				'notify_email'    => array( __( 'Send appointment enquiries to (defaults to the site admin email)', 'ceori' ), 'email' ),
			),
		),
		'booking'  => array(
			'title'  => __( 'Appointment booking', 'ceori' ),
			'fields' => array(
				'booking_method'   => array(
					__( 'When a visitor sends the appointment form', 'ceori' ),
					'select',
					array(
						'email'          => __( 'Email the details to the clinic (from the website)', 'ceori' ),
						'whatsapp'       => __( 'Open WhatsApp with the details filled in (visitor taps Send)', 'ceori' ),
						'email_whatsapp' => __( 'Both: email the clinic and open WhatsApp', 'ceori' ),
						'mailto'         => __( 'Open the visitor’s email app with the details filled in', 'ceori' ),
					),
				),
				'booking_whatsapp' => array( __( 'WhatsApp number for bookings (with country code, e.g. 918891229555). Leave empty to use the WhatsApp number above.', 'ceori' ), 'text' ),
				'booking_email'    => array( __( 'Email address for the “email app” option (defaults to the public email address)', 'ceori' ), 'email' ),
			),
		),
		'location' => array(
			'title'  => __( 'Location & opening hours', 'ceori' ),
			'fields' => array(
				'street'       => array( __( 'Street address', 'ceori' ), 'text' ),
				'locality'     => array( __( 'City', 'ceori' ), 'text' ),
				'region'       => array( __( 'State', 'ceori' ), 'text' ),
				'postcode'     => array( __( 'PIN code', 'ceori' ), 'text' ),
				'country'      => array( __( 'Country code', 'ceori' ), 'text' ),
				'map_query'    => array( __( 'Google Maps search text (used for the embedded map)', 'ceori' ), 'text' ),
				'map_url'      => array( __( 'Google Maps place link (optional, e.g. your Google Business Profile link)', 'ceori' ), 'url' ),
				'hours'        => array( __( 'Opening hours (one line per row)', 'ceori' ), 'textarea' ),
				'hours_schema' => array( __( 'Opening hours for search engines (e.g. Mo-Sa 09:00-18:00)', 'ceori' ), 'text' ),
			),
		),
		'social'   => array(
			'title'  => __( 'Social media & footer', 'ceori' ),
			'fields' => array(
				'instagram'    => array( __( 'Instagram URL', 'ceori' ), 'url' ),
				'facebook'     => array( __( 'Facebook URL', 'ceori' ), 'url' ),
				'youtube'      => array( __( 'YouTube URL', 'ceori' ), 'url' ),
				'footer_about' => array( __( 'Footer introduction', 'ceori' ), 'textarea' ),
			),
		),
		'preview'  => array(
			'title'  => __( 'Home page only mode', 'ceori' ),
			'fields' => array(
				'home_only' => array( __( 'Show only the home page to visitors and team members: every other page redirects to the home page and all links on it are disabled. Administrators still see the whole site; WP Admin and the Elementor editor keep working', 'ceori' ), 'checkbox' ),
			),
		),
		'advanced' => array(
			'title'  => __( 'Tracking, redirects & spam protection', 'ceori' ),
			'fields' => array(
				'gtm_id'          => array( __( 'Google Tag Manager container ID', 'ceori' ), 'text' ),
				'gtm_output'      => array( __( 'Output the GTM container from the theme (turn off if a plugin such as Site Kit or GTM4WP already adds it)', 'ceori' ), 'checkbox' ),
				'redirects'       => array( __( '301 redirects – one per line: /old-path/ /new-path/ (only used when the old URL would otherwise return 404; a redirect plugin also works)', 'ceori' ), 'textarea' ),
				'form_rate_limit' => array( __( 'Maximum enquiries per visitor per hour', 'ceori' ), 'number' ),
			),
		),
	);
}

// Contact details and the home page only switch appear on every page: clear the page cache when they change.
add_action(
	'update_option_ceori_clinic',
	function () {
		do_action( 'litespeed_purge_all' );
	}
);

add_action(
	'admin_menu',
	function () {
		add_menu_page(
			__( 'CEORI Clinic Settings', 'ceori' ),
			__( 'CEORI Clinic', 'ceori' ),
			'manage_options',
			'ceori-clinic',
			'ceori_render_settings_page',
			'dashicons-heart',
			3
		);
	}
);

add_action(
	'admin_init',
	function () {
		register_setting(
			'ceori_clinic',
			'ceori_clinic',
			array(
				'type'              => 'array',
				'sanitize_callback' => 'ceori_sanitize_settings',
				'default'           => array(),
			)
		);
		foreach ( ceori_settings_fields() as $section_id => $section ) {
			add_settings_section( 'ceori_' . $section_id, $section['title'], '__return_false', 'ceori-clinic' );
			foreach ( $section['fields'] as $key => $field ) {
				add_settings_field(
					$key,
					esc_html( $field[0] ),
					'ceori_render_settings_field',
					'ceori-clinic',
					'ceori_' . $section_id,
					array(
						'key'       => $key,
						'type'      => $field[1],
						'options'   => isset( $field[2] ) ? $field[2] : array(),
						'label_for' => 'ceori_' . $key,
					)
				);
			}
		}
	}
);

/**
 * Sanitize the settings array.
 *
 * @param array $input Raw input.
 * @return array
 */
function ceori_sanitize_settings( $input ) {
	$clean = array();
	foreach ( ceori_settings_fields() as $section ) {
		foreach ( $section['fields'] as $key => $field ) {
			$value = isset( $input[ $key ] ) ? $input[ $key ] : '';
			switch ( $field[1] ) {
				case 'email':
					$clean[ $key ] = sanitize_email( $value );
					break;
				case 'url':
					$clean[ $key ] = esc_url_raw( $value );
					break;
				case 'textarea':
					$clean[ $key ] = sanitize_textarea_field( $value );
					break;
				case 'checkbox':
					$clean[ $key ] = $value ? '1' : '0';
					break;
				case 'number':
					$clean[ $key ] = max( 1, absint( $value ) );
					break;
				case 'select':
					$clean[ $key ] = array_key_exists( $value, $field[2] ) ? $value : (string) key( $field[2] );
					break;
				default:
					$clean[ $key ] = sanitize_text_field( $value );
			}
		}
	}
	return $clean;
}

/**
 * Render one settings field.
 *
 * @param array $args Field args.
 */
function ceori_render_settings_field( $args ) {
	$key   = $args['key'];
	$value = ceori_option( $key );
	$name  = 'ceori_clinic[' . $key . ']';
	$id    = 'ceori_' . $key;
	switch ( $args['type'] ) {
		case 'textarea':
			printf( '<textarea class="large-text" rows="%d" id="%s" name="%s">%s</textarea>', 'redirects' === $key ? 8 : 3, esc_attr( $id ), esc_attr( $name ), esc_textarea( $value ) );
			break;
		case 'select':
			printf( '<select id="%s" name="%s">', esc_attr( $id ), esc_attr( $name ) );
			foreach ( $args['options'] as $opt => $label ) {
				printf( '<option value="%s" %s>%s</option>', esc_attr( $opt ), selected( (string) $value, (string) $opt, false ), esc_html( $label ) );
			}
			echo '</select>';
			break;
		case 'checkbox':
			printf( '<input type="checkbox" id="%s" name="%s" value="1" %s>', esc_attr( $id ), esc_attr( $name ), checked( '1', (string) $value, false ) );
			break;
		default:
			printf( '<input class="regular-text" type="%s" id="%s" name="%s" value="%s">', esc_attr( $args['type'] ), esc_attr( $id ), esc_attr( $name ), esc_attr( $value ) );
	}
}

/**
 * Settings page markup.
 */
function ceori_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	echo '<div class="wrap"><h1>' . esc_html__( 'CEORI Clinic Settings', 'ceori' ) . '</h1>';
	echo '<p>' . esc_html__( 'These details appear in the header, footer, contact page, appointment form emails and search-engine structured data.', 'ceori' ) . '</p>';
	echo '<form method="post" action="options.php">';
	settings_fields( 'ceori_clinic' );
	do_settings_sections( 'ceori-clinic' );
	submit_button();
	echo '</form></div>';
}

// Warn when WhatsApp booking is selected without a WhatsApp number.
add_action(
	'admin_notices',
	function () {
		if ( ! current_user_can( 'manage_options' ) || false === strpos( (string) ceori_option( 'booking_method' ), 'whatsapp' ) || ceori_booking_whatsapp_number() ) {
			return;
		}
		echo '<div class="notice notice-warning"><p>' . wp_kses_post(
			sprintf(
				/* translators: %s: settings link */
				__( 'Appointment booking is set to WhatsApp, but no WhatsApp number is saved. Visitors’ requests are being emailed instead. <a href="%s">Add the number in CEORI Clinic settings</a>.', 'ceori' ),
				esc_url( admin_url( 'admin.php?page=ceori-clinic' ) )
			)
		) . '</p></div>';
	}
);
