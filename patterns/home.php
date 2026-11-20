<?php
/**
 * Title: Full homepage
 * Slug: ceori/home
 * Categories: ceori-home
 * Description: Every homepage section in the CEORI order: hero, introduction, about, why choose us, treatments, results, specialists, testimonials, blog and appointment call to action.
 * Viewport Width: 1440
 *
 * @package ceori
 */

foreach ( array( 'home-hero', 'home-intro', 'home-about', 'home-why', 'home-treatments', 'home-specialities', 'home-results', 'home-specialists', 'home-testimonials', 'home-blog', 'cta-appointment' ) as $ceori_section ) {
	echo '<!-- wp:pattern {"slug":"ceori/' . esc_attr( $ceori_section ) . '"} /-->' . "\n\n";
}
