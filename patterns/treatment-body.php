<?php
/**
 * Title: Treatment page content (starter)
 * Slug: ceori/treatment-body
 * Categories: ceori-treatment
 * Post Types: page
 * Description: All sections of a treatment page with placeholder text. Use with the "Treatment Detail" template; set the page excerpt as the intro and a featured image for the hero.
 * Viewport Width: 1280
 *
 * @package ceori
 */

echo ceori_treatment_body_markup( // phpcs:ignore WordPress.Security.EscapeOutput
	array(
		'title'    => 'this treatment',
		'about'    => array(
			'Explain in two or three short paragraphs what the treatment is and how it works, in plain language.',
			'Mention who performs it at CEORI and anything that makes the approach suitable for patients.',
		),
		'who'      => array( 'This treatment may help if you have:', array( 'Concern one', 'Concern two', 'Concern three' ) ),
		'benefits' => array( 'Benefit one', 'Benefit two', 'Benefit three' ),
		'steps'    => array(
			'What happens at the consultation.',
			'What happens during the procedure, and how long it takes.',
			'Aftercare and follow-up visits.',
		),
		'expect'   => array( 'Preparation, comfort during treatment, recovery and aftercare – approved by a CEORI specialist.' ),
		'faq'      => array(
			array( 'A common question?', 'A clear, accurate answer reviewed by a specialist.' ),
			array( 'Another question?', 'Another answer.' ),
		),
		'image'    => array( 0, ceori_media( 'about-main' )['url'], '' ),
	)
);
