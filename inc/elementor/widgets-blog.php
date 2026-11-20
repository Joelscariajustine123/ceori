<?php
/**
 * Blog and site-template widgets: Blog banner, Article list, Article header, Article text,
 * Related articles and Page not found. With these the Blog page, every article and the
 * 404, search and archive templates are built from Elementor sections like the rest of the site.
 *
 * @package ceori
 */

namespace Ceori\Elementor;

use Elementor\Controls_Manager;

defined( 'ABSPATH' ) || exit;

/**
 * Search form markup (core search block, so it matches the rest of the site).
 *
 * @param string $placeholder Placeholder.
 * @param string $class       Extra class.
 * @return string
 */
function search_form( $placeholder, $class = '' ) {
	$attrs = array(
		'label'         => __( 'Search the website', 'ceori' ),
		'showLabel'     => false,
		'placeholder'   => $placeholder,
		'buttonText'    => __( 'Search', 'ceori' ),
		'buttonUseIcon' => true,
		'className'     => trim( 'ceori-search ' . $class ),
	);
	return do_blocks( '<!-- wp:search ' . wp_json_encode( $attrs ) . ' /-->' );
}

/**
 * Blog / archive / search banner.
 */
class Blog_Banner extends Widget {

	public function get_name() {
		return 'ceori-blog-banner';
	}

	public function get_title() {
		return __( 'CEORI Blog Banner', 'ceori' );
	}

	public function get_icon() {
		return 'eicon-header';
	}

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => __( 'Banner', 'ceori' ) ) );
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
				'default'     => 'The CEORI Journal',
				'label_block' => true,
			)
		);
		$this->add_control(
			'title',
			array(
				'label'       => __( 'Title', 'ceori' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 2,
				'default'     => 'Skin Care Tips &amp; <em>Expert Insights</em>',
				'description' => __( 'The page’s main heading (H1). &lt;em&gt;…&lt;/em&gt; = italic accent.', 'ceori' ),
			)
		);
		$this->add_control(
			'auto_title',
			array(
				'label'        => __( 'On category and search pages, show their own title instead', 'ceori' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);
		$this->add_control(
			'text',
			array(
				'label'   => __( 'Introduction', 'ceori' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => 'Guides, treatment explainers and everyday advice from the specialists at CEORI Skin Clinic, Calicut.',
			)
		);
		$this->add_control(
			'show_count',
			array(
				'label'        => __( 'Show number of articles (or results)', 'ceori' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);
		$this->add_control(
			'show_search',
			array(
				'label'        => __( 'Show search box', 'ceori' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'separator'    => 'before',
			)
		);
		$this->add_control(
			'search_title',
			array(
				'label'     => __( 'Search box heading', 'ceori' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => 'Looking for something?',
				'condition' => array( 'show_search' => 'yes' ),
			)
		);
		$this->add_control(
			'placeholder',
			array(
				'label'     => __( 'Search placeholder', 'ceori' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => 'Search treatments, doctors, articles…',
				'condition' => array( 'show_search' => 'yes' ),
			)
		);
		$this->add_control(
			'topics',
			array(
				'label'       => __( 'Popular topics (one per line)', 'ceori' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => "Acne\nHair fall\nHydraFacial\nBotox\nPigmentation",
				'description' => __( 'Each topic opens the search results for that word. Leave empty to hide.', 'ceori' ),
				'condition'   => array( 'show_search' => 'yes' ),
			)
		);
		$this->add_control(
			'show_categories',
			array(
				'label'       => __( 'Show article categories', 'ceori' ),
				'description' => __( 'Shown only when there are at least two categories.', 'ceori' ),
				'type'        => Controls_Manager::SWITCHER,
				'default'     => 'yes',
				'return_value' => 'yes',
			)
		);
		$this->end_controls_section();

		$this->style_box( 'frame', __( 'Banner box', 'ceori' ), '.ceori-banner__frame' );
		$this->style_text( 'crumbs', __( 'Breadcrumbs', 'ceori' ), '.ceori-breadcrumbs ol, .ceori-breadcrumbs a' );
		$this->style_text( 'eyebrow', __( 'Small label', 'ceori' ), '.ceori-eyebrow' );
		$this->style_text( 'title', __( 'Title', 'ceori' ), '.ceori-banner__title' );
		$this->style_text( 'accent', __( 'Title – italic accent', 'ceori' ), '.ceori-banner__title em' );
		$this->style_text( 'text', __( 'Introduction', 'ceori' ), '.ceori-banner__lead' );
		$this->style_text( 'count', __( 'Number of articles', 'ceori' ), '.ceori-banner__meta' );
		$this->style_box( 'card', __( 'Search card', 'ceori' ), '.ceori-searchcard' );
		$this->style_text( 'card_title', __( 'Search card heading', 'ceori' ), '.ceori-searchcard__title' );
		$this->style_box( 'search', __( 'Search box', 'ceori' ), '.ceori-search .wp-block-search__inside-wrapper' );
		$this->style_button( 'pill', __( 'Topic and category links', 'ceori' ), '.ceori-topics a, .ceori-cat-pills a' );
	}

	protected function render() {
		global $wp_query;
		$s      = $this->get_settings_for_display();
		$title  = $this->inline( $s['title'] );
		$search = is_search() && ! $this->is_editor();
		if ( 'yes' === $s['auto_title'] ) {
			if ( $search ) {
				/* translators: %s: search query */
				$title = sprintf( esc_html__( 'Results for %s', 'ceori' ), '<em>“' . esc_html( get_search_query() ) . '”</em>' );
			} elseif ( is_archive() ) {
				$title = esc_html( wp_strip_all_tags( get_the_archive_title() ) );
			}
		}

		// Count line: articles on the blog, results on the search page.
		$count = '';
		if ( 'yes' === $s['show_count'] ) {
			if ( $search ) {
				$n = (int) $wp_query->found_posts;
				/* translators: %s: number of results */
				$count = sprintf( _n( '%s result', '%s results', $n, 'ceori' ), number_format_i18n( $n ) );
			} else {
				$n = (int) wp_count_posts()->publish;
				/* translators: %s: number of articles */
				$count = sprintf( _n( '%s article', '%s articles', $n, 'ceori' ), number_format_i18n( $n ) ) . '<span aria-hidden="true">·</span>' . esc_html__( 'Written by our specialists', 'ceori' );
			}
		}

		$tools = '';
		if ( 'yes' === $s['show_search'] ) {
			$tools .= $s['search_title'] ? '<p class="ceori-searchcard__title">' . ceori_icon( 'search' ) . esc_html( $s['search_title'] ) . '</p>' : '';
			$tools .= search_form( (string) $s['placeholder'] );
			$topics = $this->lines( $s['topics'] );
			if ( $topics ) {
				$tools .= '<div class="ceori-topics"><span class="ceori-topics__label">' . esc_html__( 'Popular:', 'ceori' ) . '</span><ul>';
				foreach ( $topics as $topic ) {
					$tools .= '<li><a href="' . esc_url( add_query_arg( 's', rawurlencode( $topic ), home_url( '/' ) ) ) . '">' . esc_html( $topic ) . '</a></li>';
				}
				$tools .= '</ul></div>';
			}
		}
		if ( 'yes' === $s['show_categories'] && count( get_categories( array( 'hide_empty' => true ) ) ) > 1 ) {
			$tools .= '<ul class="wp-block-categories-list ceori-cat-pills">' . wp_list_categories(
				array(
					'title_li'   => '',
					'echo'       => false,
					'show_count' => false,
				)
			) . '</ul>';
		}

		echo '<section class="ceori-banner ceori-banner--blog"><div class="ceori-banner__frame has-cream-background-color has-background"><div class="ceori-banner__body">';
		if ( 'yes' === $s['show_breadcrumbs'] ) {
			echo ceori_breadcrumb_html(); // phpcs:ignore WordPress.Security.EscapeOutput
		}
		if ( $s['eyebrow'] && ! $search ) {
			echo '<p class="ceori-eyebrow">' . esc_html( $s['eyebrow'] ) . '</p>';
		}
		echo '<h1 class="ceori-banner__title">' . $title . '</h1>'; // phpcs:ignore WordPress.Security.EscapeOutput
		if ( $s['text'] && ! $search ) {
			echo '<p class="ceori-banner__lead">' . $this->inline( $s['text'] ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput
		}
		echo $count ? '<p class="ceori-banner__meta">' . $count . '</p>' : ''; // phpcs:ignore WordPress.Security.EscapeOutput
		echo '</div>' . ( $tools ? '<div class="ceori-banner__tools ceori-searchcard">' . $tools . '</div>' : '' ) . '</div></section>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}
}

/**
 * Article list with pagination: all articles on a page, or the current results on category
 * and search pages.
 */
class Post_List extends Widget {

	protected $photo_kind = 'post';

	public function get_name() {
		return 'ceori-post-list';
	}

	public function get_title() {
		return __( 'CEORI Article List', 'ceori' );
	}

	public function get_icon() {
		return 'eicon-posts-grid';
	}

	protected function register_controls() {
		$this->heading_controls();
		$this->start_controls_section( 'content', array( 'label' => __( 'Articles', 'ceori' ) ) );
		$this->add_control(
			'note',
			array(
				'type' => Controls_Manager::RAW_HTML,
				'raw'  => __( 'Shows every published article, newest first. On category and search pages it shows their results.', 'ceori' ),
			)
		);
		$this->add_control(
			'per_page',
			array(
				'label'       => __( 'Articles per page', 'ceori' ),
				'description' => __( 'On category and search pages this follows Settings → Reading → “Blog pages show at most”.', 'ceori' ),
				'type'        => Controls_Manager::NUMBER,
				'default' => 9,
				'min'     => 1,
				'max'     => 48,
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
			'show_date',
			array(
				'label'        => __( 'Show date', 'ceori' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
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
		$this->add_control(
			'excerpt_words',
			array(
				'label'     => __( 'Excerpt length (words)', 'ceori' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 18,
				'min'       => 5,
				'max'       => 60,
				'condition' => array( 'show_excerpt' => 'yes' ),
			)
		);
		$this->add_control(
			'more_text',
			array(
				'label'   => __( 'Link text', 'ceori' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Read more',
			)
		);
		$this->add_control(
			'empty_text',
			array(
				'label'     => __( 'Text when nothing is found', 'ceori' ),
				'type'      => Controls_Manager::TEXTAREA,
				'default'   => 'No articles found. Try another search or browse our treatments below.',
				'separator' => 'before',
			)
		);
		$this->add_control(
			'empty_categories',
			array(
				'label'        => __( 'Show treatment categories when nothing is found', 'ceori' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);
		$this->end_controls_section();

		$this->photo_controls();
		$this->section_style( '.ceori-post-list' );
		$this->heading_styles();
		$this->style_box( 'card', __( 'Cards', 'ceori' ), '.ceori-post-card' );
		$this->style_image( 'image', __( 'Images', 'ceori' ), '.ceori-post-card__media img' );
		$this->style_text( 'date', __( 'Dates', 'ceori' ), '.ceori-post-card__date' );
		$this->style_text( 'post_title', __( 'Titles', 'ceori' ), '.ceori-post-card__title, .ceori-post-card__title a' );
		$this->style_text( 'excerpt', __( 'Excerpts', 'ceori' ), '.ceori-post-card__excerpt' );
		$this->style_text( 'more', __( 'Card links', 'ceori' ), '.ceori-post-card .ceori-link-arrow' );
		$this->style_button( 'pages', __( 'Page numbers', 'ceori' ), '.ceori-pagination .page-numbers' );
	}

	protected function render() {
		global $wp_query;
		$s = $this->get_settings_for_display();
		if ( ( is_search() || is_archive() || is_home() ) && ! $this->is_editor() ) {
			$query = $wp_query;
		} else {
			$query = new \WP_Query(
				array(
					'post_type'           => 'post',
					'post_status'         => 'publish',
					'posts_per_page'      => max( 1, (int) $s['per_page'] ),
					'paged'               => max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) ),
					'ignore_sticky_posts' => true,
				)
			);
		}
		$item = is_search() ? __( 'result', 'ceori' ) : __( 'article', 'ceori' );

		echo '<section class="ceori-section ceori-section--tight ceori-post-list"><div class="ceori-wrap">' . $this->head( $s ); // phpcs:ignore WordPress.Security.EscapeOutput
		if ( ! $query->have_posts() ) {
			echo '<p class="ceori-post-list__empty">' . esc_html( $s['empty_text'] ) . '</p>';
			if ( 'yes' === $s['empty_categories'] ) {
				echo ceori_render_treatment_categories(
				array(
					'layout'         => 'cards',
					'showTreatments' => false,
				)
			); // phpcs:ignore WordPress.Security.EscapeOutput
			}
			echo '</div></section>';
			return;
		}
		echo '<div class="ceori-posts"><ul class="ceori-posts__grid ceori-grid ceori-grid--' . (int) $s['columns'] . '">';
		foreach ( $query->posts as $post ) {
			$url = get_permalink( $post );
			echo '<li class="ceori-post-card reveal"><a class="ceori-post-card__media" href="' . esc_url( $url ) . '" tabindex="-1" aria-hidden="true">' . ceori_card_image( ceori_search_image_source( $post ) ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput
			if ( is_search() ) {
				echo '<span class="ceori-post-card__type">' . esc_html( ceori_search_type( $post ) ) . '</span>';
			} elseif ( 'yes' === $s['show_date'] && 'post' === $post->post_type ) {
				echo '<time class="ceori-post-card__date" datetime="' . esc_attr( get_the_date( 'c', $post ) ) . '">' . esc_html( get_the_date( '', $post ) ) . '</time>';
			}
			echo '<h2 class="ceori-post-card__title"><a href="' . esc_url( $url ) . '">' . esc_html( get_the_title( $post ) ) . '</a></h2>';
			if ( 'yes' === $s['show_excerpt'] ) {
				echo '<p class="ceori-post-card__excerpt">' . esc_html( wp_trim_words( get_the_excerpt( $post ), max( 5, (int) $s['excerpt_words'] ) ) ) . '</p>';
			}
			if ( $s['more_text'] ) {
				echo '<a class="ceori-link-arrow" href="' . esc_url( $url ) . '">' . esc_html( $s['more_text'] ) . '<span class="screen-reader-text">: ' . esc_html( get_the_title( $post ) ) . '</span>' . ceori_icon( 'arrow-up-right' ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput
			}
			echo '</li>';
		}
		echo '</ul></div>';

		$links = paginate_links(
			array(
				'base'      => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
				'format'    => '',
				'current'   => max( 1, (int) $query->get( 'paged' ) ),
				'total'     => (int) $query->max_num_pages,
				'prev_text' => '<span aria-hidden="true">←</span><span class="screen-reader-text">' . esc_html__( 'Previous page', 'ceori' ) . '</span>',
				'next_text' => '<span aria-hidden="true">→</span><span class="screen-reader-text">' . esc_html__( 'Next page', 'ceori' ) . '</span>',
			)
		);
		if ( $links ) {
			/* translators: %s: "article" or "result" */
			echo '<nav class="ceori-pagination ceori-pagination--list" aria-label="' . esc_attr( sprintf( __( 'More %s pages', 'ceori' ), $item ) ) . '">' . $links . '</nav>'; // phpcs:ignore WordPress.Security.EscapeOutput
		}
		echo '</div></section>';
	}
}

/**
 * Article header: breadcrumbs, category, title, author and date, featured image.
 */
class Post_Header extends Widget {

	public function get_name() {
		return 'ceori-post-header';
	}

	public function get_title() {
		return __( 'CEORI Article Header', 'ceori' );
	}

	public function get_icon() {
		return 'eicon-post-title';
	}

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => __( 'Article header', 'ceori' ) ) );
		$this->add_control(
			'note',
			array(
				'type' => Controls_Manager::RAW_HTML,
				'raw'  => __( 'Title, category, author, date and featured image come from the article’s own settings.', 'ceori' ),
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
		$this->add_control(
			'show_category',
			array(
				'label'        => __( 'Show category', 'ceori' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);
		$this->add_control(
			'title',
			array(
				'label'       => __( 'Title (leave empty to use the article title)', 'ceori' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 2,
				'description' => __( 'The page’s main heading (H1). &lt;em&gt;…&lt;/em&gt; = italic accent.', 'ceori' ),
			)
		);
		$this->add_control(
			'show_author',
			array(
				'label'        => __( 'Show author', 'ceori' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);
		$this->add_control(
			'show_date',
			array(
				'label'        => __( 'Show date', 'ceori' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);
		$this->add_control(
			'show_image',
			array(
				'label'        => __( 'Show featured image', 'ceori' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);
		$this->end_controls_section();

		$this->section_style( '.ceori-article--head' );
		$this->style_text( 'crumbs', __( 'Breadcrumbs', 'ceori' ), '.ceori-breadcrumbs ol, .ceori-breadcrumbs a' );
		$this->style_text( 'category', __( 'Category', 'ceori' ), '.ceori-article__cat, .ceori-article__cat a' );
		$this->style_text( 'title', __( 'Title', 'ceori' ), '.ceori-article__title' );
		$this->style_text( 'accent', __( 'Title – italic accent', 'ceori' ), '.ceori-article__title em' );
		$this->style_text( 'meta', __( 'Author & date', 'ceori' ), '.ceori-article__meta' );
		$this->style_image( 'image', __( 'Featured image', 'ceori' ), '.ceori-article__media img' );
	}

	protected function render() {
		$s     = $this->get_settings_for_display();
		$pid   = $this->page_id();
		$title = '' !== trim( (string) $s['title'] ) ? $this->inline( $s['title'] ) : esc_html( get_the_title( $pid ) );

		echo '<section class="ceori-article ceori-article--head"><div class="ceori-article__head">';
		if ( 'yes' === $s['show_breadcrumbs'] ) {
			echo ceori_breadcrumb_html(); // phpcs:ignore WordPress.Security.EscapeOutput
		}
		if ( 'yes' === $s['show_category'] ) {
			$cats = get_the_category_list( ', ', '', $pid );
			if ( $cats ) {
				echo '<p class="ceori-eyebrow ceori-article__cat">' . $cats . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput
			}
		}
		echo '<h1 class="ceori-article__title">' . $title . '</h1>'; // phpcs:ignore WordPress.Security.EscapeOutput
		$meta = '';
		if ( 'yes' === $s['show_author'] ) {
			$meta .= '<span class="ceori-article__author">' . esc_html( get_the_author_meta( 'display_name', (int) get_post_field( 'post_author', $pid ) ) ) . '</span>';
		}
		if ( 'yes' === $s['show_date'] ) {
			$meta .= '<time datetime="' . esc_attr( get_the_date( 'c', $pid ) ) . '">' . esc_html( get_the_date( '', $pid ) ) . '</time>';
		}
		echo $meta ? '<div class="ceori-article__meta">' . $meta . '</div>' : ''; // phpcs:ignore WordPress.Security.EscapeOutput
		echo '</div>';
		if ( 'yes' === $s['show_image'] && has_post_thumbnail( $pid ) ) {
			echo '<figure class="ceori-article__media">' . get_the_post_thumbnail( // phpcs:ignore WordPress.Security.EscapeOutput
				$pid,
				'large',
				array(
					'class'         => 'is-lcp',
					'loading'       => 'eager',
					'fetchpriority' => 'high',
					'sizes'         => '(max-width: 1023px) 92vw, 1240px',
				)
			) . '</figure>';
		}
		echo '</section>';
	}
}

/**
 * Article text with share buttons and a note.
 */
class Post_Body extends Widget {

	public function get_name() {
		return 'ceori-post-body';
	}

	public function get_title() {
		return __( 'CEORI Article Text', 'ceori' );
	}

	public function get_icon() {
		return 'eicon-text';
	}

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => __( 'Article text', 'ceori' ) ) );
		$this->add_control(
			'text',
			array(
				'label'       => __( 'Text', 'ceori' ),
				'type'        => Controls_Manager::WYSIWYG,
				'description' => __( 'Use Heading 2 for main sections and Heading 3 below them (the article title is the only Heading 1).', 'ceori' ),
			)
		);
		$this->add_control(
			'show_share',
			array(
				'label'        => __( 'Show share buttons', 'ceori' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'separator'    => 'before',
			)
		);
		$this->add_control(
			'note',
			array(
				'label'   => __( 'Note below the article', 'ceori' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => 'This article is for general information. Please consult a specialist for advice about your own skin.',
			)
		);
		$this->end_controls_section();

		$this->section_style( '.ceori-article--body' );
		$this->style_text( 'text', __( 'Paragraphs', 'ceori' ), '.ceori-article__content, .ceori-article__content p' );
		$this->style_text( 'h2', __( 'Headings 2', 'ceori' ), '.ceori-article__content h2' );
		$this->style_text( 'h3', __( 'Headings 3', 'ceori' ), '.ceori-article__content h3, .ceori-article__content h4' );
		$this->style_text( 'link', __( 'Links', 'ceori' ), '.ceori-article__content a' );
		$this->style_text( 'list', __( 'Lists', 'ceori' ), '.ceori-article__content li' );
		$this->style_text( 'quote', __( 'Quotes', 'ceori' ), '.ceori-article__content blockquote' );
		$this->style_image( 'image', __( 'Images', 'ceori' ), '.ceori-article__content img' );
		$this->style_text( 'note_text', __( 'Note', 'ceori' ), '.ceori-article__note' );
		$this->style_button( 'share', __( 'Share buttons', 'ceori' ), '.ceori-share a, .ceori-share button' );
	}

	protected function render() {
		$s    = $this->get_settings_for_display();
		$foot = ( 'yes' === $s['show_share'] ? ceori_render_share() : '' )
			. ( $s['note'] ? '<p class="ceori-article__note">' . esc_html( $s['note'] ) . '</p>' : '' );
		echo '<section class="ceori-article ceori-article--body"><div class="ceori-article__content">';
		if ( $s['text'] ) {
			echo wp_kses_post( $s['text'] );
		} else {
			$this->editor_notice( __( 'Write the article in the Text box.', 'ceori' ) );
		}
		echo '</div>' . ( $foot ? '<div class="ceori-article__foot">' . $foot . '</div>' : '' ) . '</section>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}
}

/**
 * Related articles (same category, newest first).
 */
class Related_Posts extends Widget {

	protected $photo_kind = 'post';

	public function get_name() {
		return 'ceori-related-posts';
	}

	public function get_title() {
		return __( 'CEORI Related Articles', 'ceori' );
	}

	public function get_icon() {
		return 'eicon-post-list';
	}

	protected function register_controls() {
		$this->heading_controls(
			array(
				'eyebrow' => 'Keep reading',
				'title'   => 'Related <em>Articles</em>',
			)
		);
		$this->start_controls_section( 'content', array( 'label' => __( 'Related articles', 'ceori' ) ) );
		$this->add_control(
			'note',
			array(
				'type' => Controls_Manager::RAW_HTML,
				'raw'  => __( 'Shows three other articles from the same category.', 'ceori' ),
			)
		);
		$this->end_controls_section();

		$this->photo_controls();
		$this->section_style( '.ceori-related' );
		$this->heading_styles();
		$this->style_image( 'image', __( 'Images', 'ceori' ), '.ceori-post-card__media img' );
		$this->style_text( 'date', __( 'Dates', 'ceori' ), '.ceori-post-card__date' );
		$this->style_text( 'post_title', __( 'Titles', 'ceori' ), '.ceori-post-card__title, .ceori-post-card__title a' );
		$this->style_text( 'more', __( 'Card links', 'ceori' ), '.ceori-post-card .ceori-link-arrow' );
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		echo '<section class="ceori-section ceori-related has-mist-background-color has-background"><div class="ceori-wrap">' . $this->head( $s ) . ceori_render_related_posts() . '</div></section>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}
}

/**
 * Page not found message with search, buttons and treatment categories.
 */
class Not_Found extends Widget {

	public function get_name() {
		return 'ceori-not-found';
	}

	public function get_title() {
		return __( 'CEORI Page Not Found', 'ceori' );
	}

	public function get_icon() {
		return 'eicon-warning';
	}

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => __( 'Message', 'ceori' ) ) );
		$this->add_control(
			'code',
			array(
				'label'   => __( 'Small label', 'ceori' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '404',
			)
		);
		$this->add_control(
			'title',
			array(
				'label'   => __( 'Title', 'ceori' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 2,
				'default' => 'This page has <em>moved</em>',
			)
		);
		$this->add_control(
			'text',
			array(
				'label'   => __( 'Text', 'ceori' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => 'We couldn’t find the page you were looking for. It may have been renamed during our website update. Try searching, or choose one of our treatment areas below.',
			)
		);
		$this->add_control(
			'show_search',
			array(
				'label'        => __( 'Show search box', 'ceori' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);
		$this->button_controls( 'button', __( 'Main button', 'ceori' ), 'Back to Home', home_url( '/' ) );
		$this->button_controls( 'button2', __( 'Second button', 'ceori' ), 'Contact Us', home_url( '/contact-us/' ) );
		$this->add_control(
			'show_categories',
			array(
				'label'        => __( 'Show treatment categories', 'ceori' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'separator'    => 'before',
			)
		);
		$this->end_controls_section();

		$this->section_style( '.ceori-404' );
		$this->style_box( 'frame', __( 'Message box', 'ceori' ), '.ceori-404__frame' );
		$this->style_text( 'code', __( 'Small label', 'ceori' ), '.ceori-404__code' );
		$this->style_text( 'title', __( 'Title', 'ceori' ), '.ceori-404__frame h1' );
		$this->style_text( 'accent', __( 'Title – italic accent', 'ceori' ), '.ceori-404__frame h1 em' );
		$this->style_text( 'text', __( 'Text', 'ceori' ), '.ceori-404__frame .ceori-section__lead' );
		$this->style_button( 'button', __( 'Main button', 'ceori' ), '.has-arrow .wp-block-button__link' );
		$this->style_button( 'button2', __( 'Second button', 'ceori' ), '.is-style-outline-ceori .wp-block-button__link' );
		$this->style_box( 'card', __( 'Category cards', 'ceori' ), '.ceori-catcard' );
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		echo '<section class="ceori-section ceori-404"><div class="ceori-wrap"><div class="ceori-404__frame has-cream-background-color has-background">';
		echo $s['code'] ? '<p class="ceori-404__code">' . esc_html( $s['code'] ) . '</p>' : '';
		echo '<h1 class="has-text-align-center">' . $this->inline( $s['title'] ) . '</h1>'; // phpcs:ignore WordPress.Security.EscapeOutput
		echo $s['text'] ? '<p class="has-text-align-center ceori-section__lead">' . $this->inline( $s['text'] ) . '</p>' : ''; // phpcs:ignore WordPress.Security.EscapeOutput
		if ( 'yes' === $s['show_search'] ) {
			echo search_form( __( 'Search treatments and articles…', 'ceori' ), 'ceori-search--center' ); // phpcs:ignore WordPress.Security.EscapeOutput
		}
		echo $this->buttons( $this->button( $s['button_text'], $s['button_link'] ) . $this->button( $s['button2_text'], $s['button2_link'], 'outline' ), 'is-content-justification-center' ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput
		if ( 'yes' === $s['show_categories'] ) {
			echo ceori_render_treatment_categories(
				array(
					'layout'         => 'cards',
					'showTreatments' => false,
				)
			); // phpcs:ignore WordPress.Security.EscapeOutput
		}
		echo '</div></section>';
	}
}
