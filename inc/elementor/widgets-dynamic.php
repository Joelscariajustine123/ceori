<?php
/**
 * Widgets fed by dashboard content: Specialists, Testimonials, Results gallery, Latest
 * articles, Call to action, Category cards, Treatment grid, Related treatments, Contact,
 * Clinic info, Side panel menu, Breadcrumbs.
 *
 * @package ceori
 */

namespace Ceori\Elementor;

use Elementor\Controls_Manager;
use Elementor\Repeater;

defined( 'ABSPATH' ) || exit;

/**
 * Treatment category options for SELECT controls.
 *
 * @param string $empty Label of the empty option.
 * @return array
 */
function category_options( $empty ) {
	$options = array( '' => $empty );
	foreach ( ceori_treatment_categories() as $term ) {
		$options[ $term->slug ] = $term->name;
	}
	return $options;
}

/**
 * Specialists.
 */
class Specialists extends Widget {

	protected $photo_kind = 'doctor';

	public function get_name() {
		return 'ceori-specialists';
	}

	public function get_title() {
		return __( 'CEORI Specialists', 'ceori' );
	}

	public function get_icon() {
		return 'eicon-person';
	}

	protected function register_controls() {
		$this->heading_controls(
			array(
				'eyebrow' => 'Our specialists',
				'title'   => 'Meet the <em>Experts</em> Behind Your Care',
				'lead'    => 'Skin specialists, a plastic surgeon, a cosmetic gynaecologist and an oral medicine specialist – each one focused on you.',
			)
		);
		$this->start_controls_section( 'content', array( 'label' => __( 'Specialists', 'ceori' ) ) );
		$this->add_control(
			'note',
			array(
				'type' => Controls_Manager::RAW_HTML,
				'raw'  => __( 'Photos, names and designations are managed under <strong>Specialists</strong> in the dashboard.', 'ceori' ),
			)
		);
		$this->add_control(
			'category',
			array(
				'label'   => __( 'Show', 'ceori' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => category_options( __( 'All specialists', 'ceori' ) ),
			)
		);
		$this->add_control(
			'layout',
			array(
				'label'   => __( 'Layout', 'ceori' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'carousel',
				'options' => array(
					'carousel' => __( 'Scrolling row', 'ceori' ),
					'grid'     => __( 'Grid', 'ceori' ),
				),
			)
		);
		$this->add_control(
			'limit',
			array(
				'label'   => __( 'Maximum (0 = all)', 'ceori' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 0,
				'min'     => 0,
			)
		);
		$this->button_controls( 'button', __( 'Button text (leave empty to hide)', 'ceori' ), 'Book a Consultation', home_url( '/contact-us/#appointment' ) );
		$this->end_controls_section();

		$this->photo_controls();
		$this->section_style( '.ceori-specialists' );
		$this->heading_styles();
		$this->style_box( 'card', __( 'Cards', 'ceori' ), '.ceori-doctor' );
		$this->style_image( 'photo', __( 'Photos', 'ceori' ), '.ceori-doctor__media img' );
		$this->style_text( 'name', __( 'Names', 'ceori' ), '.ceori-doctor__name' );
		$this->style_text( 'role', __( 'Designations', 'ceori' ), '.ceori-doctor__role' );
		$this->style_text( 'link', __( 'Card links', 'ceori' ), '.ceori-doctor .ceori-link-arrow' );
		$this->style_button( 'button', __( 'Button', 'ceori' ), '.ceori-section__head .wp-block-button__link' );
	}

	protected function render() {
		$s      = $this->get_settings_for_display();
		$button = $this->buttons( $this->button( $s['button_text'], $s['button_link'] ) );
		$html   = ceori_render_doctors(
			array(
				'category' => $s['category'],
				'limit'    => (int) $s['limit'],
				'layout'   => $s['layout'],
				'caption'  => '',
			)
		);
		echo '<section class="ceori-section ceori-specialists" id="specialists"><div class="ceori-wrap">' . $this->head( $s, $button ); // phpcs:ignore WordPress.Security.EscapeOutput
		if ( $html ) {
			echo $html; // phpcs:ignore WordPress.Security.EscapeOutput
		} else {
			$this->editor_notice( __( 'Add specialists under Specialists in the dashboard.', 'ceori' ) );
		}
		echo '</div></section>';
	}
}

/**
 * Patient testimonials (2 × 2 grid, paged).
 */
class Testimonials extends Widget {

	protected $photo_kind = 'testimonial';

	public function get_name() {
		return 'ceori-testimonials';
	}

	public function get_title() {
		return __( 'CEORI Testimonials', 'ceori' );
	}

	public function get_icon() {
		return 'eicon-testimonial-carousel';
	}

	protected function register_controls() {
		$this->heading_controls(
			array(
				'eyebrow' => 'Patient stories',
				'title'   => 'Real Results, <em>Real Confidence</em>',
				'lead'    => 'See why patients across Calicut trust CEORI Skin Clinic for skin, hair and cosmetic care.',
				'align'   => 'center',
			)
		);
		$this->start_controls_section( 'content', array( 'label' => __( 'Testimonials', 'ceori' ) ) );
		$this->add_control(
			'note',
			array(
				'type' => Controls_Manager::RAW_HTML,
				'raw'  => __( 'Reviews are managed under <strong>Testimonials</strong>. Only published reviews with the consent box ticked are shown; the whole section stays hidden until then.', 'ceori' ),
			)
		);
		$this->add_control(
			'limit',
			array(
				'label'   => __( 'Maximum testimonials', 'ceori' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 8,
				'min'     => 1,
			)
		);
		$this->end_controls_section();

		$this->photo_controls();
		$this->section_style( '.ceori-testimonials-section' );
		$this->heading_styles();
		$this->style_box( 'card', __( 'Cards', 'ceori' ), '.ceori-testimonial__card' );
		$this->style_image( 'photo', __( 'Photos', 'ceori' ), '.ceori-testimonial__media img' );
		$this->style_text( 'label', __( 'Treatment / rating labels', 'ceori' ), '.ceori-testimonial__label' );
		$this->style_text( 'value', __( 'Treatment / rating values', 'ceori' ), '.ceori-testimonial__value' );
		$this->style_text( 'quote', __( 'Review text', 'ceori' ), '.ceori-testimonial__quote' );
		$this->style_text( 'author', __( 'Patient name', 'ceori' ), '.ceori-testimonial__author' );
		$this->style_button( 'nav', __( 'Arrows', 'ceori' ), '.ceori-round-btn' );
	}

	protected function render() {
		$s    = $this->get_settings_for_display();
		$html = ceori_render_testimonials( array( 'limit' => max( 1, (int) $s['limit'] ) ) );
		if ( ! $html ) {
			$this->editor_notice( __( 'Testimonials section – hidden on the live site until approved testimonials are published.', 'ceori' ) );
			return;
		}
		echo '<section class="ceori-section ceori-testimonials-section"><div class="ceori-wrap">' . $this->head( $s ) . $html . '</div></section>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}
}

/**
 * Results gallery.
 */
class Results extends Widget {

	public function get_name() {
		return 'ceori-results';
	}

	public function get_title() {
		return __( 'CEORI Results Gallery', 'ceori' );
	}

	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	protected function register_controls() {
		$this->heading_controls(
			array(
				'eyebrow' => 'Gallery',
				'title'   => 'Results That Tell a <em>Story</em>',
				'lead'    => 'Real results from CEORI patients who kindly agreed to share their photos. Every result is different.',
				'align'   => 'center',
			)
		);
		$this->start_controls_section( 'content', array( 'label' => __( 'Gallery', 'ceori' ) ) );
		$this->add_control(
			'note',
			array(
				'type' => Controls_Manager::RAW_HTML,
				'raw'  => __( 'Photos are managed under <strong>Results Gallery</strong>. Only published items with documented consent are shown; the whole section stays hidden until then.', 'ceori' ),
			)
		);
		$this->add_control(
			'limit',
			array(
				'label'   => __( 'Maximum images', 'ceori' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 12,
				'min'     => 1,
			)
		);
		$this->add_control(
			'filters',
			array(
				'label'        => __( 'Category filters', 'ceori' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);
		$this->end_controls_section();

		$this->section_style( '.ceori-results-section' );
		$this->heading_styles();
		$this->style_image( 'photo', __( 'Images', 'ceori' ), '.ceori-result img' );
		$this->style_text( 'caption', __( 'Captions', 'ceori' ), '.ceori-result figcaption' );
		$this->style_button( 'filter', __( 'Filter buttons', 'ceori' ), '.ceori-filters button' );
	}

	protected function render() {
		$s    = $this->get_settings_for_display();
		$html = ceori_render_results_gallery(
			array(
				'limit'   => max( 1, (int) $s['limit'] ),
				'filters' => 'yes' === $s['filters'],
			)
		);
		if ( ! $html ) {
			$this->editor_notice( __( 'Results gallery – hidden on the live site until consented patient photos are published.', 'ceori' ) );
			return;
		}
		echo '<section class="ceori-section ceori-results-section"><div class="ceori-wrap">' . $this->head( $s ) . $html . '</div></section>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}
}

/**
 * Latest blog articles.
 */
class Latest_Posts extends Widget {

	protected $photo_kind = 'post';

	public function get_name() {
		return 'ceori-latest-posts';
	}

	public function get_title() {
		return __( 'CEORI Latest Articles', 'ceori' );
	}

	public function get_icon() {
		return 'eicon-posts-grid';
	}

	protected function register_controls() {
		$this->heading_controls(
			array(
				'eyebrow' => 'Blog & insights',
				'title'   => '<em>Skin Care</em> Tips &amp; Advice',
				'lead'    => 'Simple tips, treatment guides and answers to common questions from our doctors.',
			)
		);
		$this->start_controls_section( 'content', array( 'label' => __( 'Articles', 'ceori' ) ) );
		$this->add_control(
			'count',
			array(
				'label'   => __( 'Number of articles', 'ceori' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 3,
				'min'     => 1,
				'max'     => 12,
			)
		);
		$this->add_control(
			'columns',
			array(
				'label'   => __( 'Columns', 'ceori' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '3',
				'options' => array(
					'2' => '2',
					'3' => '3',
					'4' => '4',
				),
			)
		);
		$this->add_control(
			'show_excerpt',
			array(
				'label'        => __( 'Show excerpt', 'ceori' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);
		$this->button_controls( 'button', __( 'Button text (leave empty to hide)', 'ceori' ), 'View All Blogs', home_url( '/blog/' ) );
		$this->end_controls_section();

		$this->photo_controls();
		$this->section_style( '.ceori-blog-section' );
		$this->heading_styles();
		$this->style_image( 'image', __( 'Images', 'ceori' ), '.ceori-post-card__media img' );
		$this->style_text( 'date', __( 'Dates', 'ceori' ), '.ceori-post-card__date' );
		$this->style_text( 'post_title', __( 'Titles', 'ceori' ), '.ceori-post-card__title, .ceori-post-card__title a' );
		$this->style_text( 'excerpt', __( 'Excerpts', 'ceori' ), '.ceori-post-card__excerpt' );
		$this->style_text( 'more', __( '“Read more” links', 'ceori' ), '.ceori-post-card .ceori-link-arrow' );
		$this->style_button( 'button', __( 'Button', 'ceori' ), '.ceori-section__head .wp-block-button__link' );
	}

	protected function render() {
		$s     = $this->get_settings_for_display();
		$posts = get_posts(
			array(
				'posts_per_page'      => max( 1, (int) $s['count'] ),
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
			)
		);
		echo '<section class="ceori-section ceori-blog-section"><div class="ceori-wrap">' . $this->head( $s, $this->buttons( $this->button( $s['button_text'], $s['button_link'] ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput
		echo '<div class="ceori-posts"><ul class="ceori-posts__grid ceori-grid ceori-grid--' . (int) $s['columns'] . '">';
		foreach ( $posts as $post ) {
			$url = get_permalink( $post );
			echo '<li class="ceori-post-card reveal"><a class="ceori-post-card__media" href="' . esc_url( $url ) . '" tabindex="-1" aria-hidden="true">' . ceori_card_image( $post->ID ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput
			echo '<time class="ceori-post-card__date" datetime="' . esc_attr( get_the_date( 'c', $post ) ) . '">' . esc_html( get_the_date( '', $post ) ) . '</time>';
			echo '<h3 class="ceori-post-card__title"><a href="' . esc_url( $url ) . '">' . esc_html( get_the_title( $post ) ) . '</a></h3>';
			if ( 'yes' === $s['show_excerpt'] ) {
				echo '<p class="ceori-post-card__excerpt">' . esc_html( wp_trim_words( get_the_excerpt( $post ), 18 ) ) . '</p>';
			}
			echo '<a class="ceori-link-arrow" href="' . esc_url( $url ) . '">' . esc_html__( 'Read more', 'ceori' ) . '<span class="screen-reader-text">: ' . esc_html( get_the_title( $post ) ) . '</span>' . ceori_icon( 'arrow-up-right' ) . '</a></li>'; // phpcs:ignore WordPress.Security.EscapeOutput
		}
		echo '</ul></div></div></section>';
	}
}

/**
 * Appointment call to action banner.
 */
class Cta extends Widget {

	public function get_name() {
		return 'ceori-cta';
	}

	public function get_title() {
		return __( 'CEORI Call to Action', 'ceori' );
	}

	public function get_icon() {
		return 'eicon-call-to-action';
	}

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => __( 'Call to action', 'ceori' ) ) );
		$this->add_control(
			'title',
			array(
				'label'   => __( 'Title', 'ceori' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 2,
				'default' => 'Ready for <em>Healthier Skin?</em>',
			)
		);
		$this->add_control(
			'text',
			array(
				'label'   => __( 'Text', 'ceori' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => 'Book a visit with our specialists in Calicut – we’ll help you find the right care.',
			)
		);
		$this->add_control(
			'show_call',
			array(
				'label'        => __( 'Show round call button (uses the clinic phone number)', 'ceori' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);
		$this->add_control(
			'call_text',
			array(
				'label'     => __( 'Call button label (read by screen readers)', 'ceori' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => 'Call Us',
				'condition' => array( 'show_call' => 'yes' ),
			)
		);
		$this->button_controls( 'button', __( 'Centre button text', 'ceori' ), 'Book an Appointment', home_url( '/contact-us/#appointment' ) );
		$this->add_control(
			'show_whatsapp',
			array(
				'label'        => __( 'Show round WhatsApp button', 'ceori' ),
				'description'  => __( 'Uses the WhatsApp number in CEORI Clinic settings, or the primary mobile number.', 'ceori' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'separator'    => 'before',
			)
		);
		$this->add_control(
			'whatsapp_message',
			array(
				'label'     => __( 'WhatsApp message', 'ceori' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => 'Hello CEORI, I would like to book an appointment.',
				'condition' => array( 'show_whatsapp' => 'yes' ),
			)
		);
		$this->end_controls_section();

		$this->style_box( 'box', __( 'Banner', 'ceori' ), '.ceori-cta' );
		$this->style_text( 'title', __( 'Title', 'ceori' ), '.ceori-cta h2' );
		$this->style_text( 'accent', __( 'Title – italic accent', 'ceori' ), '.ceori-cta h2 em' );
		$this->style_text( 'text', __( 'Text', 'ceori' ), '.ceori-cta .ceori-section__lead' );
		$this->style_button( 'button', __( 'Centre button', 'ceori' ), '.ceori-cta__book' );
		$this->style_button( 'call', __( 'Round call button', 'ceori' ), '.ceori-cta__round--call' );
		$this->style_button( 'whatsapp', __( 'Round WhatsApp button', 'ceori' ), '.ceori-cta__round--whatsapp' );
	}

	protected function render() {
		$s     = $this->get_settings_for_display();
		$phone = ceori_option( 'phone_primary' );
		$wa    = 'yes' === $s['show_whatsapp'] ? ceori_chat_whatsapp_href( (string) $s['whatsapp_message'] ) : '';
		$book  = ! empty( $s['button_link']['url'] ) ? $s['button_link']['url'] : home_url( '/contact-us/#appointment' );

		// Round call button – "Book an Appointment" pill – round WhatsApp button.
		$bar = '';
		if ( 'yes' === $s['show_call'] && $phone ) {
			$bar .= '<a class="ceori-cta__round ceori-cta__round--call" href="' . esc_url( ceori_tel_href( $phone ) ) . '" aria-label="' . esc_attr( trim( $s['call_text'] . ' ' . $phone ) ) . '" title="' . esc_attr( $phone ) . '">' . ceori_icon( 'phone' ) . '</a>';
		}
		$bar .= '<a class="ceori-cta__book" href="' . esc_url( $book ) . '"><span>' . esc_html( $s['button_text'] ) . '</span>' . ceori_icon( 'arrow-up-right' ) . '</a>';
		if ( $wa ) {
			$bar .= '<a class="ceori-cta__round ceori-cta__round--whatsapp" href="' . esc_url( $wa ) . '" target="_blank" rel="noopener" aria-label="' . esc_attr__( 'Chat with us on WhatsApp', 'ceori' ) . '" title="WhatsApp">' . ceori_icon( 'whatsapp' ) . '</a>';
		}

		echo '<section class="ceori-section ceori-cta-wrap" id="contact"><div class="ceori-wrap"><div class="ceori-cta reveal"><div class="ceori-cta__body">';
		echo '<h2>' . $this->inline( $s['title'] ) . '</h2><p class="ceori-section__lead">' . $this->inline( $s['text'] ) . '</p></div>'; // phpcs:ignore WordPress.Security.EscapeOutput
		echo '<div class="ceori-cta__bar">' . $bar . '</div></div></div></section>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}
}

/**
 * Treatment category cards (Treatments page, 404).
 */
class Category_Cards extends Widget {

	protected $photo_kind = 'category';

	public function get_name() {
		return 'ceori-category-cards';
	}

	public function get_title() {
		return __( 'CEORI Treatment Categories', 'ceori' );
	}

	public function get_icon() {
		return 'eicon-gallery-justified';
	}

	protected function register_controls() {
		$this->heading_controls(
			array(
				'title' => 'Care for Skin, Hair, <em>Smile</em> &amp; Body',
				'lead'  => 'Whether you need help with a skin problem, want a refreshing facial or are thinking about surgery, our doctors will help you find the right treatment. Pick a category to see everything we offer.',
			)
		);
		$this->start_controls_section( 'content', array( 'label' => __( 'Categories', 'ceori' ) ) );
		$this->add_control(
			'show_treatments',
			array(
				'label'        => __( 'List treatments inside each card', 'ceori' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);
		$this->end_controls_section();

		$this->photo_controls();
		$this->section_style( '.ceori-overview' );
		$this->heading_styles();
		$this->style_box( 'card', __( 'Cards', 'ceori' ), '.ceori-catcard' );
		$this->style_image( 'image', __( 'Card images', 'ceori' ), '.ceori-catcard__media img' );
		$this->style_text( 'cat_title', __( 'Category names', 'ceori' ), '.ceori-catcard__title, .ceori-catcard__title a' );
		$this->style_text( 'cat_text', __( 'Summaries', 'ceori' ), '.ceori-catcard__body > p' );
		$this->style_text( 'list', __( 'Treatment links', 'ceori' ), '.ceori-tlist__item a' );
		$this->style_button( 'button', __( 'Buttons', 'ceori' ), '.ceori-catcard .ceori-btn' );
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		echo '<section class="ceori-section ceori-section--tight ceori-overview"><div class="ceori-wrap">' . $this->head( $s ); // phpcs:ignore WordPress.Security.EscapeOutput
		echo ceori_render_treatment_categories( // phpcs:ignore WordPress.Security.EscapeOutput
			array(
				'layout'         => 'cards',
				'showTreatments' => 'yes' === $s['show_treatments'],
			)
		);
		echo '</div></section>';
	}
}

/**
 * Treatment cards grid (category pages).
 */
class Treatment_Grid extends Widget {

	protected $photo_kind = 'treatment';

	public function get_name() {
		return 'ceori-treatment-grid';
	}

	public function get_title() {
		return __( 'CEORI Treatment Cards', 'ceori' );
	}

	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	protected function register_controls() {
		$this->heading_controls( array( 'eyebrow' => 'About this speciality' ) );
		$this->start_controls_section( 'intro_section', array( 'label' => __( 'Introduction text', 'ceori' ) ) );
		$this->add_control(
			'text',
			array(
				'label' => __( 'Text', 'ceori' ),
				'type'  => Controls_Manager::WYSIWYG,
			)
		);
		$this->end_controls_section();
		$this->start_controls_section( 'content', array( 'label' => __( 'Treatments', 'ceori' ) ) );
		$this->add_control(
			'source',
			array(
				'label'       => __( 'Cards', 'ceori' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => '',
				'options'     => array(
					''       => __( 'Automatic – items below, then the category’s other treatments', 'ceori' ),
					'manual' => __( 'Only the items below', 'ceori' ),
				),
				'description' => __( 'Every card can be an item below. Pick its treatment page and change only what you want – empty fields use that page’s photo, title, description and link. Drag to reorder. In Automatic, treatments without an item (and new ones) are added after your items.', 'ceori' ),
			)
		);
		$this->card_items_control();
		$this->add_control(
			'category',
			array(
				'label'     => __( 'Category', 'ceori' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '',
				'options'   => category_options( __( 'This page’s category', 'ceori' ) ),
				'condition' => array( 'source' => '' ),
			)
		);
		$this->add_control(
			'slugs',
			array(
				'label'       => __( 'Or specific treatments (slugs, comma-separated)', 'ceori' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 2,
				'description' => __( 'Treatments added automatically: their featured image, title and excerpt, in page order.', 'ceori' ),
				'condition'   => array( 'source' => '' ),
			)
		);
		$this->add_control(
			'columns',
			array(
				'label'   => __( 'Columns', 'ceori' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '3',
				'options' => array(
					'2' => '2',
					'3' => '3',
					'4' => '4',
				),
			)
		);
		$this->add_control(
			'limit',
			array(
				'label'     => __( 'Maximum automatic cards (0 = all)', 'ceori' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 0,
				'min'       => 0,
				'condition' => array( 'source' => '' ),
			)
		);
		$this->end_controls_section();

		$this->photo_controls( array( 'source' => '' ) );
		$this->section_style( '.ceori-tgrid' );
		$this->heading_styles();
		$this->style_text( 'text', __( 'Introduction text', 'ceori' ), '.ceori-tgrid__text, .ceori-tgrid__text p' );
		$this->card_styles();
	}

	/**
	 * Card style controls (shared by every treatment card widget, so cards look and edit the same).
	 */
	protected function card_styles() {
		$this->style_image( 'image', __( 'Card images', 'ceori' ), '.ceori-card__media img' );
		$this->style_text( 'card_title', __( 'Card titles', 'ceori' ), '.ceori-card__title, .ceori-card__title a' );
		$this->style_text( 'card_text', __( 'Card text', 'ceori' ), '.ceori-card__text' );
		$this->style_text( 'card_link', __( 'Card links', 'ceori' ), '.ceori-card .ceori-link-arrow' );
	}

	/**
	 * "Card items" repeater: each card's treatment page, photo, title, description, link and link text.
	 */
	protected function card_items_control() {
		$card = new Repeater();
		$card->add_control(
			'card_page',
			array(
				'label'       => __( 'Treatment page', 'ceori' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array( '' => __( '— None (custom card) —', 'ceori' ) ) + self::treatment_page_options(),
				'default'     => '',
				'label_block' => true,
			)
		);
		$card->add_control(
			'card_image',
			array(
				'label'       => __( 'Photo', 'ceori' ),
				'type'        => Controls_Manager::MEDIA,
				'description' => __( 'Empty = the treatment page’s featured image.', 'ceori' ),
			)
		);
		$card->add_control(
			'card_title',
			array(
				'label'       => __( 'Title', 'ceori' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'placeholder' => __( 'Empty = the page title', 'ceori' ),
			)
		);
		$card->add_control(
			'card_text',
			array(
				'label'       => __( 'Description', 'ceori' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 3,
				'placeholder' => __( 'Empty = the page summary (excerpt)', 'ceori' ),
			)
		);
		$card->add_control(
			'card_link',
			array(
				'label'       => __( 'Link', 'ceori' ),
				'type'        => Controls_Manager::URL,
				'description' => __( 'Empty = the treatment page.', 'ceori' ),
			)
		);
		$card->add_control(
			'card_button',
			array(
				'label'   => __( 'Link text', 'ceori' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Learn more', 'ceori' ),
			)
		);
		$this->add_control(
			'cards',
			array(
				'label'         => __( 'Card items', 'ceori' ),
				'type'          => Controls_Manager::REPEATER,
				'fields'        => $card->get_controls(),
				'default'       => array(),
				'prevent_empty' => false,
				'title_field'   => '<# var ceoriPages = ' . wp_json_encode( (object) self::treatment_page_options() ) . '; #>{{ card_title || ceoriPages[ card_page ] || "' . esc_js( __( 'Custom card', 'ceori' ) ) . '" }}',
			)
		);
	}

	/**
	 * Treatment pages for the "Treatment page" picker, keyed by page ID.
	 *
	 * @return array
	 */
	protected static function treatment_page_options() {
		static $options = null;
		if ( null === $options ) {
			$options = array();
			foreach ( ceori_get_treatments( '', 0 ) as $p ) {
				$options[ (string) $p->ID ] = html_entity_decode( get_the_title( $p ), ENT_QUOTES );
			}
			asort( $options );
		}
		return $options;
	}

	/**
	 * One card item: its own photo, title, description and link, each falling back to the
	 * chosen treatment page.
	 *
	 * @param array $c Repeater row.
	 * @return string
	 */
	protected function item_card( $c ) {
		$page  = ! empty( $c['card_page'] ) ? get_post( (int) $c['card_page'] ) : null;
		$page  = $page && 'publish' === $page->post_status ? $page : null;
		$title = trim( (string) $c['card_title'] );
		$title = '' !== $title ? $title : ( $page ? get_the_title( $page ) : '' );
		$text  = trim( (string) $c['card_text'] );
		if ( '' === $text && $page ) {
			$text = has_excerpt( $page ) ? get_the_excerpt( $page ) : wp_trim_words( wp_strip_all_tags( $page->post_content ), 20 );
		}
		$url   = ! empty( $c['card_link']['url'] ) ? $c['card_link']['url'] : ( $page ? get_permalink( $page ) : '' );
		$ext   = ! empty( $c['card_link']['url'] ) && ! empty( $c['card_link']['is_external'] ) ? ' target="_blank" rel="noopener"' : '';
		$sizes = array( 'sizes' => '(max-width: 599px) 88px, (max-width: 1023px) 46vw, 400px' );
		$img   = $this->img( $c['card_image'], 'ceori-card', $sizes + array( 'alt' => $title ) );
		if ( ! $img ) {
			$img = $page ? ceori_card_image( $page->ID, 'ceori-card', $sizes ) : '<span class="ceori-card__placeholder" aria-hidden="true">' . ceori_icon( 'sparkle' ) . '</span>';
		}
		$html  = '<article class="ceori-card ceori-card--grid reveal elementor-repeater-item-' . esc_attr( $c['_id'] ) . '">';
		$html .= $url ? '<a class="ceori-card__media" href="' . esc_url( $url ) . '" tabindex="-1" aria-hidden="true"' . $ext . '>' . $img . '</a>' : '<div class="ceori-card__media">' . $img . '</div>';
		$html .= '<div class="ceori-card__body"><h3 class="ceori-card__title">' . ( $url ? '<a href="' . esc_url( $url ) . '"' . $ext . '>' . esc_html( $title ) . '</a>' : esc_html( $title ) ) . '</h3>';
		$html .= $text ? '<p class="ceori-card__text">' . esc_html( $text ) . '</p>' : '';
		if ( $url && $c['card_button'] ) {
			$html .= '<a class="ceori-link-arrow" href="' . esc_url( $url ) . '"' . $ext . '>' . ceori_link_text( $c['card_button'], $title ) . ceori_icon( 'arrow-up-right' ) . '</a>';
		}
		return $html . '</div></article>';
	}

	protected function render() {
		$s       = $this->get_settings_for_display();
		$items   = (array) $s['cards'];
		$cards   = '';
		$covered = array();
		foreach ( $items as $c ) {
			$cards .= $this->item_card( $c );
			// A treatment counts as shown when an item picks it or links straight to it.
			if ( ! empty( $c['card_page'] ) ) {
				$covered[ (int) $c['card_page'] ] = true;
			} elseif ( ! empty( $c['card_link']['url'] ) ) {
				$id = url_to_postid( $c['card_link']['url'] );
				if ( $id ) {
					$covered[ $id ] = true;
				}
			}
		}
		if ( 'manual' !== $s['source'] ) {
			$category = $s['category'];
			if ( ! $category && ! trim( (string) $s['slugs'] ) ) {
				$term     = ceori_current_category( $this->page_id() );
				$category = $term ? $term->slug : '';
			}
			$posts = trim( (string) $s['slugs'] )
				? array_filter( array_map( fn( $x ) => get_page_by_path( sanitize_title( trim( $x ) ) ), explode( ',', $s['slugs'] ) ) )
				: ceori_get_treatments( $category, 0 );
			$added = 0;
			foreach ( $posts as $post ) {
				if ( isset( $covered[ $post->ID ] ) || 'publish' !== $post->post_status ) {
					continue;
				}
				if ( (int) $s['limit'] > 0 && $added >= (int) $s['limit'] ) {
					break;
				}
				$cards .= ceori_treatment_card( $post, 'grid', 'ceori-card', array( 'sizes' => '(max-width: 599px) 88px, (max-width: 1023px) 46vw, 400px' ) ); // Phones show an 88px thumbnail.
				++$added;
			}
		}
		$html = $cards ? '<div class="ceori-grid ceori-grid--' . max( 2, min( 4, (int) $s['columns'] ) ) . '">' . $cards . '</div>' : '';
		echo '<section class="ceori-section ceori-tgrid"><div class="ceori-wrap">' . $this->head( $s ); // phpcs:ignore WordPress.Security.EscapeOutput
		if ( ! empty( $s['text'] ) ) {
			echo '<div class="ceori-tgrid__text">' . wp_kses_post( $s['text'] ) . '</div>';
		}
		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput
		if ( ! $html ) {
			$this->editor_notice( __( 'No published treatment pages match these settings yet.', 'ceori' ) );
		}
		echo '</div></section>';
	}
}

/**
 * Overlapping specialist photos with the count and a caption (home hero), from Specialists.
 */
class Specialist_Avatars extends Widget {

	public function get_name() {
		return 'ceori-specialist-avatars';
	}

	public function get_title() {
		return __( 'CEORI Specialist Photos', 'ceori' );
	}

	public function get_icon() {
		return 'eicon-person';
	}

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => __( 'Specialist photos', 'ceori' ) ) );
		$this->add_control(
			'note',
			array(
				'type' => Controls_Manager::RAW_HTML,
				'raw'  => __( 'Shows the first three photos under <strong>Specialists</strong> in the dashboard and how many specialists there are.', 'ceori' ),
			)
		);
		$this->add_control(
			'caption',
			array(
				'label'   => __( 'Text beside the photos', 'ceori' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Specialists Who Care',
			)
		);
		$this->add_control(
			'category',
			array(
				'label'   => __( 'Show', 'ceori' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => category_options( __( 'All specialists', 'ceori' ) ),
			)
		);
		$this->end_controls_section();
		$this->style_image( 'photos', __( 'Photos', 'ceori' ), '.ceori-avatars__stack img' );
		$this->style_text( 'count', __( 'Number', 'ceori' ), '.ceori-avatars__text strong' );
		$this->style_text( 'caption', __( 'Text', 'ceori' ), '.ceori-avatars__text' );
	}

	protected function render() {
		$s    = $this->get_settings_for_display();
		$html = ceori_render_doctors(
			array(
				'layout'   => 'avatars',
				'limit'    => 3, // Same as the hero widget: three photos, total count of specialists.
				'caption'  => $s['caption'],
				'category' => $s['category'],
			)
		);
		echo $html ? $html : ''; // phpcs:ignore WordPress.Security.EscapeOutput
		if ( ! $html ) {
			$this->editor_notice( __( 'Add specialists under Specialists in the dashboard.', 'ceori' ) );
		}
	}
}

/**
 * Related treatments.
 */
class Related extends Treatment_Grid {

	protected $photo_kind = 'treatment';

	public function get_name() {
		return 'ceori-related';
	}

	public function get_title() {
		return __( 'CEORI Related Treatments', 'ceori' );
	}

	public function get_icon() {
		return 'eicon-product-related';
	}

	protected function register_controls() {
		$this->heading_controls(
			array(
				'eyebrow' => 'You may also like',
				'title'   => 'Related <em>Treatments</em>',
			)
		);
		$this->start_controls_section( 'content', array( 'label' => __( 'Related treatments', 'ceori' ) ) );
		$this->add_control(
			'source',
			array(
				'label'       => __( 'Cards', 'ceori' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => '',
				'options'     => array(
					''       => __( 'Automatic – items below, then related treatments', 'ceori' ),
					'manual' => __( 'Only the items below', 'ceori' ),
				),
				'description' => __( 'Each card can be an item below: pick its treatment page and change only what you want – empty fields use that page’s photo, title, description and link. Drag to reorder.', 'ceori' ),
			)
		);
		$this->card_items_control();
		$this->add_control(
			'limit',
			array(
				'label'       => __( 'Number of cards', 'ceori' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 3,
				'min'         => 1,
				'max'         => 12,
				'description' => __( 'In Automatic, related treatments fill up to this number after your items.', 'ceori' ),
				'condition'   => array( 'source' => '' ),
			)
		);
		$this->add_control(
			'columns',
			array(
				'label'   => __( 'Columns', 'ceori' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '3',
				'options' => array(
					'2' => '2',
					'3' => '3',
					'4' => '4',
				),
			)
		);
		$this->button_controls( 'button', __( 'Button text (leave empty to hide)', 'ceori' ), 'View All Treatments', home_url( '/treatments/' ) );
		$this->end_controls_section();

		$this->photo_controls( array( 'source' => '' ) );
		$this->section_style( '.ceori-related' );
		$this->heading_styles();
		$this->card_styles();
		$this->style_button( 'button', __( 'Button', 'ceori' ), '.ceori-section__head .wp-block-button__link' );
	}

	protected function render() {
		$s       = $this->get_settings_for_display();
		$cards   = '';
		$covered = array();
		foreach ( (array) $s['cards'] as $c ) {
			$cards .= $this->item_card( $c );
			$id     = ! empty( $c['card_page'] ) ? (int) $c['card_page'] : ( ! empty( $c['card_link']['url'] ) ? url_to_postid( $c['card_link']['url'] ) : 0 );
			if ( $id ) {
				$covered[] = $id;
			}
		}
		if ( 'manual' !== $s['source'] ) {
			$page_id = ceori_is_editor_preview() && isset( $_GET['post_id'] ) ? absint( $_GET['post_id'] ) : $this->page_id(); // phpcs:ignore WordPress.Security.NonceVerification
			$need    = max( 0, (int) $s['limit'] - count( (array) $s['cards'] ) );
			foreach ( ceori_related_treatment_posts( $page_id, $need, $covered ) as $post ) {
				$cards .= ceori_treatment_card( $post, 'grid', 'ceori-card', array( 'sizes' => '(max-width: 599px) 88px, (max-width: 1023px) 46vw, 400px' ) );
			}
		}
		echo '<section class="ceori-section ceori-related"><div class="ceori-wrap">' . $this->head( $s, $this->buttons( $this->button( $s['button_text'], $s['button_link'], 'outline' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput
		if ( $cards ) {
			echo '<div class="ceori-grid ceori-grid--' . max( 2, min( 4, (int) $s['columns'] ) ) . '">' . $cards . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput
		} else {
			$this->editor_notice( __( 'Related treatments appear here on published treatment pages.', 'ceori' ) );
		}
		echo '</div></section>';
	}
}

/**
 * Contact details, appointment form and map.
 */
class Contact extends Widget {

	public function get_name() {
		return 'ceori-contact';
	}

	public function get_title() {
		return __( 'CEORI Contact & Appointment Form', 'ceori' );
	}

	public function get_icon() {
		return 'eicon-form-horizontal';
	}

	protected function register_controls() {
		$this->heading_controls(
			array(
				'eyebrow' => 'Get in touch',
				'title'   => 'We’re Here to <em>Help</em>',
				'lead'    => 'Want an appointment, have a question, or not sure where to start? Call, email or send the form, and our team will help.',
			)
		);
		$this->start_controls_section( 'content', array( 'label' => __( 'Details & form', 'ceori' ) ) );
		$this->add_control(
			'note',
			array(
				'type' => Controls_Manager::RAW_HTML,
				'raw'  => __( 'Phone, email, address, hours and social links come from <strong>CEORI Clinic</strong> settings.', 'ceori' ),
			)
		);
		$this->add_control(
			'card_title',
			array(
				'label'   => __( 'Contact card title', 'ceori' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'CEORI Skin Clinic',
			)
		);
		$this->add_control(
			'show_hours',
			array(
				'label'        => __( 'Show opening hours & social links', 'ceori' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);
		$this->add_control(
			'form_heading',
			array(
				'label'     => __( 'Form heading', 'ceori' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => 'Request an appointment',
				'separator' => 'before',
			)
		);
		$this->add_control(
			'form_note',
			array(
				'label'   => __( 'Text under the form heading', 'ceori' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => 'Leave a few details and we will call you to fix a time. Your appointment is confirmed only when we call you back.',
			)
		);
		$this->add_control(
			'show_map',
			array(
				'label'        => __( 'Show Google map', 'ceori' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'separator'    => 'before',
			)
		);
		$this->end_controls_section();

		$this->section_style( '.ceori-contact' );
		$this->heading_styles();
		$this->style_box( 'info_card', __( 'Information cards', 'ceori' ), '.ceori-info-card' );
		$this->style_text( 'info_title', __( 'Card titles', 'ceori' ), '.ceori-info-card__title' );
		$this->style_text( 'info_text', __( 'Contact details', 'ceori' ), '.ceori-contact-list, .ceori-contact-list a, .ceori-hours' );
		$this->style_box( 'form_card', __( 'Form card', 'ceori' ), '.ceori-form-card' );
		$this->style_text( 'form_title', __( 'Form heading', 'ceori' ), '.ceori-form-card__title' );
		$this->style_text( 'form_label', __( 'Form labels', 'ceori' ), '.ceori-field label' );
		$this->style_box( 'field', __( 'Form fields', 'ceori' ), '.ceori-field input:not([type=checkbox]), .ceori-field select, .ceori-field textarea' );
		$this->style_button( 'submit', __( 'Submit button', 'ceori' ), '.ceori-form__footer .ceori-btn' );
		$this->style_box( 'map', __( 'Map', 'ceori' ), '.ceori-map' );
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		echo '<section class="ceori-section ceori-contact" id="appointment"><div class="ceori-wrap"><div class="wp-block-columns is-layout-flex ceori-contact__cols">';
		echo '<div class="wp-block-column ceori-contact__info" style="flex-basis:40%">' . $this->head( $s ); // phpcs:ignore WordPress.Security.EscapeOutput
		echo '<div class="ceori-info-card"><h3 class="ceori-info-card__title">' . esc_html( $s['card_title'] ) . '</h3>' . ceori_render_clinic_info( array( 'field' => 'contact' ) ) . ceori_render_clinic_info( array( 'field' => 'call' ) ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput
		if ( 'yes' === $s['show_hours'] ) {
			echo '<div class="ceori-info-card"><h3 class="ceori-info-card__title">' . esc_html__( 'Opening hours', 'ceori' ) . '</h3>' . ceori_render_clinic_info( array( 'field' => 'hours' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
			echo '<h3 class="ceori-info-card__title">' . esc_html__( 'Follow us', 'ceori' ) . '</h3><ul class="ceori-social">' . ceori_social_items() . '</ul></div>'; // phpcs:ignore WordPress.Security.EscapeOutput
		}
		echo '</div><div class="wp-block-column ceori-contact__form">';
		echo ceori_render_appointment_form( // phpcs:ignore WordPress.Security.EscapeOutput
			array(
				'heading' => $s['form_heading'],
				'note'    => $s['form_note'],
			)
		);
		echo '</div></div>';
		if ( 'yes' === $s['show_map'] ) {
			echo ceori_render_clinic_info( array( 'field' => 'map' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
		}
		echo '</div></section>';
	}
}

/**
 * Clinic info from CEORI Clinic settings (footer, sidebars).
 */
class Clinic_Info extends Widget {

	public function get_name() {
		return 'ceori-clinic-info';
	}

	public function get_title() {
		return __( 'CEORI Clinic Info', 'ceori' );
	}

	public function get_icon() {
		return 'eicon-map-pin';
	}

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => __( 'Clinic info', 'ceori' ) ) );
		$this->add_control(
			'field',
			array(
				'label'   => __( 'Show', 'ceori' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'contact',
				'options' => array(
					'contact'   => __( 'Phone, email & address list', 'ceori' ),
					'phone'     => __( 'Phone numbers', 'ceori' ),
					'email'     => __( 'Email', 'ceori' ),
					'address'   => __( 'Address with map link', 'ceori' ),
					'hours'     => __( 'Opening hours', 'ceori' ),
					'social'    => __( 'Social media icons', 'ceori' ),
					'map'       => __( 'Embedded Google map', 'ceori' ),
					'about'     => __( 'Clinic introduction text', 'ceori' ),
					'call'      => __( 'Call button', 'ceori' ),
					'copyright' => __( 'Copyright line', 'ceori' ),
				),
			)
		);
		$this->add_control(
			'note',
			array(
				'type' => Controls_Manager::RAW_HTML,
				'raw'  => __( 'Edit the details themselves under <strong>CEORI Clinic</strong> in the dashboard – they update everywhere at once.', 'ceori' ),
			)
		);
		$this->end_controls_section();

		$this->style_text( 'text', __( 'Text & links', 'ceori' ), '.ceori-contact-list, .ceori-contact-list a, .ceori-hours, .ceori-info, .ceori-info a, .ceori-info-about, .ceori-copyright', array( 'align' => true ) );
		$this->start_controls_section(
			'icon_style',
			array(
				'label' => __( 'Icons', 'ceori' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_control(
			'icon_color',
			array(
				'label'     => __( 'Icon colour', 'ceori' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .ceori-icon' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_responsive_control(
			'icon_size',
			array(
				'label'      => __( 'Icon size', 'ceori' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array( '{{WRAPPER}} .ceori-icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'row_gap',
			array(
				'label'      => __( 'Space between rows', 'ceori' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array( '{{WRAPPER}} .ceori-contact-list, {{WRAPPER}} .ceori-hours' => 'gap: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->end_controls_section();
		$this->style_button( 'social', __( 'Social icons / call button', 'ceori' ), '.ceori-social a, .ceori-call .ceori-btn' );
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		echo ceori_render_clinic_info( array( 'field' => $s['field'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput
	}
}

/**
 * Header hamburger + side panel.
 */
class Side_Panel extends Widget {

	public function get_name() {
		return 'ceori-side-panel';
	}

	public function get_title() {
		return __( 'CEORI Side Panel Menu', 'ceori' );
	}

	public function get_icon() {
		return 'eicon-menu-bar';
	}

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => __( 'Side panel', 'ceori' ) ) );
		$this->add_control(
			'note',
			array(
				'type' => Controls_Manager::RAW_HTML,
				'raw'  => __( 'Opens a panel with the main menu (phones/tablets), treatment links, address, contact details, social links and the appointment button. Menu: <strong>Appearance → Menus</strong>. Details: <strong>CEORI Clinic</strong> settings.', 'ceori' ),
			)
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'toggle_style',
			array(
				'label' => __( 'Hamburger icon', 'ceori' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_control(
			'toggle_color',
			array(
				'label'     => __( 'Colour', 'ceori' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .ceori-offcanvas__toggle' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'toggle_hover',
			array(
				'label'     => __( 'Hover colour', 'ceori' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .ceori-offcanvas__toggle:hover' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_responsive_control(
			'toggle_width',
			array(
				'label'      => __( 'Width', 'ceori' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors'  => array( '{{WRAPPER}} .ceori-burger' => 'width: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->end_controls_section();
		$this->style_box( 'panel', __( 'Panel', 'ceori' ), '.ceori-offcanvas__panel' );
		$this->style_text( 'panel_title', __( 'Panel headings', 'ceori' ), '.ceori-offcanvas__title' );
		$this->style_text( 'panel_text', __( 'Panel text & links', 'ceori' ), '.ceori-offcanvas__about, .ceori-offcanvas__links a, .ceori-offcanvas__group p a' );
		$this->style_button( 'panel_button', __( 'Panel button', 'ceori' ), '.ceori-offcanvas__cta' );
	}

	protected function render() {
		echo ceori_render_offcanvas(); // phpcs:ignore WordPress.Security.EscapeOutput
	}
}

/**
 * Breadcrumbs.
 */
class Breadcrumbs extends Widget {

	public function get_name() {
		return 'ceori-breadcrumbs';
	}

	public function get_title() {
		return __( 'CEORI Breadcrumbs', 'ceori' );
	}

	public function get_icon() {
		return 'eicon-navigation-horizontal';
	}

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => __( 'Breadcrumbs', 'ceori' ) ) );
		$this->add_control(
			'note',
			array(
				'type' => Controls_Manager::RAW_HTML,
				'raw'  => __( 'Generated automatically from the page’s position on the site.', 'ceori' ),
			)
		);
		$this->end_controls_section();
		$this->style_text( 'crumbs', __( 'Breadcrumbs', 'ceori' ), '.ceori-breadcrumbs ol, .ceori-breadcrumbs a' );
		$this->style_text( 'current', __( 'Current page', 'ceori' ), '.ceori-breadcrumbs [aria-current]' );
	}

	protected function render() {
		echo ceori_breadcrumb_html(); // phpcs:ignore WordPress.Security.EscapeOutput
	}
}

/**
 * Clinic equipment, from Equipment in the dashboard.
 */
class Equipment extends Widget {

	protected $photo_kind = 'equipment';

	public function get_name() {
		return 'ceori-equipment';
	}

	public function get_title() {
		return __( 'CEORI Equipment', 'ceori' );
	}

	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	protected function register_controls() {
		$this->heading_controls(
			array(
				'eyebrow' => 'Our equipment',
				'title'   => 'The <em>Technology</em> Behind Your Care',
				'lead'    => 'Modern devices, used by trained specialists after a careful check of your skin.',
			)
		);
		$this->start_controls_section( 'content', array( 'label' => __( 'Equipment', 'ceori' ) ) );
		$this->add_control(
			'note',
			array(
				'type' => Controls_Manager::RAW_HTML,
				'raw'  => __( 'Photos, names and descriptions are managed under <strong>Equipment</strong> in the dashboard (drag order with “Order”).', 'ceori' ),
			)
		);
		$this->add_control(
			'columns',
			array(
				'label'   => __( 'Columns', 'ceori' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '3',
				'options' => array(
					'2' => '2',
					'3' => '3',
					'4' => '4',
				),
			)
		);
		$this->add_control(
			'limit',
			array(
				'label'   => __( 'Maximum (0 = all)', 'ceori' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 0,
				'min'     => 0,
			)
		);
		$this->add_control(
			'link_text',
			array(
				'label'   => __( 'Link text (to the treatment page)', 'ceori' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'See the treatment',
			)
		);
		$this->end_controls_section();

		$this->photo_controls();
		$this->section_style( '.ceori-equipment' );
		$this->heading_styles();
		$this->style_box( 'card', __( 'Cards', 'ceori' ), '.ceori-equip' );
		$this->style_image( 'photo', __( 'Photos', 'ceori' ), '.ceori-equip__media img' );
		$this->style_text( 'used', __( 'Used for', 'ceori' ), '.ceori-equip__used' );
		$this->style_text( 'name', __( 'Names', 'ceori' ), '.ceori-equip__name' );
		$this->style_text( 'text', __( 'Descriptions', 'ceori' ), '.ceori-equip__text' );
		$this->style_text( 'link', __( 'Links', 'ceori' ), '.ceori-equip .ceori-link-arrow' );
	}

	protected function render() {
		$s     = $this->get_settings_for_display();
		$items = get_posts(
			array(
				'post_type'      => 'ceori_equipment',
				'post_status'    => 'publish',
				'posts_per_page' => (int) $s['limit'] > 0 ? (int) $s['limit'] : -1,
				'orderby'        => array(
					'menu_order' => 'ASC',
					'title'      => 'ASC',
				),
			)
		);
		echo '<section class="ceori-section ceori-equipment"><div class="ceori-wrap">' . $this->head( $s ); // phpcs:ignore WordPress.Security.EscapeOutput
		if ( ! $items ) {
			$this->editor_notice( __( 'Add equipment under Equipment in the dashboard.', 'ceori' ) );
			echo '</div></section>';
			return;
		}
		echo '<ul class="ceori-grid ceori-grid--' . (int) $s['columns'] . ' ceori-equipment__grid">';
		foreach ( $items as $item ) {
			$used = (string) get_post_meta( $item->ID, 'used_for', true );
			$text = (string) get_post_meta( $item->ID, 'summary', true );
			$page = (int) get_post_meta( $item->ID, 'treatment_page', true );
			$url  = $page && 'publish' === get_post_status( $page ) ? get_permalink( $page ) : '';
			echo '<li class="ceori-equip reveal"><div class="ceori-equip__media">' . ceori_card_image( $item->ID, 'ceori-card', array( 'sizes' => '(max-width: 599px) 92vw, (max-width: 1023px) 46vw, 400px' ) ) . '</div><div class="ceori-equip__body">'; // phpcs:ignore WordPress.Security.EscapeOutput
			echo $used ? '<p class="ceori-equip__used">' . esc_html( $used ) . '</p>' : '';
			echo '<h3 class="ceori-equip__name">' . esc_html( get_the_title( $item ) ) . '</h3>';
			echo $text ? '<p class="ceori-equip__text">' . esc_html( $text ) . '</p>' : '';
			if ( $url && $s['link_text'] ) {
				echo '<a class="ceori-link-arrow" href="' . esc_url( $url ) . '">' . esc_html( $s['link_text'] ) . '<span class="screen-reader-text">: ' . esc_html( get_the_title( $page ) ) . '</span>' . ceori_icon( 'arrow-up-right' ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput
			}
			echo '</div></li>';
		}
		echo '</ul></div></section>';
	}
}
