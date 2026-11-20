<?php
/**
 * Block markup for the body of a treatment page.
 *
 * Shared by the "Treatment page content" pattern (placeholder text for new treatments)
 * and the content importer (real CEORI content), so every treatment page has the same
 * structure: About → Who may benefit → Benefits → How it works → What to expect → FAQs.
 *
 * @package ceori
 */

defined( 'ABSPATH' ) || exit;

/**
 * Serialized paragraph block.
 *
 * @param string $text  Plain text.
 * @param string $class Optional class.
 * @return string
 */
function ceori_p_block( $text, $class = '' ) {
	$attrs = $class ? ' ' . wp_json_encode( array( 'className' => $class ) ) : '';
	return "<!-- wp:paragraph{$attrs} -->\n<p" . ( $class ? ' class="' . esc_attr( $class ) . '"' : '' ) . '>' . esc_html( $text ) . "</p>\n<!-- /wp:paragraph -->";
}

/**
 * Serialized heading block (level 2 unless given).
 *
 * @param string $html  Heading HTML (may contain <em>).
 * @param int    $level Level.
 * @param string $class Optional class.
 * @return string
 */
function ceori_h_block( $html, $level = 2, $class = '' ) {
	$attrs = array();
	if ( 2 !== $level ) {
		$attrs['level'] = $level;
	}
	if ( $class ) {
		$attrs['className'] = $class;
	}
	$json = $attrs ? ' ' . wp_json_encode( $attrs ) : '';
	return "<!-- wp:heading{$json} -->\n<h{$level} class=\"wp-block-heading" . ( $class ? ' ' . esc_attr( $class ) : '' ) . "\">" . wp_kses( $html, array( 'em' => array() ) ) . "</h{$level}>\n<!-- /wp:heading -->";
}

/**
 * Serialized list block.
 *
 * @param string[] $items Items (plain text).
 * @param string   $class Optional class.
 * @return string
 */
function ceori_list_block( $items, $class = '' ) {
	$attrs = $class ? ' ' . wp_json_encode( array( 'className' => $class ) ) : '';
	$out   = "<!-- wp:list{$attrs} -->\n<ul class=\"wp-block-list" . ( $class ? ' ' . esc_attr( $class ) : '' ) . '">';
	foreach ( $items as $item ) {
		$out .= "<!-- wp:list-item -->\n<li>" . esc_html( $item ) . "</li>\n<!-- /wp:list-item -->";
	}
	return $out . "</ul>\n<!-- /wp:list -->";
}

/**
 * Serialized group block wrapping inner markup.
 *
 * @param string $inner Inner block markup.
 * @param array  $attrs Group attributes (className, align, backgroundColor, tagName, layout).
 * @return string
 */
function ceori_group_block( $inner, $attrs = array() ) {
	$attrs   = array_merge( array( 'layout' => array( 'type' => 'default' ) ), $attrs );
	$tag     = isset( $attrs['tagName'] ) ? $attrs['tagName'] : 'div';
	$classes = array( 'wp-block-group' );
	if ( ! empty( $attrs['align'] ) ) {
		$classes[] = 'align' . $attrs['align'];
	}
	if ( ! empty( $attrs['className'] ) ) {
		$classes[] = $attrs['className'];
	}
	if ( ! empty( $attrs['backgroundColor'] ) ) {
		$classes[] = 'has-' . $attrs['backgroundColor'] . '-background-color';
		$classes[] = 'has-background';
	}
	$id = ! empty( $attrs['anchor'] ) ? ' id="' . esc_attr( $attrs['anchor'] ) . '"' : '';
	return '<!-- wp:group ' . wp_json_encode( $attrs, JSON_UNESCAPED_SLASHES ) . " -->\n<{$tag}{$id} class=\"" . esc_attr( implode( ' ', $classes ) ) . "\">{$inner}</{$tag}>\n<!-- /wp:group -->";
}

/**
 * Serialized core/image block for an attachment (or a plain URL when $id is 0).
 *
 * @param int    $id    Attachment ID.
 * @param string $url   Image URL.
 * @param string $alt   Alt text.
 * @param string $class Class.
 * @return string
 */
function ceori_attachment_image_block( $id, $url, $alt, $class = '' ) {
	$attrs = array(
		'sizeSlug'        => 'large',
		'linkDestination' => 'none',
	);
	if ( $id ) {
		$attrs = array( 'id' => (int) $id ) + $attrs;
	}
	if ( $class ) {
		$attrs['className'] = $class;
	}
	return '<!-- wp:image ' . wp_json_encode( $attrs ) . " -->\n<figure class=\"wp-block-image size-large" . ( $class ? ' ' . esc_attr( $class ) : '' ) . '"><img src="' . esc_url( $url ) . '" alt="' . esc_attr( $alt ) . '"' . ( $id ? ' class="wp-image-' . (int) $id . '"' : '' ) . "/></figure>\n<!-- /wp:image -->";
}

/**
 * Full treatment body markup.
 *
 * @param array $t {
 *     @type string   $title    Treatment name.
 *     @type string[] $about    Paragraphs.
 *     @type array    $who      [ intro, items[] ].
 *     @type string[] $benefits Benefits.
 *     @type string[] $steps    [ consultation, procedure, follow-up ].
 *     @type string[] $expect   Paragraphs.
 *     @type array    $faq      [ [ q, a ], ... ].
 *     @type array    $image    [ id, url, alt ] content image.
 * }
 * @return string
 */
function ceori_treatment_body_markup( $t ) {
	$title = $t['title'];
	$out   = array();

	// 1. About the treatment.
	$about = ceori_p_block( 'About the treatment', 'ceori-eyebrow' ) . "\n\n" . ceori_h_block( 'What is <em>' . esc_html( $title ) . '</em>?' );
	foreach ( $t['about'] as $para ) {
		$about .= "\n\n" . ceori_p_block( $para );
	}
	$media = ! empty( $t['image'][1] ) ? ceori_attachment_image_block( $t['image'][0], $t['image'][1], $t['image'][2], 'ceori-tb-about__img' ) : '';
	$out[] = ceori_group_block(
		'<!-- wp:columns {"verticalAlignment":"center","className":"ceori-tb-about__cols"} -->' . "\n"
		. '<div class="wp-block-columns are-vertically-aligned-center ceori-tb-about__cols"><!-- wp:column {"verticalAlignment":"center","width":"55%","className":"reveal"} -->' . "\n"
		. '<div class="wp-block-column is-vertically-aligned-center reveal" style="flex-basis:55%">' . $about . '</div>' . "\n"
		. '<!-- /wp:column -->' . "\n\n"
		. '<!-- wp:column {"verticalAlignment":"center","className":"reveal"} -->' . "\n"
		. '<div class="wp-block-column is-vertically-aligned-center reveal">' . $media . '</div>' . "\n"
		. '<!-- /wp:column --></div>' . "\n"
		. '<!-- /wp:columns -->',
		array(
			'tagName'   => 'section',
			'align'     => 'wide',
			'className' => 'ceori-tb ceori-tb-about',
		)
	);

	// 2 & 3. Who may benefit + Benefits.
	$who      = ceori_h_block( 'Who may <em>benefit?</em>', 3 ) . "\n\n" . ceori_p_block( $t['who'][0] ) . "\n\n" . ceori_list_block( $t['who'][1], 'is-style-checks' );
	$benefits = ceori_h_block( 'Benefits', 3 ) . "\n\n" . ceori_list_block( $t['benefits'], 'is-style-checks' );
	$out[]    = ceori_group_block(
		ceori_h_block( 'Is this treatment <em>right for you?</em>', 2, 'screen-reader-text' ) . "\n\n"
		. '<!-- wp:columns {"className":"ceori-tb-fit__cols"} -->' . "\n"
		. '<div class="wp-block-columns ceori-tb-fit__cols"><!-- wp:column {"className":"ceori-tb-card reveal"} -->' . "\n"
		. '<div class="wp-block-column ceori-tb-card reveal">' . $who . '</div>' . "\n"
		. '<!-- /wp:column -->' . "\n\n"
		. '<!-- wp:column {"className":"ceori-tb-card ceori-tb-card--accent reveal"} -->' . "\n"
		. '<div class="wp-block-column ceori-tb-card ceori-tb-card--accent reveal">' . $benefits . '</div>' . "\n"
		. '<!-- /wp:column --></div>' . "\n"
		. '<!-- /wp:columns -->',
		array(
			'tagName'   => 'section',
			'align'     => 'wide',
			'className' => 'ceori-tb ceori-tb-fit',
		)
	);

	// 4. How the treatment works.
	$labels = array( 'Consultation', 'Treatment', 'Follow-up' );
	$cols   = '';
	foreach ( $t['steps'] as $i => $step ) {
		$cols .= '<!-- wp:column {"className":"ceori-step reveal"} -->' . "\n" . '<div class="wp-block-column ceori-step reveal">'
			. ceori_p_block( sprintf( '%02d', $i + 1 ), 'ceori-step__num' ) . "\n\n"
			. ceori_h_block( esc_html( isset( $labels[ $i ] ) ? $labels[ $i ] : '' ), 3 ) . "\n\n"
			. ceori_p_block( $step ) . '</div>' . "\n" . '<!-- /wp:column -->' . "\n\n";
	}
	$out[] = ceori_group_block(
		ceori_p_block( 'Step by step', 'ceori-eyebrow' ) . "\n\n" . ceori_h_block( 'How the treatment <em>works</em>' ) . "\n\n"
		. '<!-- wp:columns {"className":"ceori-steps"} -->' . "\n" . '<div class="wp-block-columns ceori-steps">' . $cols . '</div>' . "\n" . '<!-- /wp:columns -->',
		array(
			'tagName'   => 'section',
			'align'     => 'wide',
			'className' => 'ceori-tb ceori-tb-steps',
		)
	);

	// 5. What to expect.
	$expect = ceori_p_block( 'Preparation & recovery', 'ceori-eyebrow' ) . "\n\n" . ceori_h_block( 'What to <em>expect</em>' );
	foreach ( $t['expect'] as $para ) {
		$expect .= "\n\n" . ceori_p_block( $para );
	}
	$expect .= "\n\n" . ceori_p_block( 'Suitability, the number of sessions and results depend on an individual assessment by your CEORI specialist. Results vary from person to person.', 'ceori-note' );
	$out[]   = ceori_group_block(
		$expect,
		array(
			'tagName'   => 'section',
			'align'     => 'wide',
			'className' => 'ceori-tb ceori-tb-expect reveal',
		)
	);

	// 6. FAQs.
	if ( ! empty( $t['faq'] ) ) {
		$faq = '';
		foreach ( $t['faq'] as $qa ) {
			$faq .= '<!-- wp:details {"className":"is-style-faq"} -->' . "\n" . '<details class="wp-block-details is-style-faq"><summary>' . esc_html( $qa[0] ) . '</summary>' . ceori_p_block( $qa[1] ) . '</details>' . "\n" . '<!-- /wp:details -->' . "\n\n";
		}
		$out[] = ceori_group_block(
			'<!-- wp:columns {"className":"ceori-faq__cols"} -->' . "\n"
			. '<div class="wp-block-columns ceori-faq__cols"><!-- wp:column {"width":"36%","className":"ceori-faq__intro"} -->' . "\n"
			. '<div class="wp-block-column ceori-faq__intro" style="flex-basis:36%">' . ceori_p_block( 'FAQ', 'ceori-eyebrow' ) . "\n\n" . ceori_h_block( 'Frequently asked <em>questions</em>' ) . "\n\n" . ceori_p_block( 'Have another question about ' . $title . '? Our team will be glad to help.', 'ceori-section__lead' ) . '</div>' . "\n"
			. '<!-- /wp:column -->' . "\n\n"
			. '<!-- wp:column {"className":"ceori-faq__list"} -->' . "\n" . '<div class="wp-block-column ceori-faq__list">' . $faq . '</div>' . "\n" . '<!-- /wp:column --></div>' . "\n"
			. '<!-- /wp:columns -->',
			array(
				'tagName'   => 'section',
				'align'     => 'wide',
				'className' => 'ceori-tb ceori-tb-faq ceori-faq',
			)
		);
	}

	return implode( "\n\n", $out );
}
