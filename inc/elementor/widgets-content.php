<?php
/**
 * Page content widgets: Page hero, Text & image, Info cards, Steps, Callout, FAQ,
 * Principles, Specialist profile.
 *
 * @package ceori
 */

namespace Ceori\Elementor;

use Elementor\Controls_Manager;
use Elementor\Repeater;

defined( 'ABSPATH' ) || exit;

/**
 * Page hero / banner with breadcrumbs, title, intro, buttons and image.
 */
class Page_Hero extends Widget {

	public function get_name() {
		return 'ceori-page-hero';
	}

	public function get_title() {
		return __( 'CEORI Page Hero', 'ceori' );
	}

	public function get_icon() {
		return 'eicon-header';
	}

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => __( 'Page hero', 'ceori' ) ) );
		$this->add_control(
			'show_breadcrumbs',
			array(
				'label'        => __( 'Show breadcrumbs', 'ceori' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);
		$this->add_control(
			'eyebrow',
			array(
				'label'       => __( 'Small label above the title', 'ceori' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
			)
		);
		$this->add_control(
			'title',
			array(
				'label'       => __( 'Title (leave empty to use the page title)', 'ceori' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 2,
				'description' => __( 'This is the page’s main heading (H1). &lt;em&gt;…&lt;/em&gt; = italic accent.', 'ceori' ),
			)
		);
		$this->add_control(
			'text',
			array(
				'label' => __( 'Introduction', 'ceori' ),
				'type'  => Controls_Manager::TEXTAREA,
			)
		);
		$this->add_control(
			'image',
			array(
				'label'       => __( 'Image (leave empty to use the featured image)', 'ceori' ),
				'type'        => Controls_Manager::MEDIA,
				'description' => __( 'Remove both to show a text-only banner.', 'ceori' ),
			)
		);
		$this->add_control(
			'use_featured',
			array(
				'label'        => __( 'Use featured image when no image is chosen', 'ceori' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);
		$this->button_controls( 'button', __( 'Button text (leave empty to hide)', 'ceori' ), '', home_url( '/contact-us/#appointment' ) );
		$this->add_control(
			'show_call',
			array(
				'label'        => __( 'Show “Call us” button', 'ceori' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'return_value' => 'yes',
			)
		);
		$this->add_control(
			'side',
			array(
				'label'       => __( 'Right side (when there is no image)', 'ceori' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => '',
				'options'     => array(
					''        => __( 'Nothing', 'ceori' ),
					'contact' => __( 'Quick contact cards (call, WhatsApp, email, visit)', 'ceori' ),
				),
				'description' => __( 'Phone numbers, email and address come from CEORI Clinic settings.', 'ceori' ),
				'separator'   => 'before',
			)
		);
		$this->end_controls_section();

		$this->style_box( 'frame', __( 'Banner box', 'ceori' ), '.ceori-thero__frame', array( 'height' => true ) );
		$this->style_text( 'crumbs', __( 'Breadcrumbs', 'ceori' ), '.ceori-breadcrumbs ol, .ceori-breadcrumbs a' );
		$this->style_text( 'eyebrow', __( 'Small label', 'ceori' ), '.ceori-eyebrow' );
		$this->style_text( 'title', __( 'Title', 'ceori' ), '.ceori-thero__title' );
		$this->style_text( 'accent', __( 'Title – italic accent', 'ceori' ), '.ceori-thero__title em' );
		$this->style_text( 'text', __( 'Introduction', 'ceori' ), '.ceori-thero__lead' );
		$this->style_image( 'image', __( 'Image', 'ceori' ), '.ceori-thero__media img' );
		$this->style_button( 'button', __( 'Main button', 'ceori' ), '.has-arrow .wp-block-button__link' );
		$this->style_button( 'call', __( 'Call button', 'ceori' ), '.has-phone .wp-block-button__link' );
		$this->style_box( 'qc', __( 'Quick contact cards', 'ceori' ), '.ceori-qc__item' );
		$this->style_text( 'qc_label', __( 'Quick contact – labels', 'ceori' ), '.ceori-qc__label' );
		$this->style_text( 'qc_value', __( 'Quick contact – details', 'ceori' ), '.ceori-qc__value' );
	}

	/**
	 * Quick contact cards from CEORI Clinic settings.
	 *
	 * @return string
	 */
	protected function quick_contact() {
		$phone = ceori_option( 'phone_primary' );
		$email = ceori_option( 'email' );
		$wa    = ceori_chat_whatsapp_href( __( 'Hello CEORI, I would like to book an appointment.', 'ceori' ) );
		$map   = ceori_option( 'map_url' );
		$cards = array();
		if ( $phone ) {
			$cards[] = array( 'phone', __( 'Call us', 'ceori' ), $phone, ceori_tel_href( $phone ), false );
		}
		if ( $wa ) {
			$cards[] = array( 'whatsapp', __( 'WhatsApp', 'ceori' ), __( 'Chat with our team', 'ceori' ), $wa, true );
		}
		if ( $email ) {
			$cards[] = array( 'mail', __( 'Email', 'ceori' ), $email, 'mailto:' . $email, false );
		}
		$cards[] = array( 'pin', __( 'Visit', 'ceori' ), ceori_address_line(), $map ? $map : '#appointment', (bool) $map );
		$out     = '<ul class="ceori-qc">';
		foreach ( $cards as $c ) {
			$out .= '<li><a class="ceori-qc__item" href="' . esc_url( $c[3] ) . '"' . ( $c[4] ? ' target="_blank" rel="noopener"' : '' ) . '>'
				. '<span class="ceori-qc__icon" aria-hidden="true">' . ceori_icon( $c[0] ) . '</span>'
				. '<span class="ceori-qc__text"><span class="ceori-qc__label">' . esc_html( $c[1] ) . '</span><span class="ceori-qc__value">' . esc_html( $c[2] ) . '</span></span>'
				. '<span class="ceori-qc__go" aria-hidden="true">' . ceori_icon( 'arrow-up-right' ) . '</span></a></li>';
		}
		return $out . '</ul>';
	}

	protected function render() {
		$s     = $this->get_settings_for_display();
		$pid   = $this->page_id();
		$title = '' !== trim( (string) $s['title'] ) ? $this->inline( $s['title'] ) : esc_html( get_the_title( $pid ) );
		// The banner image fills half the frame on desktop (~490px), the full width on phones.
		$sizes = '(max-width: 1023px) 92vw, 540px';
		$image = $this->img(
			$s['image'],
			'large',
			array(
				'class'         => 'is-lcp',
				'loading'       => 'eager',
				'fetchpriority' => 'high',
				'sizes'         => $sizes,
			)
		);
		if ( ! $image && 'yes' === $s['use_featured'] && has_post_thumbnail( $pid ) ) {
			$image = get_the_post_thumbnail(
				$pid,
				'large',
				array(
					'loading'       => 'eager',
					'fetchpriority' => 'high',
					'sizes'         => $sizes,
				)
			);
		}
		$buttons = $this->button( $s['button_text'], $s['button_link'] )
			. ( 'yes' === $s['show_call'] ? $this->button( __( 'Call Us', 'ceori' ), ceori_tel_href( ceori_option( 'phone_primary' ) ), 'phone' ) : '' );

		$side = ! $image && 'contact' === $s['side'] ? $this->quick_contact() : '';
		echo '<section class="ceori-thero' . ( $image ? '' : ' is-text-only' ) . ( $side ? ' has-side' : '' ) . '"><div class="ceori-thero__frame"><div class="ceori-thero__body">';
		if ( 'yes' === $s['show_breadcrumbs'] ) {
			echo ceori_breadcrumb_html(); // phpcs:ignore WordPress.Security.EscapeOutput
		}
		if ( $s['eyebrow'] ) {
			echo '<p class="ceori-eyebrow ceori-thero__cat">' . esc_html( $s['eyebrow'] ) . '</p>';
		}
		echo '<h1 class="ceori-thero__title">' . $title . '</h1>'; // phpcs:ignore WordPress.Security.EscapeOutput
		if ( $s['text'] ) {
			echo '<p class="ceori-thero__lead">' . $this->inline( $s['text'] ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput
		}
		echo $this->buttons( $buttons, 'ceori-thero__actions' ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput
		if ( $image ) {
			echo '<figure class="ceori-thero__media">' . $image . '</figure>'; // phpcs:ignore WordPress.Security.EscapeOutput
		}
		echo $side . '</div></section>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}
}

/**
 * Text beside an image (one or two images), with optional check list and button.
 */
class Split extends Widget {

	public function get_name() {
		return 'ceori-split';
	}

	public function get_title() {
		return __( 'CEORI Text & Image', 'ceori' );
	}

	public function get_icon() {
		return 'eicon-image-box';
	}

	protected function register_controls() {
		$this->heading_controls( array( 'eyebrow' => 'About the treatment' ) );
		$this->start_controls_section( 'content', array( 'label' => __( 'Text & image', 'ceori' ) ) );
		$this->add_control(
			'text',
			array(
				'label' => __( 'Text', 'ceori' ),
				'type'  => Controls_Manager::WYSIWYG,
			)
		);
		$this->add_control(
			'list',
			array(
				'label'       => __( 'Check list (one item per line, optional)', 'ceori' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 4,
				'description' => __( 'Shown with tick marks under the text.', 'ceori' ),
			)
		);
		$this->add_control(
			'image',
			array(
				'label'     => __( 'Image', 'ceori' ),
				'type'      => Controls_Manager::MEDIA,
				'separator' => 'before',
			)
		);
		$this->add_control(
			'image2',
			array(
				'label'       => __( 'Second, smaller image (optional)', 'ceori' ),
				'type'        => Controls_Manager::MEDIA,
				'description' => __( 'Overlaps the corner of the main image.', 'ceori' ),
			)
		);
		$this->add_control(
			'image_side',
			array(
				'label'   => __( 'Image position', 'ceori' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'right',
				'options' => array(
					'right' => __( 'Right', 'ceori' ),
					'left'  => __( 'Left', 'ceori' ),
				),
			)
		);
		$this->button_controls( 'button', __( 'Button text (optional)', 'ceori' ), '', '' );
		$this->end_controls_section();

		$this->section_style( '.ceori-split' );
		$this->heading_styles();
		$this->style_text( 'text', __( 'Text', 'ceori' ), '.ceori-split__text, .ceori-split__text p' );
		$this->style_text( 'list', __( 'Check list', 'ceori' ), '.is-style-checks li' );
		$this->style_image( 'image', __( 'Main image', 'ceori' ), '.ceori-split__main img' );
		$this->style_image( 'image2', __( 'Second image', 'ceori' ), '.ceori-welcome__img--small img' );
		$this->style_button( 'button', __( 'Button', 'ceori' ), '.wp-block-button__link' );
	}

	protected function render() {
		$s       = $this->get_settings_for_display();
		$two     = ! empty( $s['image2']['url'] );
		$media   = $two
			? '<div class="wp-block-column ceori-welcome__media reveal">' . $this->figure( $s['image'], 'large', 'ceori-split__main ceori-welcome__img ceori-welcome__img--main' ) . $this->figure( $s['image2'], 'ceori-card', 'ceori-welcome__img ceori-welcome__img--small' ) . '</div>'
			: '<div class="wp-block-column reveal">' . $this->figure( $s['image'], 'large', 'ceori-split__main ceori-tb-about__img' ) . '</div>';
		$text    = '<div class="wp-block-column reveal" style="flex-basis:55%">' . $this->head( $s )
			. ( $s['text'] ? '<div class="ceori-split__text">' . wp_kses_post( $s['text'] ) . '</div>' : '' )
			. $this->checklist( $this->lines( $s['list'] ) )
			. $this->buttons( $this->button( $s['button_text'], $s['button_link'] ) ) . '</div>';
		$columns = 'left' === $s['image_side'] ? $media . $text : $text . $media;
		echo '<section class="ceori-section ceori-split ceori-tb-about' . ( $two ? ' ceori-welcome' : '' ) . '"><div class="ceori-wrap"><div class="wp-block-columns is-layout-flex are-vertically-aligned-center ceori-tb-about__cols' . ( $two ? ' ceori-welcome__cols' : '' ) . '">' . $columns . '</div></div></section>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}
}

/**
 * Two or more cards with a title, intro and check list (e.g. Who may benefit / Benefits).
 */
class Info_Cards extends Widget {

	public function get_name() {
		return 'ceori-info-cards';
	}

	public function get_title() {
		return __( 'CEORI Info Cards', 'ceori' );
	}

	public function get_icon() {
		return 'eicon-columns';
	}

	protected function register_controls() {
		$this->heading_controls();
		$this->start_controls_section( 'content', array( 'label' => __( 'Cards', 'ceori' ) ) );
		$r = new Repeater();
		$r->add_control(
			'card_title',
			array(
				'label' => __( 'Title', 'ceori' ),
				'type'  => Controls_Manager::TEXT,
			)
		);
		$r->add_control(
			'card_intro',
			array(
				'label' => __( 'Introduction', 'ceori' ),
				'type'  => Controls_Manager::TEXTAREA,
				'rows'  => 2,
			)
		);
		$r->add_control(
			'card_items',
			array(
				'label' => __( 'List (one item per line)', 'ceori' ),
				'type'  => Controls_Manager::TEXTAREA,
				'rows'  => 5,
			)
		);
		$r->add_control(
			'card_accent',
			array(
				'label'        => __( 'Blush background', 'ceori' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
			)
		);
		$this->add_control(
			'cards',
			array(
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $r->get_controls(),
				'title_field' => '{{{ card_title }}}',
				'default'     => array(
					array(
						'card_title' => 'Who can it <em>help?</em>',
						'card_items' => "Concern one\nConcern two",
					),
					array(
						'card_title'  => 'Benefits',
						'card_items'  => "Benefit one\nBenefit two",
						'card_accent' => 'yes',
					),
				),
			)
		);
		$this->end_controls_section();

		$this->section_style( '.ceori-tb-fit' );
		$this->heading_styles();
		$this->style_box( 'card', __( 'Cards', 'ceori' ), '.ceori-tb-card' );
		$this->style_text( 'card_title', __( 'Card titles', 'ceori' ), '.ceori-tb-card h3' );
		$this->style_text( 'card_intro', __( 'Card introductions', 'ceori' ), '.ceori-tb-card > p' );
		$this->style_text( 'card_list', __( 'Lists', 'ceori' ), '.ceori-tb-card li' );
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		echo '<section class="ceori-section ceori-tb-fit"><div class="ceori-wrap">' . $this->head( $s ) . '<div class="wp-block-columns is-layout-flex ceori-tb-fit__cols">'; // phpcs:ignore WordPress.Security.EscapeOutput
		foreach ( (array) $s['cards'] as $card ) {
			echo '<div class="wp-block-column ceori-tb-card' . ( 'yes' === $card['card_accent'] ? ' ceori-tb-card--accent' : '' ) . ' reveal">';
			echo '<h3>' . $this->inline( $card['card_title'] ) . '</h3>'; // phpcs:ignore WordPress.Security.EscapeOutput
			echo $card['card_intro'] ? '<p>' . $this->inline( $card['card_intro'] ) . '</p>' : ''; // phpcs:ignore WordPress.Security.EscapeOutput
			echo $this->checklist( $this->lines( $card['card_items'] ) ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput
		}
		echo '</div></div></section>';
	}
}

/**
 * Numbered steps (How it works / Consultation process), optional opening hours card.
 */
class Steps extends Widget {

	public function get_name() {
		return 'ceori-steps';
	}

	public function get_title() {
		return __( 'CEORI Steps', 'ceori' );
	}

	public function get_icon() {
		return 'eicon-number-field';
	}

	protected function register_controls() {
		$this->heading_controls(
			array(
				'eyebrow' => 'Step by step',
				'title'   => 'How the treatment <em>works</em>',
			)
		);
		$this->start_controls_section( 'content', array( 'label' => __( 'Steps', 'ceori' ) ) );
		$r = new Repeater();
		$r->add_control(
			'step_title',
			array(
				'label' => __( 'Title', 'ceori' ),
				'type'  => Controls_Manager::TEXT,
			)
		);
		$r->add_control(
			'step_text',
			array(
				'label' => __( 'Text', 'ceori' ),
				'type'  => Controls_Manager::TEXTAREA,
			)
		);
		$this->add_control(
			'steps',
			array(
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $r->get_controls(),
				'title_field' => '{{{ step_title }}}',
				'default'     => array(
					array( 'step_title' => 'Consultation' ),
					array( 'step_title' => 'Treatment' ),
					array( 'step_title' => 'Follow-up' ),
				),
			)
		);
		$this->add_control(
			'variant',
			array(
				'label'   => __( 'Style', 'ceori' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'cream',
				'options' => array(
					'cream'    => __( 'Cream cards', 'ceori' ),
					'outlined' => __( 'White cards on grey band', 'ceori' ),
				),
			)
		);
		$this->add_control(
			'show_hours',
			array(
				'label'        => __( 'Show opening hours card', 'ceori' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
			)
		);
		$this->end_controls_section();

		$this->section_style( '.ceori-steps-section' );
		$this->heading_styles();
		$this->style_box( 'card', __( 'Step cards', 'ceori' ), '.ceori-step' );
		$this->style_text( 'num', __( 'Step numbers', 'ceori' ), '.ceori-step__num' );
		$this->style_text( 'step_title', __( 'Step titles', 'ceori' ), '.ceori-step h3' );
		$this->style_text( 'step_text', __( 'Step text', 'ceori' ), '.ceori-step p:not(.ceori-step__num)' );
		$this->style_box( 'hours', __( 'Opening hours card', 'ceori' ), '.ceori-hours-card' );
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		$c = 'outlined' === $s['variant'] ? 'ceori-process' : 'ceori-tb-steps';
		echo '<section class="ceori-section ceori-steps-section ' . esc_attr( $c ) . '"><div class="ceori-wrap">' . $this->head( $s ) . '<div class="wp-block-columns is-layout-flex ceori-steps">'; // phpcs:ignore WordPress.Security.EscapeOutput
		foreach ( array_values( (array) $s['steps'] ) as $i => $step ) {
			echo '<div class="wp-block-column ceori-step reveal"><p class="ceori-step__num">' . esc_html( sprintf( '%02d', $i + 1 ) ) . '</p><h3>' . esc_html( $step['step_title'] ) . '</h3><p>' . $this->inline( $step['step_text'] ) . '</p></div>'; // phpcs:ignore WordPress.Security.EscapeOutput
		}
		echo '</div>';
		if ( 'yes' === $s['show_hours'] ) {
			echo '<div class="ceori-hours-card is-layout-flex reveal"><div><h3>' . esc_html__( 'Opening hours', 'ceori' ) . '</h3><p class="ceori-muted">' . esc_html__( 'Our clinic is here for you during the following hours.', 'ceori' ) . '</p></div>' . ceori_render_clinic_info( array( 'field' => 'hours' ) ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput
		}
		echo '</div></section>';
	}
}

/**
 * Highlighted text box (What to expect) with a note.
 */
class Callout extends Widget {

	public function get_name() {
		return 'ceori-callout';
	}

	public function get_title() {
		return __( 'CEORI Callout Box', 'ceori' );
	}

	public function get_icon() {
		return 'eicon-alert';
	}

	protected function register_controls() {
		$this->heading_controls(
			array(
				'eyebrow' => 'Before &amp; after',
				'title'   => 'What to <em>expect</em>',
			)
		);
		$this->start_controls_section( 'content', array( 'label' => __( 'Text', 'ceori' ) ) );
		$this->add_control(
			'text',
			array(
				'label' => __( 'Text', 'ceori' ),
				'type'  => Controls_Manager::WYSIWYG,
			)
		);
		$this->add_control(
			'note',
			array(
				'label'   => __( 'Highlighted note', 'ceori' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => 'Whether a treatment suits you, how many sessions you need and your results all depend on a check by your CEORI specialist. Results differ from person to person.',
			)
		);
		$this->end_controls_section();

		$this->style_box( 'box', __( 'Box', 'ceori' ), '.ceori-tb-expect' );
		$this->heading_styles();
		$this->style_text( 'text', __( 'Text', 'ceori' ), '.ceori-callout__text, .ceori-callout__text p' );
		$this->style_box( 'note_box', __( 'Note box', 'ceori' ), '.ceori-note' );
		$this->style_text( 'note_text', __( 'Note text', 'ceori' ), '.ceori-note' );
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		echo '<section class="ceori-section ceori-section--tight ceori-callout"><div class="ceori-wrap"><div class="ceori-tb-expect reveal">' . $this->head( $s ); // phpcs:ignore WordPress.Security.EscapeOutput
		echo $s['text'] ? '<div class="ceori-callout__text">' . wp_kses_post( $s['text'] ) . '</div>' : '';
		echo $s['note'] ? '<p class="ceori-note">' . esc_html( $s['note'] ) . '</p>' : '';
		echo '</div></div></section>';
	}
}

/**
 * FAQ accordion.
 */
class Faq extends Widget {

	public function get_name() {
		return 'ceori-faq';
	}

	public function get_title() {
		return __( 'CEORI FAQ', 'ceori' );
	}

	public function get_icon() {
		return 'eicon-help-o';
	}

	protected function register_controls() {
		$this->heading_controls(
			array(
				'eyebrow' => 'FAQ',
				'title'   => 'Common <em>questions</em>',
			)
		);
		$this->start_controls_section( 'content', array( 'label' => __( 'Questions', 'ceori' ) ) );
		$r = new Repeater();
		$r->add_control(
			'question',
			array(
				'label'       => __( 'Question', 'ceori' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
			)
		);
		$r->add_control(
			'answer',
			array(
				'label' => __( 'Answer', 'ceori' ),
				'type'  => Controls_Manager::TEXTAREA,
			)
		);
		$this->add_control(
			'items',
			array(
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $r->get_controls(),
				'title_field' => '{{{ question }}}',
				'default'     => array(
					array(
						'question' => 'A common question?',
						'answer'   => 'A clear, accurate answer reviewed by a specialist.',
					),
				),
			)
		);
		$this->add_control(
			'first_open',
			array(
				'label'        => __( 'Open the first question', 'ceori' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
			)
		);
		$this->button_controls( 'button', __( 'Button text (optional)', 'ceori' ), '', home_url( '/contact-us/' ) );
		$this->end_controls_section();

		$this->section_style( '.ceori-faq' );
		$this->heading_styles();
		$this->style_text( 'question', __( 'Questions', 'ceori' ), '.wp-block-details summary' );
		$this->style_text( 'answer', __( 'Answers', 'ceori' ), '.wp-block-details p' );
		$this->start_controls_section(
			'divider_style',
			array(
				'label' => __( 'Dividers', 'ceori' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_control(
			'divider_color',
			array(
				'label'     => __( 'Divider colour', 'ceori' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .wp-block-details' => 'border-bottom-color: {{VALUE}};' ),
			)
		);
		$this->end_controls_section();
		$this->style_button( 'button', __( 'Button', 'ceori' ), '.wp-block-button__link' );
	}

	protected function render() {
		$s    = $this->get_settings_for_display();
		$list = '';
		foreach ( array_values( (array) $s['items'] ) as $i => $item ) {
			$list .= '<details class="wp-block-details is-style-faq"' . ( 0 === $i && 'yes' === $s['first_open'] ? ' open' : '' ) . '><summary>' . esc_html( $item['question'] ) . '</summary><p>' . $this->inline( $item['answer'] ) . '</p></details>';
		}
		echo '<section class="ceori-section ceori-faq"><div class="ceori-wrap"><div class="wp-block-columns is-layout-flex ceori-faq__cols"><div class="wp-block-column ceori-faq__intro" style="flex-basis:36%">'; // phpcs:ignore WordPress.Security.EscapeOutput
		echo $this->head( $s ) . $this->buttons( $this->button( $s['button_text'], $s['button_link'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput
		echo '</div><div class="wp-block-column ceori-faq__list">' . $list . '</div></div></div></section>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}
}

/**
 * Mission statement with principles.
 */
class Principles extends Widget {

	public function get_name() {
		return 'ceori-principles';
	}

	public function get_title() {
		return __( 'CEORI Mission & Principles', 'ceori' );
	}

	public function get_icon() {
		return 'eicon-bullet-list';
	}

	protected function register_controls() {
		$this->heading_controls(
			array(
				'eyebrow' => 'Our mission',
				'title'   => 'Helping You Feel <em>Confident</em> in Your Skin',
				'lead'    => 'Our aim is simple: safe care that works, respects your natural look and fits your goals.',
			)
		);
		$this->start_controls_section( 'content', array( 'label' => __( 'Principles', 'ceori' ) ) );
		$r = new Repeater();
		$r->add_control(
			'p_title',
			array(
				'label' => __( 'Title', 'ceori' ),
				'type'  => Controls_Manager::TEXT,
			)
		);
		$r->add_control(
			'p_text',
			array(
				'label' => __( 'Text', 'ceori' ),
				'type'  => Controls_Manager::TEXTAREA,
			)
		);
		$this->add_control(
			'items',
			array(
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $r->get_controls(),
				'title_field' => '{{{ p_title }}}',
				'default'     => array(
					array(
						'p_title' => 'Listen first',
						'p_text'  => 'Every visit starts with your concerns and questions. We take time to understand your skin, your health and what matters to you.',
					),
					array(
						'p_title' => 'Honest, clear advice',
						'p_text'  => 'We explain what a treatment can and cannot do, how many sessions it may take and what recovery involves – before you decide.',
					),
					array(
						'p_title' => 'Care that continues',
						'p_text'  => 'Good results need follow-up. We check your progress and adjust your plan, so you always know what comes next.',
					),
				),
			)
		);
		$this->end_controls_section();

		$this->section_style( '.ceori-approach' );
		$this->heading_styles();
		$this->style_text( 'p_title', __( 'Principle titles', 'ceori' ), '.ceori-principle h3' );
		$this->style_text( 'p_text', __( 'Principle text', 'ceori' ), '.ceori-principle p' );
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		echo '<section class="ceori-section ceori-approach"><div class="ceori-wrap"><div class="wp-block-columns is-layout-flex ceori-approach__cols"><div class="wp-block-column" style="flex-basis:40%">' . $this->head( $s ) . '</div><div class="wp-block-column">'; // phpcs:ignore WordPress.Security.EscapeOutput
		foreach ( (array) $s['items'] as $item ) {
			echo '<div class="ceori-principle reveal"><h3>' . esc_html( $item['p_title'] ) . '</h3><p>' . $this->inline( $item['p_text'] ) . '</p></div>'; // phpcs:ignore WordPress.Security.EscapeOutput
		}
		echo '</div></div></div></section>';
	}
}

/**
 * Specialist profile.
 */
class Profile extends Widget {

	public function get_name() {
		return 'ceori-profile';
	}

	public function get_title() {
		return __( 'CEORI Specialist Profile', 'ceori' );
	}

	public function get_icon() {
		return 'eicon-person';
	}

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => __( 'Profile', 'ceori' ) ) );
		$this->add_control(
			'photo',
			array(
				'label' => __( 'Photo', 'ceori' ),
				'type'  => Controls_Manager::MEDIA,
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
			'name',
			array(
				'label' => __( 'Name', 'ceori' ),
				'type'  => Controls_Manager::TEXT,
			)
		);
		$this->add_control(
			'qualifications',
			array(
				'label'       => __( 'Qualifications (verified only)', 'ceori' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
			)
		);
		$this->add_control(
			'bio',
			array(
				'label' => __( 'Biography', 'ceori' ),
				'type'  => Controls_Manager::WYSIWYG,
			)
		);
		$this->add_control(
			'focus_title',
			array(
				'label'   => __( 'List heading', 'ceori' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Areas of care',
			)
		);
		$this->add_control(
			'focus',
			array(
				'label' => __( 'List (one item per line)', 'ceori' ),
				'type'  => Controls_Manager::TEXTAREA,
				'rows'  => 5,
			)
		);
		$this->button_controls( 'button', __( 'Main button text', 'ceori' ), 'Book a Consultation', home_url( '/contact-us/#appointment' ) );
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
				'label'       => __( 'More buttons', 'ceori' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $r->get_controls(),
				'title_field' => '{{{ l_text }}}',
				'default'     => array(),
			)
		);
		$this->end_controls_section();

		$this->section_style( '.ceori-profile' );
		$this->style_image( 'photo', __( 'Photo', 'ceori' ), '.ceori-profile__img img' );
		$this->style_text( 'role', __( 'Designation', 'ceori' ), '.ceori-profile__role' );
		$this->style_text( 'name', __( 'Name', 'ceori' ), '.ceori-profile__name' );
		$this->style_text( 'quals', __( 'Qualifications', 'ceori' ), '.ceori-profile__quals' );
		$this->style_text( 'bio', __( 'Biography', 'ceori' ), '.ceori-profile__bio, .ceori-profile__bio p' );
		$this->style_text( 'list', __( 'List', 'ceori' ), '.is-style-checks li' );
		$this->style_button( 'button', __( 'Main button', 'ceori' ), '.has-arrow .wp-block-button__link' );
		$this->style_button( 'links', __( 'More buttons', 'ceori' ), '.is-style-outline-ceori .wp-block-button__link' );
	}

	protected function render() {
		$s       = $this->get_settings_for_display();
		$buttons = $this->button( $s['button_text'], $s['button_link'] );
		foreach ( (array) $s['links'] as $link ) {
			$buttons .= $this->button( $link['l_text'], $link['l_link'], 'outline' );
		}
		echo '<section class="ceori-section ceori-profile"><div class="ceori-wrap"><div class="wp-block-columns is-layout-flex are-vertically-aligned-center ceori-profile__cols">';
		echo '<div class="wp-block-column" style="flex-basis:40%">' . $this->figure( $s['photo'], 'large', 'ceori-profile__img', array( 'alt' => trim( $s['name'] . ', ' . $s['role'], ', ' ) ) ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput
		echo '<div class="wp-block-column"><p class="ceori-profile__role">' . esc_html( $s['role'] ) . '</p><h2 class="ceori-profile__name">' . esc_html( $s['name'] ) . '</h2>';
		echo $s['qualifications'] ? '<p class="ceori-profile__quals">' . esc_html( $s['qualifications'] ) . '</p>' : '';
		echo $s['bio'] ? '<div class="ceori-profile__bio">' . wp_kses_post( $s['bio'] ) . '</div>' : '';
		$items = $this->lines( $s['focus'] );
		if ( $items ) {
			echo '<h3>' . esc_html( $s['focus_title'] ) . '</h3>' . $this->checklist( $items ); // phpcs:ignore WordPress.Security.EscapeOutput
		}
		echo $this->buttons( $buttons ) . '</div></div></div></section>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}
}

/**
 * Video library: YouTube videos and Instagram reels as cards. Nothing loads from YouTube or
 * Instagram until a visitor presses play (the video then opens in a lightbox).
 */
class Videos extends Widget {

	public function get_name() {
		return 'ceori-videos';
	}

	public function get_title() {
		return __( 'CEORI Videos', 'ceori' );
	}

	public function get_icon() {
		return 'eicon-youtube';
	}

	/**
	 * Provider and ID of a YouTube or Instagram link.
	 *
	 * @param string $url Link.
	 * @return array|null [ 'youtube'|'instagram', id ]
	 */
	public static function parse( $url ) {
		if ( preg_match( '#(?:youtube(?:-nocookie)?\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/|live/)|youtu\.be/)([A-Za-z0-9_-]{6,20})#', (string) $url, $m ) ) {
			return array( 'youtube', $m[1] );
		}
		if ( preg_match( '#instagram\.com/(?:[A-Za-z0-9_.]+/)?(?:reel|reels|p|tv)/([A-Za-z0-9_-]{5,40})#', (string) $url, $m ) ) {
			return array( 'instagram', $m[1] );
		}
		return null;
	}

	protected function register_controls() {
		$this->heading_controls(
			array(
				'eyebrow' => 'Videos',
				'title'   => 'Experiences Worth <em>Sharing</em>',
				'lead'    => 'Dermatologists and specialists answer your questions on skin, hair and confidence – one conversation at a time.',
			)
		);
		$this->start_controls_section( 'content', array( 'label' => __( 'Videos', 'ceori' ) ) );
		$v = new Repeater();
		$v->add_control(
			'video_url',
			array(
				'label'       => __( 'YouTube or Instagram link', 'ceori' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'placeholder' => 'https://www.youtube.com/watch?v=… or https://www.instagram.com/reel/…',
			)
		);
		$v->add_control(
			'video_title',
			array(
				'label'       => __( 'Title', 'ceori' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
			)
		);
		$v->add_control(
			'video_text',
			array(
				'label' => __( 'Short description', 'ceori' ),
				'type'  => Controls_Manager::TEXTAREA,
				'rows'  => 3,
			)
		);
		$v->add_control(
			'video_meta',
			array(
				'label'       => __( 'Small label (e.g. Dr. Nimitha · Malayalam)', 'ceori' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
			)
		);
		$v->add_control(
			'video_thumb',
			array(
				'label'       => __( 'Cover image', 'ceori' ),
				'type'        => Controls_Manager::MEDIA,
				'description' => __( 'A tall image works best. Empty: YouTube’s own thumbnail; Instagram reels show a CEORI cover.', 'ceori' ),
			)
		);
		$this->add_control(
			'videos',
			array(
				'label'         => __( 'Videos', 'ceori' ),
				'type'          => Controls_Manager::REPEATER,
				'fields'        => $v->get_controls(),
				'default'       => array(),
				'prevent_empty' => false,
				'title_field'   => '{{{ video_title || video_url }}}',
			)
		);
		$this->add_control(
			'columns',
			array(
				'label'     => __( 'Columns', 'ceori' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '4',
				'options'   => array(
					'2' => '2',
					'3' => '3',
					'4' => '4',
				),
				'separator' => 'before',
			)
		);
		$this->button_controls( 'button', __( 'Button below the videos (leave empty to hide)', 'ceori' ), 'More on Instagram', 'https://www.instagram.com/ceori_skinclinic/' );
		$this->end_controls_section();

		$this->section_style( '.ceori-videos' );
		$this->heading_styles();
		$this->style_box( 'card', __( 'Cards', 'ceori' ), '.ceori-vcard' );
		$this->style_image( 'thumb', __( 'Cover images', 'ceori' ), '.ceori-vcard__media img' );
		$this->style_text( 'meta', __( 'Small labels', 'ceori' ), '.ceori-vcard__meta' );
		$this->style_text( 'vtitle', __( 'Titles', 'ceori' ), '.ceori-vcard__title' );
		$this->style_text( 'vtext', __( 'Descriptions', 'ceori' ), '.ceori-vcard__text' );
		$this->style_button( 'button', __( 'Button', 'ceori' ), '.ceori-videos__more .wp-block-button__link' );
	}

	protected function render() {
		$s     = $this->get_settings_for_display();
		$cards = '';
		foreach ( (array) $s['videos'] as $v ) {
			$p = self::parse( $v['video_url'] );
			if ( ! $p ) {
				continue;
			}
			list( $provider, $id ) = $p;
			$title = trim( (string) $v['video_title'] );
			$title = '' !== $title ? $title : ( 'youtube' === $provider ? __( 'Watch on YouTube', 'ceori' ) : __( 'Watch on Instagram', 'ceori' ) );
			$url   = 'youtube' === $provider ? 'https://www.youtube.com/watch?v=' . $id : 'https://www.instagram.com/reel/' . $id . '/';
			$thumb = $this->img( $v['video_thumb'], 'large', array( 'alt' => '', 'sizes' => '(max-width: 599px) 46vw, (max-width: 1023px) 30vw, 300px' ) );
			if ( ! $thumb && 'youtube' === $provider ) {
				$thumb = '<img src="' . esc_url( 'https://i.ytimg.com/vi/' . $id . '/hqdefault.jpg' ) . '" alt="" loading="lazy" width="480" height="360">';
			}
			$thumb  = $thumb ? $thumb : '<span class="ceori-vcard__ph" aria-hidden="true">' . ceori_icon( 'instagram' ) . '</span>';
			$badge  = 'youtube' === $provider ? 'YouTube' : 'Instagram';
			$cards .= '<li class="ceori-vcard reveal elementor-repeater-item-' . esc_attr( $v['_id'] ) . '">';
			$cards .= '<a class="ceori-vcard__media" href="' . esc_url( $url ) . '" data-video="' . esc_attr( $id ) . '" data-provider="' . esc_attr( $provider ) . '" data-title="' . esc_attr( $title ) . '">' . $thumb
				. '<span class="ceori-vcard__badge">' . esc_html( $badge ) . '</span><span class="ceori-vcard__play" aria-hidden="true"></span>'
				/* translators: %s: video title */
				. '<span class="screen-reader-text">' . esc_html( sprintf( __( 'Play video: %s', 'ceori' ), $title ) ) . '</span></a>';
			$cards .= '<div class="ceori-vcard__body">' . ( $v['video_meta'] ? '<p class="ceori-vcard__meta">' . esc_html( $v['video_meta'] ) . '</p>' : '' );
			$cards .= '<h3 class="ceori-vcard__title">' . esc_html( $title ) . '</h3>';
			$cards .= $v['video_text'] ? '<p class="ceori-vcard__text">' . esc_html( $v['video_text'] ) . '</p>' : '';
			$cards .= '</div></li>';
		}
		echo '<section class="ceori-section ceori-videos"><div class="ceori-wrap">' . $this->head( $s ); // phpcs:ignore WordPress.Security.EscapeOutput
		if ( $cards ) {
			echo '<ul class="ceori-grid ceori-grid--' . (int) $s['columns'] . ' ceori-videos__grid">' . $cards . '</ul>'; // phpcs:ignore WordPress.Security.EscapeOutput
		} else {
			$this->editor_notice( __( 'Add YouTube or Instagram links under Videos.', 'ceori' ) );
		}
		echo $this->buttons( $this->button( $s['button_text'], $s['button_link'], 'outline' ), 'ceori-videos__more' ); // phpcs:ignore WordPress.Security.EscapeOutput
		echo '</div></section>';
	}
}
