<?php
/**
 * Base class for CEORI Elementor widgets.
 *
 * Every widget renders the same markup and classes as the theme's design, so it looks
 * right out of the box, and exposes Elementor style controls (typography, colours, text
 * shadow, spacing, backgrounds, borders, radius, box shadow, button hover states) for each
 * visible part. Elementor's own Advanced tab (margin, padding, motion effects, responsive
 * visibility, custom classes) is available on every widget as usual.
 *
 * @package ceori
 */

namespace Ceori\Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Shared controls and markup helpers.
 */
abstract class Widget extends Widget_Base {

	/**
	 * Widget category.
	 */
	public function get_categories() {
		return array( 'ceori' );
	}

	/**
	 * Search keywords.
	 */
	public function get_keywords() {
		return array( 'ceori', 'clinic' );
	}

	/**
	 * Keep the widget wrapper lean.
	 */
	public function has_widget_inner_wrapper(): bool {
		return false;
	}

	/*
	 * ------------------------------------------------------------------
	 * Content controls
	 * ------------------------------------------------------------------
	 */

	/**
	 * Section heading controls: eyebrow, title (italic words with <em>), lead, alignment.
	 *
	 * @param array $d Defaults: eyebrow, title, lead, align, tag.
	 */
	protected function heading_controls( $d = array() ) {
		$d = wp_parse_args(
			$d,
			array(
				'eyebrow' => '',
				'title'   => '',
				'lead'    => '',
				'align'   => 'left',
				'tag'     => 'h2',
			)
		);
		$this->start_controls_section( 'section_heading', array( 'label' => __( 'Heading', 'ceori' ) ) );
		$this->add_control(
			'eyebrow',
			array(
				'label'       => __( 'Small label above', 'ceori' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => $d['eyebrow'],
				'label_block' => true,
			)
		);
		$this->add_control(
			'title',
			array(
				'label'       => __( 'Title', 'ceori' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 2,
				'default'     => $d['title'],
				'description' => __( 'Wrap words in &lt;em&gt;…&lt;/em&gt; for the italic accent style.', 'ceori' ),
			)
		);
		$this->add_control(
			'title_tag',
			array(
				'label'   => __( 'Title HTML tag', 'ceori' ),
				'type'    => Controls_Manager::SELECT,
				'default' => $d['tag'],
				'options' => array(
					'h1'  => 'H1',
					'h2'  => 'H2',
					'h3'  => 'H3',
					'h4'  => 'H4',
					'div' => 'div',
				),
			)
		);
		$this->add_control(
			'lead',
			array(
				'label'   => __( 'Introduction', 'ceori' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 3,
				'default' => $d['lead'],
			)
		);
		$this->add_control(
			'head_align',
			array(
				'label'   => __( 'Heading alignment', 'ceori' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => $d['align'],
				'options' => array(
					'left'   => array(
						'title' => __( 'Left', 'ceori' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'ceori' ),
						'icon'  => 'eicon-text-align-center',
					),
				),
			)
		);
		$this->end_controls_section();
	}

	/**
	 * Button text + link controls.
	 *
	 * @param string $key   Control prefix.
	 * @param string $label Label.
	 * @param string $text  Default text.
	 * @param string $url   Default URL.
	 */
	protected function button_controls( $key, $label, $text, $url ) {
		$this->add_control(
			$key . '_text',
			array(
				'label'     => $label,
				'type'      => Controls_Manager::TEXT,
				'default'   => $text,
				'separator' => 'before',
			)
		);
		$this->add_control(
			$key . '_link',
			array(
				'label'   => __( 'Link', 'ceori' ),
				'type'    => Controls_Manager::URL,
				'default' => array( 'url' => $url ),
			)
		);
	}

	/*
	 * ------------------------------------------------------------------
	 * Style controls
	 * ------------------------------------------------------------------
	 */

	/**
	 * Whether the "Show all style options" switch has been added to this widget.
	 *
	 * @var bool
	 */
	private $style_switch_added = false;

	/**
	 * Style tab, first section: a switch for the less common options. With it off the editor
	 * shows only colours, fonts, alignment, backgrounds, spacing, sizes and corners; borders,
	 * shadows, margins, hover colours, image fit and opacity are hidden from the panel (any value
	 * already set still applies). Hiding happens in the editor only (see inc/elementor.php),
	 * because Elementor drops the CSS of controls hidden by a condition.
	 */
	protected function style_switch() {
		if ( $this->style_switch_added ) {
			return;
		}
		$this->style_switch_added = true;
		$this->start_controls_section(
			'ceori_style_options',
			array(
				'label' => __( 'Style options', 'ceori' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_control(
			'ceori_more',
			array(
				'label'       => __( 'Show all style options', 'ceori' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Off: colours, fonts, spacing and corners. On: also borders, shadows, margins, hover colours and image fit. Hidden options keep their settings.', 'ceori' ),
			)
		);
		$this->end_controls_section();
	}

	/**
	 * Prefix each comma-separated selector with {{WRAPPER}}.
	 *
	 * @param string $selector Selector list.
	 * @return string
	 */
	protected function sel( $selector ) {
		return implode( ', ', array_map( fn( $s ) => '{{WRAPPER}} ' . trim( $s ), explode( ',', $selector ) ) );
	}

	/**
	 * Text style section: colour, typography, text shadow, alignment, margin.
	 *
	 * @param string $key      Prefix.
	 * @param string $label    Section label.
	 * @param string $selector CSS selector(s) inside the widget.
	 * @param array  $opts     'align' => bool.
	 */
	protected function style_text( $key, $label, $selector, $opts = array() ) {
		$this->style_switch();
		$s = $this->sel( $selector );
		$this->start_controls_section(
			$key . '_style',
			array(
				'label' => $label,
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_control(
			$key . '_color',
			array(
				'label'     => __( 'Colour', 'ceori' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( $s => 'color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			$key . '_hover_color',
			array(
				'classes'   => 'ceori-adv',
				'label'     => __( 'Link hover colour', 'ceori' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					implode(
						', ',
						array_merge(
							array_map( fn( $x ) => $x . ' a:hover', explode( ', ', $s ) ),
							array_map( fn( $x ) => $x . ':hover', explode( ', ', $s ) )
						)
					) => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => $key . '_typography',
				'selector' => $s,
			)
		);
		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => $key . '_text_shadow',
				'selector' => $s,
			)
		);
		if ( ! empty( $opts['align'] ) ) {
			$this->add_responsive_control(
				$key . '_align',
				array(
					'label'     => __( 'Alignment', 'ceori' ),
					'type'      => Controls_Manager::CHOOSE,
					'options'   => array(
						'left'   => array(
							'title' => __( 'Left', 'ceori' ),
							'icon'  => 'eicon-text-align-left',
						),
						'center' => array(
							'title' => __( 'Center', 'ceori' ),
							'icon'  => 'eicon-text-align-center',
						),
						'right'  => array(
							'title' => __( 'Right', 'ceori' ),
							'icon'  => 'eicon-text-align-right',
						),
					),
					'selectors' => array( $s => 'text-align: {{VALUE}};' ),
				)
			);
		}
		$this->add_responsive_control(
			$key . '_margin',
			array(
				'classes'   => 'ceori-adv',
				'label'      => __( 'Margin', 'ceori' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem', '%' ),
				'selectors'  => array( $s => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);
		$this->end_controls_section();
	}

	/**
	 * Box style section: background, padding, border, radius, box shadow, optional min height.
	 *
	 * @param string $key      Prefix.
	 * @param string $label    Section label.
	 * @param string $selector Selector(s).
	 * @param array  $opts     'height' => bool, 'gap' => selector for spacing.
	 */
	protected function style_box( $key, $label, $selector, $opts = array() ) {
		$this->style_switch();
		$s = $this->sel( $selector );
		$this->start_controls_section(
			$key . '_style',
			array(
				'label' => $label,
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => $key . '_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => $s,
			)
		);
		$this->add_responsive_control(
			$key . '_padding',
			array(
				'label'      => __( 'Padding', 'ceori' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem', '%', 'vw' ),
				'selectors'  => array( $s => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);
		if ( ! empty( $opts['height'] ) ) {
			$this->add_responsive_control(
				$key . '_height',
				array(
					'label'      => __( 'Height', 'ceori' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( 'px', 'vh', 'vw' ),
					'range'      => array(
						'px' => array(
							'min' => 200,
							'max' => 1400,
						),
					),
					'selectors'  => array( $s => 'height: {{SIZE}}{{UNIT}}; min-height: 0;' ),
				)
			);
		}
		if ( ! empty( $opts['gap'] ) ) {
			$this->add_responsive_control(
				$key . '_gap',
				array(
					'label'      => __( 'Spacing between items', 'ceori' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( 'px', 'em', 'rem' ),
					'selectors'  => array( $this->sel( $opts['gap'] ) => 'gap: {{SIZE}}{{UNIT}} !important;' ),
				)
			);
		}
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => $key . '_border',
				'selector' => $s,
			)
		);
		$this->add_responsive_control(
			$key . '_radius',
			array(
				'label'      => __( 'Border radius', 'ceori' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array( $s => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => $key . '_shadow',
				'selector' => $s,
			)
		);
		$this->end_controls_section();
	}

	/**
	 * Image style section: width, height, object fit, radius, border, shadow, CSS filters via opacity.
	 *
	 * @param string $key      Prefix.
	 * @param string $label    Label.
	 * @param string $selector img selector.
	 */
	protected function style_image( $key, $label, $selector ) {
		$this->style_switch();
		$s = $this->sel( $selector );
		$this->start_controls_section(
			$key . '_style',
			array(
				'label' => $label,
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_responsive_control(
			$key . '_width',
			array(
				'label'      => __( 'Width', 'ceori' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%', 'px', 'vw' ),
				'selectors'  => array( $s => 'width: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			$key . '_height',
			array(
				'label'      => __( 'Height', 'ceori' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vh', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 50,
						'max' => 1200,
					),
				),
				'selectors'  => array( $s => 'height: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			$key . '_fit',
			array(
				'classes'   => 'ceori-adv',
				'label'     => __( 'Object fit', 'ceori' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					''        => __( 'Default', 'ceori' ),
					'cover'   => __( 'Cover', 'ceori' ),
					'contain' => __( 'Contain', 'ceori' ),
					'fill'    => __( 'Fill', 'ceori' ),
				),
				'selectors' => array( $s => 'object-fit: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => $key . '_border',
				'selector' => $s,
			)
		);
		$this->add_responsive_control(
			$key . '_radius',
			array(
				'label'      => __( 'Border radius', 'ceori' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array( $s => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => $key . '_shadow',
				'selector' => $s,
			)
		);
		$this->add_control(
			$key . '_opacity',
			array(
				'classes'   => 'ceori-adv',
				'label'     => __( 'Opacity', 'ceori' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 1,
						'step' => 0.05,
					),
				),
				'selectors' => array( $s => 'opacity: {{SIZE}};' ),
			)
		);
		$this->end_controls_section();
	}

	/**
	 * Button style section with normal/hover tabs.
	 *
	 * @param string $key      Prefix.
	 * @param string $label    Label.
	 * @param string $selector Link selector.
	 */
	protected function style_button( $key, $label, $selector ) {
		$this->style_switch();
		$s     = $this->sel( $selector );
		$hover = implode( ', ', array_map( fn( $x ) => $x . ':hover, ' . $x . ':focus-visible', explode( ', ', $s ) ) );
		$this->start_controls_section(
			$key . '_style',
			array(
				'label' => $label,
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => $key . '_typography',
				'selector' => $s,
			)
		);
		$this->start_controls_tabs( $key . '_tabs' );
		foreach ( array(
			'normal' => array( __( 'Normal', 'ceori' ), $s ),
			'hover'  => array( __( 'Hover', 'ceori' ), $hover ),
		) as $state => $info ) {
			$this->start_controls_tab( $key . '_tab_' . $state, array( 'label' => $info[0] ) );
			$this->add_control(
				$key . '_' . $state . '_color',
				array(
					'label'     => __( 'Text colour', 'ceori' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array( $info[1] => 'color: {{VALUE}} !important;' ),
				)
			);
			$this->add_control(
				$key . '_' . $state . '_bg',
				array(
					'label'     => __( 'Background colour', 'ceori' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array( $info[1] => 'background-color: {{VALUE}} !important;' ),
				)
			);
			$this->add_control(
				$key . '_' . $state . '_border_color',
				array(
					'label'     => __( 'Border colour', 'ceori' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array( $info[1] => 'border-color: {{VALUE}} !important;' ),
				)
			);
			$this->end_controls_tab();
		}
		$this->end_controls_tabs();
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => $key . '_border',
				'selector'  => $s,
				'separator' => 'before',
			)
		);
		$this->add_responsive_control(
			$key . '_radius',
			array(
				'label'      => __( 'Border radius', 'ceori' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array( $s => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			$key . '_padding',
			array(
				'classes'    => 'ceori-adv',
				'label'      => __( 'Padding', 'ceori' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem' ),
				'selectors'  => array( $s => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => $key . '_shadow',
				'selector' => $s,
			)
		);
		$this->end_controls_section();
	}

	/**
	 * Standard style sections for the section heading (eyebrow, marker, title, accent, lead).
	 */
	protected function heading_styles() {
		$this->style_text( 'eyebrow', __( 'Heading – small label', 'ceori' ), '.ceori-eyebrow' );
		$this->start_controls_section(
			'marker_style',
			array(
				'label' => __( 'Heading – label marker', 'ceori' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_control(
			'marker_color',
			array(
				'classes'   => 'ceori-adv',
				'label'     => __( 'Marker colour', 'ceori' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .ceori-eyebrow::after' => 'background-color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'marker_hide',
			array(
				'classes'   => 'ceori-adv',
				'label'     => __( 'Hide marker', 'ceori' ),
				'type'      => Controls_Manager::SWITCHER,
				'selectors' => array( '{{WRAPPER}} .ceori-eyebrow::after' => 'display: none;' ),
			)
		);
		$this->end_controls_section();
		$this->style_text( 'title', __( 'Heading – title', 'ceori' ), '.ceori-head-title' );
		$this->style_text( 'accent', __( 'Heading – italic accent words', 'ceori' ), '.ceori-head-title em' );
		$this->style_text( 'lead', __( 'Heading – introduction', 'ceori' ), '.ceori-section__lead' );
	}

	/**
	 * Section (outer band) style: background, padding, border, radius, shadow.
	 *
	 * @param string $selector Section selector.
	 */
	protected function section_style( $selector = '.ceori-section' ) {
		$this->style_box( 'section', __( 'Section background & spacing', 'ceori' ), $selector );
	}

	/*
	 * ------------------------------------------------------------------
	 * Markup helpers
	 * ------------------------------------------------------------------
	 */

	/**
	 * Allowed inline HTML in titles and short texts.
	 *
	 * @param string $text Text.
	 * @return string
	 */
	protected function inline( $text ) {
		return wp_kses(
			(string) $text,
			array(
				'em'     => array(),
				'strong' => array(),
				'br'     => array(),
				'span'   => array( 'class' => array() ),
				'a'      => array(
					'href'   => array(),
					'target' => array(),
					'rel'    => array(),
				),
			)
		);
	}

	/**
	 * Section heading markup.
	 *
	 * @param array  $s      Settings.
	 * @param string $extra  Extra HTML placed beside the heading (e.g. a button).
	 * @param bool   $center Force centred.
	 * @return string
	 */
	protected function head( $s, $extra = '', $center = false ) {
		$eyebrow = isset( $s['eyebrow'] ) ? $s['eyebrow'] : '';
		$title   = isset( $s['title'] ) ? $s['title'] : '';
		$lead    = isset( $s['lead'] ) ? $s['lead'] : '';
		if ( '' === trim( $eyebrow . $title . $lead ) && '' === $extra ) {
			return '';
		}
		$tag      = isset( $s['title_tag'] ) && in_array( $s['title_tag'], array( 'h1', 'h2', 'h3', 'h4', 'div' ), true ) ? $s['title_tag'] : 'h2';
		$centered = $center || ( isset( $s['head_align'] ) && 'center' === $s['head_align'] );
		$inner    = ( $eyebrow ? '<p class="ceori-eyebrow">' . esc_html( $eyebrow ) . '</p>' : '' )
			. ( $title ? '<' . $tag . ' class="ceori-head-title">' . $this->inline( $title ) . '</' . $tag . '>' : '' )
			. ( $lead ? '<p class="ceori-section__lead">' . $this->inline( $lead ) . '</p>' : '' );
		if ( $extra && ! $centered ) {
			return '<div class="ceori-section__head ceori-section__head--split"><div>' . $inner . '</div>' . $extra . '</div>';
		}
		return '<div class="ceori-section__head' . ( $centered ? ' is-centered' : '' ) . '">' . $inner . $extra . '</div>';
	}

	/**
	 * Button markup in the theme style.
	 *
	 * @param string       $text  Text.
	 * @param array|string $link  URL control value or URL.
	 * @param string       $style solid|outline|phone|white.
	 * @return string
	 */
	protected function button( $text, $link, $style = 'solid' ) {
		if ( '' === trim( (string) $text ) ) {
			return '';
		}
		$url   = is_array( $link ) ? ( isset( $link['url'] ) ? $link['url'] : '' ) : (string) $link;
		$attrs = '';
		if ( is_array( $link ) && ! empty( $link['is_external'] ) ) {
			$attrs .= ' target="_blank" rel="noopener"';
		}
		$classes = array(
			'solid'   => 'wp-block-button has-arrow',
			'outline' => 'wp-block-button is-style-outline-ceori',
			'phone'   => 'wp-block-button is-style-outline-ceori has-phone',
			'white'   => 'wp-block-button is-style-outline-ceori has-phone ceori-cta__call',
		);
		return '<div class="' . esc_attr( $classes[ $style ] ?? $classes['solid'] ) . '"><a class="wp-block-button__link wp-element-button" href="' . esc_url( $url ) . '"' . $attrs . '>' . esc_html( $text ) . '</a></div>';
	}

	/**
	 * Wrap buttons.
	 *
	 * @param string $buttons Buttons HTML.
	 * @param string $class   Extra class.
	 * @return string
	 */
	protected function buttons( $buttons, $class = '' ) {
		return trim( $buttons ) ? '<div class="wp-block-buttons is-layout-flex ' . esc_attr( $class ) . '">' . $buttons . '</div>' : '';
	}

	/**
	 * Image markup from a MEDIA control value.
	 *
	 * @param array  $media  Media value.
	 * @param string $size   Image size.
	 * @param string $class  Figure classes.
	 * @param array  $attr   Extra img attributes.
	 * @return string
	 */
	protected function figure( $media, $size, $class, $attr = array() ) {
		$img = $this->img( $media, $size, $attr );
		return $img ? '<figure class="wp-block-image ' . esc_attr( $class ) . '">' . $img . '</figure>' : '';
	}

	/**
	 * Bare img tag from a MEDIA control value.
	 *
	 * @param array  $media Media value.
	 * @param string $size  Size.
	 * @param array  $attr  Attributes.
	 * @return string
	 */
	protected function img( $media, $size = 'large', $attr = array() ) {
		$id = ! empty( $media['id'] ) ? (int) $media['id'] : 0;
		$attr = array_merge( array( 'loading' => 'lazy' ), $attr );
		if ( 'local' === wp_get_environment_type() && isset( $_GET['ceori_nolazy'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			unset( $attr['loading'] );
		}
		if ( $id && wp_attachment_is_image( $id ) ) {
			return wp_get_attachment_image( $id, $size, false, $attr );
		}
		if ( ! empty( $media['url'] ) ) {
			$alt = isset( $attr['alt'] ) ? $attr['alt'] : ( isset( $media['alt'] ) ? $media['alt'] : '' );
			return '<img src="' . esc_url( $media['url'] ) . '" alt="' . esc_attr( $alt ) . '" loading="lazy">';
		}
		return '';
	}

	/**
	 * Whether we are inside the Elementor editor.
	 */
	protected function is_editor() {
		return \Elementor\Plugin::$instance->editor->is_edit_mode() || \Elementor\Plugin::$instance->preview->is_preview_mode();
	}

	/**
	 * Editor-only notice for sections that are hidden on the live site.
	 *
	 * @param string $message Message.
	 */
	protected function editor_notice( $message ) {
		if ( $this->is_editor() ) {
			echo '<div class="ceori-editor-notice">' . esc_html( $message ) . '</div>';
		}
	}

	/**
	 * ID of the page being viewed or edited.
	 */
	protected function page_id() {
		$id = get_the_ID();
		return $id ? (int) $id : (int) get_queried_object_id();
	}

	/**
	 * Lines of a textarea as an array.
	 *
	 * @param string $text Text.
	 * @return string[]
	 */
	protected function lines( $text ) {
		return array_values( array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', (string) $text ) ) ) );
	}

	/**
	 * Check-mark list markup.
	 *
	 * @param string[] $items Items.
	 * @return string
	 */
	protected function checklist( $items ) {
		if ( ! $items ) {
			return '';
		}
		$out = '<ul class="wp-block-list is-style-checks">';
		foreach ( $items as $item ) {
			$out .= '<li>' . $this->inline( $item ) . '</li>';
		}
		return $out . '</ul>';
	}

	/*
	 * ------------------------------------------------------------------
	 * Card photos: per-widget photo choices for cards that normally take
	 * their photo from elsewhere (featured images, category images).
	 * ------------------------------------------------------------------
	 */

	/**
	 * What the cards are: treatment, category, doctor, testimonial, equipment or post.
	 * Empty means the widget has no "Card photos" section.
	 *
	 * @var string
	 */
	protected $photo_kind = '';

	/**
	 * "Card photos" section: pick a card and a photo for it.
	 *
	 * @param array $condition Optional Elementor condition for showing the section.
	 */
	protected function photo_controls( $condition = array() ) {
		if ( ! $this->photo_kind ) {
			return;
		}
		$options = self::photo_item_options( $this->photo_kind );
		$this->start_controls_section( 'section_card_photos', array_filter( array( 'label' => __( 'Card photos', 'ceori' ), 'condition' => $condition ) ) );
		$this->add_control(
			'card_photos_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'Add a row, pick a card and choose a photo. It replaces that card’s usual photo in this widget only. Cards you don’t add keep their usual photo.', 'ceori' ),
				'content_classes' => 'elementor-descriptor',
			)
		);
		$r = new Repeater();
		$r->add_control(
			'item',
			array(
				'label'       => __( 'Card', 'ceori' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array( '' => __( '— Choose a card —', 'ceori' ) ) + $options,
				'default'     => '',
				'label_block' => true,
			)
		);
		$r->add_control(
			'photo',
			array(
				'label' => __( 'Photo', 'ceori' ),
				'type'  => Controls_Manager::MEDIA,
			)
		);
		$this->add_control(
			'card_photos',
			array(
				'label'         => __( 'Photos', 'ceori' ),
				'type'          => Controls_Manager::REPEATER,
				'fields'        => $r->get_controls(),
				'default'       => array(),
				'prevent_empty' => false,
				'title_field'   => '<# var ceoriCards = ' . wp_json_encode( (object) $options ) . '; #>{{ ceoriCards[ item ] || "' . esc_js( __( 'Choose a card', 'ceori' ) ) . '" }}',
			)
		);
		$this->end_controls_section();
	}

	/**
	 * Cards that can be picked, keyed "c" + ID so the list keeps its alphabetical order.
	 *
	 * @param string $kind Card kind.
	 * @return array
	 */
	protected static function photo_item_options( $kind ) {
		static $cache = array();
		if ( isset( $cache[ $kind ] ) ) {
			return $cache[ $kind ];
		}
		$out = array();
		if ( 'category' === $kind ) {
			$terms = get_terms(
				array(
					'taxonomy'   => 'treatment_category',
					'hide_empty' => false,
				)
			);
			foreach ( is_array( $terms ) ? $terms : array() as $term ) {
				$out[ 'c' . $term->term_id ] = $term->name;
			}
		} else {
			$types = array(
				'treatment'   => 'page',
				'doctor'      => 'ceori_doctor',
				'testimonial' => 'ceori_testimonial',
				'equipment'   => 'ceori_equipment',
				'post'        => 'post',
			);
			$args  = array(
				'post_type'   => isset( $types[ $kind ] ) ? $types[ $kind ] : 'post',
				'post_status' => 'publish',
				'numberposts' => 300,
				'orderby'     => 'title',
				'order'       => 'ASC',
			);
			if ( 'treatment' === $kind ) {
				$args['meta_key']   = '_wp_page_template'; // phpcs:ignore WordPress.DB.SlowDBQuery
				$args['meta_value'] = 'page-treatment.php'; // phpcs:ignore WordPress.DB.SlowDBQuery
			}
			foreach ( get_posts( $args ) as $post ) {
				$title                     = html_entity_decode( get_the_title( $post ), ENT_QUOTES, 'UTF-8' );
				$out[ 'c' . $post->ID ] = '' !== $title ? $title : '#' . $post->ID;
			}
		}
		$cache[ $kind ] = $out;
		return $out;
	}

	/**
	 * Render with the chosen card photos in place, then put everything back.
	 */
	public function render_content() {
		$hook = $this->photo_kind ? $this->photo_overrides_on() : null;
		try {
			parent::render_content();
		} finally {
			if ( $hook ) {
				remove_filter( $hook[0], $hook[1], 10 );
			}
		}
	}

	/**
	 * Swap in the chosen photos while this widget renders.
	 *
	 * @return array|null Filter name and callback to remove afterwards.
	 */
	private function photo_overrides_on() {
		$rows = $this->get_settings( 'card_photos' );
		$map  = array();
		foreach ( is_array( $rows ) ? $rows : array() as $row ) {
			$id  = isset( $row['item'] ) ? absint( substr( (string) $row['item'], 1 ) ) : 0;
			$img = ! empty( $row['photo']['id'] ) ? absint( $row['photo']['id'] ) : 0;
			if ( $id && $img && wp_attachment_is_image( $img ) ) {
				$map[ $id ] = $img;
			}
		}
		if ( ! $map ) {
			return null;
		}
		if ( 'category' === $this->photo_kind ) {
			$cb = function ( $value, $object_id, $meta_key ) use ( $map ) {
				return ( 'ceori_image' === $meta_key && isset( $map[ (int) $object_id ] ) ) ? array( $map[ (int) $object_id ] ) : $value;
			};
			add_filter( 'get_term_metadata', $cb, 10, 3 );
			return array( 'get_term_metadata', $cb );
		}
		$cb = function ( $thumbnail_id, $post ) use ( $map ) {
			$pid = $post instanceof \WP_Post ? $post->ID : (int) $post;
			return isset( $map[ $pid ] ) ? $map[ $pid ] : $thumbnail_id;
		};
		add_filter( 'post_thumbnail_id', $cb, 10, 2 );
		return array( 'post_thumbnail_id', $cb );
	}
}
