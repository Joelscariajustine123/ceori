<?php
/**
 * Homepage section widgets: Hero, Introduction, About, Why choose us, Treatment showcase,
 * Specialities strip.
 *
 * @package ceori
 */

namespace Ceori\Elementor;

use Elementor\Controls_Manager;
use Elementor\Repeater;

defined( 'ABSPATH' ) || exit;

/**
 * Hero with portrait, headline, specialist avatars and featured treatment card.
 */
class Hero extends Widget {

	public function get_name() {
		return 'ceori-hero';
	}

	public function get_title() {
		return __( 'CEORI Hero', 'ceori' );
	}

	public function get_icon() {
		return 'eicon-banner';
	}

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => __( 'Hero', 'ceori' ) ) );
		$this->add_control(
			'title',
			array(
				'label'       => __( 'Headline', 'ceori' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 2,
				'default'     => 'See Your <br><em>Real</em> Skin.',
				'description' => __( 'Wrap words in &lt;em&gt;…&lt;/em&gt; for the italic accent.', 'ceori' ),
			)
		);
		$this->add_control(
			'title_tag',
			array(
				'label'   => __( 'Headline tag', 'ceori' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h1',
				'options' => array(
					'h1' => 'H1',
					'h2' => 'H2',
				),
			)
		);
		$this->add_control(
			'portrait',
			array(
				'label'   => __( 'Portrait (transparent PNG/WebP works best)', 'ceori' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array( 'url' => ceori_media( 'hero-portrait' )['url'] ),
			)
		);
		$this->add_control(
			'show_avatars',
			array(
				'label'        => __( 'Show specialist photos', 'ceori' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'separator'    => 'before',
			)
		);
		$this->add_control(
			'avatar_caption',
			array(
				'label'     => __( 'Text beside the photos', 'ceori' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => 'Specialists Who Care',
				'condition' => array( 'show_avatars' => 'yes' ),
			)
		);
		$this->end_controls_section();

		$this->start_controls_section( 'card', array( 'label' => __( 'Featured treatment card', 'ceori' ) ) );
		$this->add_control(
			'show_card',
			array(
				'label'        => __( 'Show card', 'ceori' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);
		$this->add_control(
			'card_image',
			array(
				'label'   => __( 'Image', 'ceori' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array( 'url' => ceori_media( 'hero-card' )['url'] ),
			)
		);
		$this->add_control(
			'card_title',
			array(
				'label'   => __( 'Title', 'ceori' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Hydra Facial',
			)
		);
		$this->add_control(
			'card_text',
			array(
				'label'   => __( 'Text', 'ceori' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => 'A gentle facial that cleans, smooths and hydrates your skin.',
			)
		);
		$this->button_controls( 'card_button', __( 'Button text', 'ceori' ), 'Book an Appointment', home_url( '/contact-us/#appointment' ) );
		$this->end_controls_section();

		$this->style_box( 'frame', __( 'Hero frame', 'ceori' ), '.ceori-hero__frame', array( 'height' => true ) );
		$this->style_text( 'title', __( 'Headline', 'ceori' ), '.ceori-hero__title' );
		$this->style_text( 'accent', __( 'Headline – italic accent', 'ceori' ), '.ceori-hero__title em' );
		$this->style_text( 'avatars', __( 'Specialist count & text', 'ceori' ), '.ceori-avatars__text, .ceori-avatars__text strong' );
		$this->style_image( 'portrait', __( 'Portrait', 'ceori' ), '.ceori-hero__portrait img' );
		$this->style_box( 'card_box', __( 'Card boxes', 'ceori' ), '.ceori-hero__card-frame, .ceori-hero__card-body' );
		$this->style_image( 'card_img', __( 'Card image', 'ceori' ), '.ceori-hero__card-media img' );
		$this->style_text( 'card_title', __( 'Card title', 'ceori' ), '.ceori-hero__card-title' );
		$this->style_text( 'card_text', __( 'Card text', 'ceori' ), '.ceori-hero__card-text' );
		$this->style_button( 'button', __( 'Card button', 'ceori' ), '.wp-block-button__link' );
	}

	protected function render() {
		$s   = $this->get_settings_for_display();
		$tag = 'h2' === $s['title_tag'] ? 'h2' : 'h1';
		echo '<section class="ceori-hero"><div class="ceori-hero__frame">';
		echo $this->figure( $s['portrait'], 'full', 'ceori-hero__portrait is-lcp', array( 'loading' => 'eager', 'fetchpriority' => 'high' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
		echo '<div class="ceori-hero__content"><' . $tag . ' class="ceori-hero__title">' . $this->inline( $s['title'] ) . '</' . $tag . '>'; // phpcs:ignore WordPress.Security.EscapeOutput
		if ( 'yes' === $s['show_avatars'] ) {
			echo ceori_render_doctors( // phpcs:ignore WordPress.Security.EscapeOutput
				array(
					'layout'   => 'avatars',
					'limit'    => 3,
					'caption'  => $s['avatar_caption'],
					'category' => '',
				)
			);
		}
		echo '</div>';
		if ( 'yes' === $s['show_card'] ) {
			echo '<div class="ceori-hero__card"><div class="ceori-hero__card-frame">' . $this->figure( $s['card_image'], 'large', 'ceori-hero__card-media', array( 'loading' => 'eager', 'sizes' => '(max-width: 599px) 88vw, 360px' ) ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput
			echo '<div class="ceori-hero__card-body"><h2 class="ceori-hero__card-title">' . esc_html( $s['card_title'] ) . '</h2>';
			echo '<p class="ceori-hero__card-text">' . $this->inline( $s['card_text'] ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput
			echo $this->buttons( $this->button( $s['card_button_text'], $s['card_button_link'] ) ) . '</div></div>'; // phpcs:ignore WordPress.Security.EscapeOutput
		}
		echo '</div></section>';
	}
}

/**
 * Centred quote with two floating images.
 */
class Intro extends Widget {

	public function get_name() {
		return 'ceori-intro';
	}

	public function get_title() {
		return __( 'CEORI Introduction Quote', 'ceori' );
	}

	public function get_icon() {
		return 'eicon-blockquote';
	}

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => __( 'Introduction', 'ceori' ) ) );
		$this->add_control(
			'quote',
			array(
				'label'       => __( 'Quote', 'ceori' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 5,
				'default'     => 'At CEORI, skin doctors and cosmetic specialists work side by side. We listen first, explain your options in plain words and plan your treatment around you – <strong>so you can feel good in your skin.</strong>',
				'description' => __( 'Use &lt;strong&gt;…&lt;/strong&gt; for bold words.', 'ceori' ),
			)
		);
		$this->add_control(
			'name',
			array(
				'label'   => __( 'Name', 'ceori' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'CEORI Skin Clinic',
			)
		);
		$this->add_control(
			'place',
			array(
				'label'   => __( 'Second line', 'ceori' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Eranhipalam, Calicut',
			)
		);
		$this->add_control(
			'image_left',
			array(
				'label'   => __( 'Left image', 'ceori' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array( 'url' => ceori_media( 'intro-left' )['url'] ),
			)
		);
		$this->add_control(
			'image_right',
			array(
				'label'   => __( 'Right image', 'ceori' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array( 'url' => ceori_media( 'intro-right' )['url'] ),
			)
		);
		$this->add_control(
			'sticky',
			array(
				'label'        => __( 'Keep quote in view while images scroll (desktop)', 'ceori' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);
		$this->end_controls_section();

		$this->section_style( '.ceori-intro' );
		$this->style_text( 'quote', __( 'Quote', 'ceori' ), '.ceori-intro__quote', array( 'align' => true ) );
		$this->style_text( 'quote_bold', __( 'Quote – bold words', 'ceori' ), '.ceori-intro__quote strong' );
		$this->style_text( 'byline', __( 'Name line', 'ceori' ), '.ceori-intro__byline, .ceori-intro__byline strong' );
		$this->style_image( 'images', __( 'Images', 'ceori' ), '.ceori-intro__img img' );
	}

	protected function render() {
		$s   = $this->get_settings_for_display();
		$raw = (array) $this->get_data( 'settings' );
		// Layouts saved before the switch existed have no value: use the default (on).
		$sticky = array_key_exists( 'sticky', $raw ) ? 'yes' === $s['sticky'] : true;
		echo '<section class="ceori-section ceori-intro' . ( $sticky ? ' is-sticky' : '' ) . '">';
		echo $this->figure( $s['image_left'], 'ceori-portrait', 'ceori-intro__img ceori-intro__img--left reveal', array( 'alt' => '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
		echo '<div class="ceori-intro__body reveal"><p class="ceori-intro__quote">' . $this->inline( $s['quote'] ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput
		echo '<p class="ceori-intro__byline"><strong>' . esc_html( $s['name'] ) . '</strong><span class="ceori-intro__rule" aria-hidden="true"></span>' . esc_html( $s['place'] ) . '</p></div>';
		echo $this->figure( $s['image_right'], 'ceori-portrait', 'ceori-intro__img ceori-intro__img--right reveal', array( 'alt' => '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
		echo '</section>';
	}
}

/**
 * About: image with optional video button beside an accordion card.
 */
class About extends Widget {

	public function get_name() {
		return 'ceori-about';
	}

	public function get_title() {
		return __( 'CEORI About (image + accordion)', 'ceori' );
	}

	public function get_icon() {
		return 'eicon-accordion';
	}

	protected function register_controls() {
		$this->heading_controls(
			array(
				'eyebrow' => 'About us',
				'title'   => 'Where <em>Science</em> Meets <em>Skin</em>',
				'lead'    => 'CEORI is a skin clinic in Calicut where experienced doctors and modern treatments come together for your skin, hair and smile.',
			)
		);
		$this->start_controls_section( 'content', array( 'label' => __( 'Image & video', 'ceori' ) ) );
		$this->add_control(
			'image',
			array(
				'label'   => __( 'Image', 'ceori' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array( 'url' => ceori_media( 'about-main' )['url'] ),
			)
		);
		$this->add_control(
			'video_id',
			array(
				'label'       => __( 'YouTube video ID (optional)', 'ceori' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'Lp7sEgC2mt8',
				'description' => __( 'The part after "v=" in a YouTube link. Leave empty to hide the play button.', 'ceori' ),
			)
		);
		$this->add_control(
			'video_label',
			array(
				'label'   => __( 'Video description (for screen readers)', 'ceori' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Watch a CEORI specialist explain PRP treatment',
			)
		);
		$this->end_controls_section();

		$this->start_controls_section( 'items_section', array( 'label' => __( 'Accordion', 'ceori' ) ) );
		$r = new Repeater();
		$r->add_control(
			'item_title',
			array(
				'label'       => __( 'Title', 'ceori' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
			)
		);
		$r->add_control(
			'item_text',
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
				'title_field' => '{{{ item_title }}}',
				'default'     => array(
					array(
						'item_title' => 'Experienced Specialists',
						'item_text'  => 'Skin specialists, a plastic surgeon, a cosmetic gynaecologist and an oral medicine specialist – all in one clinic.',
					),
					array(
						'item_title' => 'A Plan Made for You',
						'item_text'  => 'We listen to you and check your skin first, so your treatment fits your concerns, your routine and your goals.',
					),
					array(
						'item_title' => 'Modern, Proven Treatments',
						'item_text'  => 'From lasers and HydraFacial to minor skin surgery, we use well-tested treatments chosen with your safety in mind.',
					),
					array(
						'item_title' => 'Care That Continues',
						'item_text'  => 'Clear answers, honest advice on what to expect, and follow-up after your visit.',
					),
				),
			)
		);
		$this->button_controls( 'button', __( 'Button text', 'ceori' ), 'About CEORI', home_url( '/about-us/' ) );
		$this->end_controls_section();

		$this->section_style( '.ceori-about' );
		$this->heading_styles();
		$this->style_image( 'image', __( 'Image', 'ceori' ), '.ceori-cover-img img' );
		$this->style_box( 'panel', __( 'Accordion card', 'ceori' ), '.ceori-about__panel' );
		$this->style_text( 'item_title', __( 'Accordion titles', 'ceori' ), '.ceori-acc summary' );
		$this->style_text( 'item_text', __( 'Accordion text', 'ceori' ), '.ceori-acc p' );
		$this->style_button( 'button', __( 'Button', 'ceori' ), '.ceori-about__actions .wp-block-button__link' );
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		echo '<section class="ceori-section ceori-about" id="about"><div class="ceori-wrap">' . $this->head( $s ); // phpcs:ignore WordPress.Security.EscapeOutput
		echo '<div class="wp-block-columns is-layout-flex ceori-about__cols"><div class="wp-block-column ceori-about__media">';
		echo $this->figure( $s['image'], 'large', 'ceori-cover-img' ); // phpcs:ignore WordPress.Security.EscapeOutput
		if ( ! empty( $s['video_id'] ) ) {
			$id = preg_replace( '/[^A-Za-z0-9_-]/', '', $s['video_id'] );
			echo '<p class="ceori-play-wrap"><a class="ceori-play" href="' . esc_url( 'https://www.youtube.com/watch?v=' . $id ) . '" data-video="' . esc_attr( $id ) . '">' . esc_html( $s['video_label'] ) . '</a></p>';
		}
		// Skintiva motion: the heading rises, the photo stays put and the card slides in from the left.
		echo '</div><div class="wp-block-column ceori-about__panel reveal reveal--left">';
		foreach ( (array) $s['items'] as $i => $item ) {
			echo '<details class="wp-block-details is-style-faq ceori-acc"' . ( 0 === $i ? ' open' : '' ) . '><summary>' . esc_html( $item['item_title'] ) . '</summary><p>' . $this->inline( $item['item_text'] ) . '</p></details>'; // phpcs:ignore WordPress.Security.EscapeOutput
		}
		echo $this->buttons( $this->button( $s['button_text'], $s['button_link'] ), 'ceori-about__actions' ); // phpcs:ignore WordPress.Security.EscapeOutput
		echo '</div></div></div></section>';
	}
}

/**
 * Why choose us: four features around a central portrait.
 */
class Why extends Widget {

	public function get_name() {
		return 'ceori-why';
	}

	public function get_title() {
		return __( 'CEORI Why Choose Us', 'ceori' );
	}

	public function get_icon() {
		return 'eicon-info-box';
	}

	protected function register_controls() {
		$this->heading_controls(
			array(
				'eyebrow' => 'Why choose us',
				'title'   => 'Care Built <em>Around</em> You',
				'lead'    => 'Honest advice from specialists and a plan made for your skin – from your first visit to your follow-up.',
				'align'   => 'center',
			)
		);
		$this->start_controls_section( 'content', array( 'label' => __( 'Portrait & features', 'ceori' ) ) );
		$this->add_control(
			'image',
			array(
				'label'   => __( 'Centre portrait', 'ceori' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array( 'url' => ceori_media( 'why-center' )['url'] ),
			)
		);
		$r = new Repeater();
		$r->add_control(
			'icon',
			array(
				'label' => __( 'Icon image', 'ceori' ),
				'type'  => Controls_Manager::MEDIA,
			)
		);
		$r->add_control(
			'f_title',
			array(
				'label' => __( 'Title', 'ceori' ),
				'type'  => Controls_Manager::TEXT,
			)
		);
		$r->add_control(
			'f_text',
			array(
				'label' => __( 'Text', 'ceori' ),
				'type'  => Controls_Manager::TEXTAREA,
			)
		);
		$r->add_control(
			'f_link_text',
			array(
				'label' => __( 'Link text', 'ceori' ),
				'type'  => Controls_Manager::TEXT,
			)
		);
		$r->add_control(
			'f_link',
			array(
				'label' => __( 'Link', 'ceori' ),
				'type'  => Controls_Manager::URL,
			)
		);
		$icon = fn( $n ) => array( 'url' => CEORI_URI . '/assets/icons/' . $n . '.svg' );
		$this->add_control(
			'features',
			array(
				'label'       => __( 'Features (first two left, the rest right)', 'ceori' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $r->get_controls(),
				'title_field' => '{{{ f_title }}}',
				'default'     => array(
					array(
						'icon'        => $icon( 'expert' ),
						'f_title'     => 'Expert Guidance',
						'f_text'      => 'Talk to qualified doctors who take your concerns seriously.',
						'f_link_text' => 'Meet our specialists',
						'f_link'      => array( 'url' => home_url( '/about-us/#specialists' ) ),
					),
					array(
						'icon'        => $icon( 'personal' ),
						'f_title'     => 'Personalised Care',
						'f_text'      => 'Get treatment options that suit you.',
						'f_link_text' => 'Book a consultation',
						'f_link'      => array( 'url' => home_url( '/#contact' ) ),
					),
					array(
						'icon'        => $icon( 'technology' ),
						'f_title'     => 'Modern Technology',
						'f_text'      => 'See the modern skin and cosmetic treatments we offer.',
						'f_link_text' => 'Learn more',
						'f_link'      => array( 'url' => home_url( '/#treatments' ) ),
					),
					array(
						'icon'        => $icon( 'comprehensive' ),
						'f_title'     => 'Everything in One Place',
						'f_text'      => 'Skin, hair, dental and cosmetic care under one roof.',
						'f_link_text' => 'View all treatments',
						'f_link'      => array( 'url' => home_url( '/treatments/' ) ),
					),
				),
			)
		);
		$this->end_controls_section();

		$this->section_style( '.ceori-why' );
		$this->heading_styles();
		$this->style_image( 'portrait', __( 'Portrait', 'ceori' ), '.ceori-why__img img' );
		$this->style_image( 'icon', __( 'Feature icons', 'ceori' ), '.ceori-feature__icon img' );
		$this->style_text( 'f_title', __( 'Feature titles', 'ceori' ), '.ceori-feature__title' );
		$this->style_text( 'f_text', __( 'Feature text', 'ceori' ), '.ceori-feature__text' );
		$this->style_text( 'f_link', __( 'Feature links', 'ceori' ), '.ceori-feature__link a' );
		$this->start_controls_section(
			'connector_style',
			array(
				'label' => __( 'Connector lines', 'ceori' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_control(
			'line_color',
			array(
				'label'     => __( 'Line colour', 'ceori' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .ceori-feature__head::after' => 'background-color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'dot_color',
			array(
				'label'     => __( 'Dot colour', 'ceori' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .ceori-feature__head::before' => 'background-color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'hide_lines',
			array(
				'label'     => __( 'Hide lines', 'ceori' ),
				'type'      => Controls_Manager::SWITCHER,
				'selectors' => array( '{{WRAPPER}} .ceori-feature__head::after, {{WRAPPER}} .ceori-feature__head::before' => 'display: none;' ),
			)
		);
		$this->end_controls_section();
	}

	/**
	 * One feature.
	 *
	 * @param array $f Item.
	 * @return string
	 */
	private function feature( $f ) {
		$out  = '<div class="ceori-feature reveal"><div class="ceori-feature__head">';
		$out .= $this->figure( $f['icon'], 'full', 'ceori-feature__icon', array( 'alt' => '' ) );
		$out .= '<h3 class="ceori-feature__title">' . esc_html( $f['f_title'] ) . '</h3></div>';
		$out .= '<p class="ceori-feature__text">' . $this->inline( $f['f_text'] ) . '</p>';
		if ( ! empty( $f['f_link_text'] ) && ! empty( $f['f_link']['url'] ) ) {
			$out .= '<p class="ceori-feature__link"><a href="' . esc_url( ceori_section_link( $f['f_link']['url'] ) ) . '">' . ceori_link_text( $f['f_link_text'], $f['f_title'] ) . '</a></p>';
		}
		return $out . '</div>';
	}

	protected function render() {
		$s        = $this->get_settings_for_display();
		$features = array_values( (array) $s['features'] );
		$left     = '';
		$right    = '';
		foreach ( $features as $i => $f ) {
			if ( $i < 2 ) {
				$left .= $this->feature( $f );
			} else {
				$right .= $this->feature( $f );
			}
		}
		echo '<section class="ceori-section ceori-why"><div class="ceori-wrap">' . $this->head( $s ); // phpcs:ignore WordPress.Security.EscapeOutput
		echo '<div class="wp-block-columns is-layout-flex ceori-why__cols">';
		echo '<div class="wp-block-column ceori-why__side ceori-why__side--left">' . $left . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput
		echo '<div class="wp-block-column ceori-why__center">' . $this->figure( $s['image'], 'large', 'ceori-why__img' ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput
		echo '<div class="wp-block-column ceori-why__side ceori-why__side--right">' . $right . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput
		echo '</div></div></section>';
	}
}

/**
 * Treatment showcase: four image-led cards in an asymmetric grid.
 */
class Showcase extends Widget {

	protected $photo_kind = 'treatment';

	public function get_name() {
		return 'ceori-treatment-showcase';
	}

	public function get_title() {
		return __( 'CEORI Treatment Showcase', 'ceori' );
	}

	public function get_icon() {
		return 'eicon-gallery-masonry';
	}

	protected function register_controls() {
		$this->heading_controls(
			array(
				'eyebrow' => 'Treatments',
				'title'   => 'Visible Results, <em>Backed by Doctors</em>',
			)
		);
		$this->start_controls_section( 'content', array( 'label' => __( 'Treatments', 'ceori' ) ) );
		$this->add_control(
			'slugs',
			array(
				'label'       => __( 'Treatment pages (slugs, comma-separated, 4 shown)', 'ceori' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 2,
				'default'     => 'laser-hair-removal-in-calicut, hydra-facial-in-calicut, rhinoplasty, acne-scar-microneedling',
				'description' => __( 'The slug is the last part of the page address, e.g. hydra-facial-in-calicut. Card image = the page’s featured image.', 'ceori' ),
			)
		);
		$this->add_control(
			'intro',
			array(
				'label'   => __( 'Text beside the cards', 'ceori' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => 'From everyday skin problems to cosmetic procedures – find the care that suits you.',
			)
		);
		$this->end_controls_section();

		$this->photo_controls();
		$this->section_style( '.ceori-treatments' );
		$this->heading_styles();
		$this->style_image( 'card_img', __( 'Card images', 'ceori' ), '.ceori-card__media img' );
		$this->style_text( 'card_title', __( 'Card titles', 'ceori' ), '.ceori-card__title, .ceori-card__title a' );
		$this->style_text( 'card_link', __( 'Card links', 'ceori' ), '.ceori-card .ceori-link-arrow' );
		$this->style_text( 'intro', __( 'Side text', 'ceori' ), '.ceori-showcase__intro' );
		$this->style_button( 'button', __( '“View all” button', 'ceori' ), '.ceori-showcase .ceori-btn' );
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		echo '<section class="ceori-section ceori-treatments" id="treatments"><div class="ceori-wrap">' . $this->head( $s ); // phpcs:ignore WordPress.Security.EscapeOutput
		echo ceori_render_treatment_grid( // phpcs:ignore WordPress.Security.EscapeOutput
			array(
				'slugs'    => $s['slugs'],
				'layout'   => 'showcase',
				'intro'    => $s['intro'],
				'category' => '',
				'columns'  => 3,
				'limit'    => 0,
			)
		);
		echo '</div></section>';
	}
}

/**
 * Specialities: full-width image strip of the five treatment categories.
 */
class Specialities extends Widget {

	protected $photo_kind = 'category';

	public function get_name() {
		return 'ceori-specialities';
	}

	public function get_title() {
		return __( 'CEORI Specialities Strip', 'ceori' );
	}

	public function get_icon() {
		return 'eicon-slider-push';
	}

	protected function register_controls() {
		$this->heading_controls(
			array(
				'eyebrow' => 'Specialities',
				'title'   => 'Five <em>Specialities</em>, One <em>Clinic</em>',
				'lead'    => 'From skin problems to smile makeovers and cosmetic surgery – pick a speciality to see all its treatments.',
				'align'   => 'center',
			)
		);
		$this->start_controls_section( 'content', array( 'label' => __( 'Strip', 'ceori' ) ) );
		$this->add_control(
			'note',
			array(
				'type' => Controls_Manager::RAW_HTML,
				'raw'  => __( 'Names, summaries and photos come from <strong>Pages → Treatment categories</strong>.', 'ceori' ),
			)
		);
		$this->end_controls_section();

		$this->photo_controls();
		$this->section_style( '.ceori-specialities' );
		$this->heading_styles();
		$this->style_box( 'strip', __( 'Strip', 'ceori' ), '.ceori-panels', array( 'height' => true ) );
		$this->start_controls_section(
			'overlay_style',
			array(
				'label' => __( 'Overlay & dividers', 'ceori' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_control(
			'overlay',
			array(
				'label'     => __( 'Hover tint', 'ceori' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .ceori-panel__link:hover, {{WRAPPER}} .ceori-panel__link:focus-visible' => 'background-color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'divider',
			array(
				'label'     => __( 'Divider colour', 'ceori' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .ceori-panel' => 'border-color: {{VALUE}};' ),
			)
		);
		$this->end_controls_section();
		$this->style_text( 'panel_title', __( 'Category names', 'ceori' ), '.ceori-panel__title' );
		$this->style_text( 'panel_text', __( 'Category summaries', 'ceori' ), '.ceori-panel__text' );
		$this->style_text( 'panel_cta', __( 'Category links', 'ceori' ), '.ceori-panel__cta' );
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		echo '<section class="ceori-section ceori-specialities"><div class="ceori-wrap">' . $this->head( $s ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput
		echo ceori_render_treatment_categories( // phpcs:ignore WordPress.Security.EscapeOutput
			array(
				'layout'         => 'panels',
				'showTreatments' => false,
			)
		);
		echo '</section>';
	}
}
