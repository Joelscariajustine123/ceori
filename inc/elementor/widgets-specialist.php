<?php
/**
 * Specialist page widgets: Specialist Header (photo, name, facts, booking) and
 * Specialist About (description beside a "Specialised in" card).
 *
 * @package ceori
 */

namespace Ceori\Elementor;

use Elementor\Controls_Manager;
use Elementor\Repeater;

defined( 'ABSPATH' ) || exit;

/**
 * Specialist page header.
 */
class Specialist_Header extends Widget {

	public function get_name() {
		return 'ceori-specialist-header';
	}

	public function get_title() {
		return __( 'CEORI Specialist Header', 'ceori' );
	}

	public function get_icon() {
		return 'eicon-person';
	}

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => __( 'Specialist', 'ceori' ) ) );
		$this->add_control(
			'photo',
			array(
				'label' => __( 'Photo', 'ceori' ),
				'type'  => Controls_Manager::MEDIA,
			)
		);
		$this->add_control(
			'name',
			array(
				'label'       => __( 'Name (page heading, H1)', 'ceori' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'Leave empty to use the page title.', 'ceori' ),
			)
		);
		$this->add_control(
			'role',
			array(
				'label' => __( 'Designation', 'ceori' ),
				'type'  => Controls_Manager::TEXT,
			)
		);
		$this->add_control(
			'specialisation',
			array(
				'label'       => __( 'Specialised in (short line)', 'ceori' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
			)
		);
		$this->add_control(
			'qualifications',
			array(
				'label'       => __( 'Qualifications (verified only, optional)', 'ceori' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
			)
		);
		$r = new Repeater();
		$r->add_control(
			'fact_label',
			array(
				'label' => __( 'Label', 'ceori' ),
				'type'  => Controls_Manager::TEXT,
			)
		);
		$r->add_control(
			'fact_value',
			array(
				'label' => __( 'Value', 'ceori' ),
				'type'  => Controls_Manager::TEXT,
			)
		);
		$this->add_control(
			'facts',
			array(
				'label'       => __( 'Quick facts', 'ceori' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $r->get_controls(),
				'title_field' => '{{{ fact_label }}}',
				'default'     => array(
					array(
						'fact_label' => 'Department',
						'fact_value' => 'Dermatology',
					),
					array(
						'fact_label' => 'Location',
						'fact_value' => 'Eranhipalam, Calicut',
					),
					array(
						'fact_label' => 'Consultations',
						'fact_value' => 'By appointment',
					),
				),
			)
		);
		$this->button_controls( 'button', __( 'Booking button text', 'ceori' ), 'Book an Appointment', home_url( '/contact-us/#appointment' ) );
		$this->add_control(
			'show_external',
			array(
				'label'        => __( 'Use the specialist’s booking link (e.g. Malabar Hospitals) for the booking button', 'ceori' ),
				'description'  => __( 'Set under Specialists in the dashboard. Without a link the button opens the clinic’s appointment form.', 'ceori' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);
		$this->add_control(
			'show_call',
			array(
				'label'        => __( 'Show “Call us” button', 'ceori' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);
		$this->add_control(
			'show_breadcrumbs',
			array(
				'label'        => __( 'Show breadcrumbs', 'ceori' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);
		$this->end_controls_section();

		$this->style_box( 'frame', __( 'Header box', 'ceori' ), '.ceori-dochead__frame' );
		$this->style_image( 'photo', __( 'Photo', 'ceori' ), '.ceori-dochead__photo img' );
		$this->style_text( 'role', __( 'Designation', 'ceori' ), '.ceori-dochead__role' );
		$this->style_text( 'name', __( 'Name', 'ceori' ), '.ceori-dochead__name' );
		$this->style_text( 'spec', __( 'Specialised in', 'ceori' ), '.ceori-dochead__spec' );
		$this->style_text( 'quals', __( 'Qualifications', 'ceori' ), '.ceori-dochead__quals' );
		$this->style_box( 'facts', __( 'Quick facts box', 'ceori' ), '.ceori-dochead__facts' );
		$this->style_text( 'fact_label', __( 'Quick facts – labels', 'ceori' ), '.ceori-dochead__facts dt' );
		$this->style_text( 'fact_value', __( 'Quick facts – values', 'ceori' ), '.ceori-dochead__facts dd' );
		$this->style_button( 'button', __( 'Booking button', 'ceori' ), '.has-arrow .wp-block-button__link' );
		$this->style_button( 'call', __( 'Call button', 'ceori' ), '.has-phone .wp-block-button__link' );
	}

	protected function render() {
		$s     = $this->get_settings_for_display();
		$name  = '' !== trim( (string) $s['name'] ) ? $s['name'] : get_the_title( $this->page_id() );
		$facts = '';
		foreach ( (array) $s['facts'] as $fact ) {
			if ( $fact['fact_label'] || $fact['fact_value'] ) {
				$facts .= '<div><dt>' . esc_html( $fact['fact_label'] ) . '</dt><dd>' . esc_html( $fact['fact_value'] ) . '</dd></div>';
			}
		}
		// Doctors who take bookings through Malabar Hospitals (Specialists → "Booking link"): the
		// booking button opens that page in a new tab instead of the clinic's appointment form.
		$doctor = 'yes' === $s['show_external'] ? ceori_doctor_for_page( $this->page_id() ) : 0;
		$ext    = $doctor ? (string) get_post_meta( $doctor, 'external_url', true ) : '';
		$label  = $doctor ? trim( (string) get_post_meta( $doctor, 'external_label', true ) ) : '';
		$book   = $ext
			? array(
				'url'         => $ext,
				'is_external' => true,
			)
			: $s['button_link'];
		$buttons = $this->button( $ext && $label ? $label : $s['button_text'], $book )
			. ( 'yes' === $s['show_call'] ? $this->button( __( 'Call Us', 'ceori' ), ceori_tel_href( ceori_option( 'phone_primary' ) ), 'phone' ) : '' );

		echo '<section class="ceori-dochead"><div class="ceori-dochead__frame">';
		echo $this->figure( $s['photo'], 'ceori-portrait', 'ceori-dochead__photo', array( 'alt' => trim( $name . ', ' . $s['role'], ', ' ), 'loading' => 'eager', 'fetchpriority' => 'high' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
		echo '<div class="ceori-dochead__body">';
		if ( 'yes' === $s['show_breadcrumbs'] ) {
			echo ceori_breadcrumb_html(); // phpcs:ignore WordPress.Security.EscapeOutput
		}
		echo $s['role'] ? '<p class="ceori-eyebrow ceori-dochead__role">' . esc_html( $s['role'] ) . '</p>' : '';
		echo '<h1 class="ceori-dochead__name">' . esc_html( $name ) . '</h1>';
		echo $s['specialisation'] ? '<p class="ceori-dochead__spec">' . esc_html( $s['specialisation'] ) . '</p>' : '';
		echo $s['qualifications'] ? '<p class="ceori-dochead__quals">' . esc_html( $s['qualifications'] ) . '</p>' : '';
		echo $facts ? '<dl class="ceori-dochead__facts">' . $facts . '</dl>' : ''; // phpcs:ignore WordPress.Security.EscapeOutput
		echo $this->buttons( $buttons, 'ceori-dochead__actions' ); // phpcs:ignore WordPress.Security.EscapeOutput
		echo '</div></div></section>';
	}
}

/**
 * Specialist description beside a "Specialised in" card.
 */
class Specialist_About extends Widget {

	public function get_name() {
		return 'ceori-specialist-about';
	}

	public function get_title() {
		return __( 'CEORI Specialist About', 'ceori' );
	}

	public function get_icon() {
		return 'eicon-text';
	}

	protected function register_controls() {
		$this->heading_controls(
			array(
				'eyebrow' => 'About',
				'title'   => 'Care you can <em>trust</em>',
			)
		);
		$this->start_controls_section( 'content', array( 'label' => __( 'Description & card', 'ceori' ) ) );
		$this->add_control(
			'text',
			array(
				'label' => __( 'Description', 'ceori' ),
				'type'  => Controls_Manager::WYSIWYG,
			)
		);
		$this->add_control(
			'card_title',
			array(
				'label'     => __( 'Card title', 'ceori' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => 'Specialised in',
				'separator' => 'before',
			)
		);
		$this->add_control(
			'items',
			array(
				'label' => __( 'Card list (one item per line)', 'ceori' ),
				'type'  => Controls_Manager::TEXTAREA,
				'rows'  => 6,
			)
		);
		$r = new Repeater();
		$r->add_control(
			'l_text',
			array(
				'label' => __( 'Text', 'ceori' ),
				'type'  => Controls_Manager::TEXT,
			)
		);
		$r->add_control(
			'l_link',
			array(
				'label' => __( 'Link', 'ceori' ),
				'type'  => Controls_Manager::URL,
			)
		);
		$this->add_control(
			'links',
			array(
				'label'       => __( 'Department links under the list', 'ceori' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $r->get_controls(),
				'title_field' => '{{{ l_text }}}',
				'default'     => array(),
			)
		);
		$this->end_controls_section();

		$this->section_style( '.ceori-docabout' );
		$this->heading_styles();
		$this->style_text( 'text', __( 'Description', 'ceori' ), '.ceori-docabout__text, .ceori-docabout__text p' );
		$this->style_box( 'card', __( 'Card', 'ceori' ), '.ceori-docabout__card' );
		$this->style_text( 'card_title', __( 'Card title', 'ceori' ), '.ceori-docabout__card h3' );
		$this->style_text( 'card_list', __( 'Card list', 'ceori' ), '.ceori-docabout__card li' );
		$this->style_text( 'card_links', __( 'Department links', 'ceori' ), '.ceori-docabout__links a' );
	}

	protected function render() {
		$s     = $this->get_settings_for_display();
		$links = '';
		foreach ( (array) $s['links'] as $link ) {
			if ( $link['l_text'] && ! empty( $link['l_link']['url'] ) ) {
				$links .= '<li><a href="' . esc_url( $link['l_link']['url'] ) . '">' . esc_html( $link['l_text'] ) . ceori_icon( 'arrow-up-right' ) . '</a></li>';
			}
		}
		echo '<section class="ceori-section ceori-docabout"><div class="ceori-wrap"><div class="ceori-docabout__grid">';
		echo '<div class="ceori-docabout__main reveal">' . $this->head( $s ); // phpcs:ignore WordPress.Security.EscapeOutput
		echo $s['text'] ? '<div class="ceori-docabout__text">' . wp_kses_post( $s['text'] ) . '</div>' : '';
		echo '</div><aside class="ceori-docabout__card reveal"><h3>' . esc_html( $s['card_title'] ) . '</h3>';
		echo $this->checklist( $this->lines( $s['items'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput
		echo $links ? '<ul class="ceori-docabout__links">' . $links . '</ul>' : ''; // phpcs:ignore WordPress.Security.EscapeOutput
		echo '</aside></div></div></section>';
	}
}
