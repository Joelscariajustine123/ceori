<?php
/**
 * Title: Frequently asked questions
 * Slug: ceori/about-faq
 * Categories: ceori-pages
 * Description: General FAQs as an accessible accordion.
 * Viewport Width: 1440
 *
 * @package ceori
 */

$ceori_faqs = array(
	array( 'How long does Botox last?', 'It varies from person to person, but the effect of Botox usually lasts around three to six months.' ),
	array( 'Are there side effects of teeth whitening?', 'Professional teeth whitening is generally safe when done by a qualified dental professional. Some people have temporary sensitivity or gum irritation for a short time.' ),
	array( 'When should I start preparing my skin for my wedding?', 'It depends on your skin concerns and the treatments you plan to have, but starting several months before the wedding gives time to address different concerns gently.' ),
	array( 'Is there an age limit for cosmetic surgery?', 'There is no single age limit. Suitability is decided case by case, based on your overall health, medical history and goals.' ),
	array( 'How long does laser hair removal last?', 'Laser hair removal gives long-term hair reduction. Results vary, and many people need occasional maintenance sessions.' ),
	array( 'Can a chemical peel help with acne or large pores?', 'Chemical peels can help with mild to moderate acne and enlarged pores. More severe acne may need other or additional treatment.' ),
);
?>
<!-- wp:group {"tagName":"section","align":"full","className":"ceori-section ceori-faq","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull ceori-section ceori-faq"><!-- wp:columns {"align":"wide","className":"ceori-faq__cols"} -->
<div class="wp-block-columns alignwide ceori-faq__cols"><!-- wp:column {"width":"38%","className":"ceori-faq__intro"} -->
<div class="wp-block-column ceori-faq__intro" style="flex-basis:38%"><!-- wp:paragraph {"className":"ceori-eyebrow"} -->
<p class="ceori-eyebrow">FAQ</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Questions <em>Patients</em> Often Ask</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"ceori-section__lead"} -->
<p class="ceori-section__lead">Can’t find your answer here? Our team is happy to help.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"has-arrow"} -->
<div class="wp-block-button has-arrow"><a class="wp-block-button__link wp-element-button" href="<?php echo ceori_link( '/contact-us/' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>">Get in Touch</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column -->

<!-- wp:column {"className":"ceori-faq__list"} -->
<div class="wp-block-column ceori-faq__list"><?php foreach ( $ceori_faqs as $ceori_faq ) : ?><!-- wp:details {"className":"is-style-faq"} -->
<details class="wp-block-details is-style-faq"><summary><?php echo esc_html( $ceori_faq[0] ); ?></summary><!-- wp:paragraph -->
<p><?php echo esc_html( $ceori_faq[1] ); ?></p>
<!-- /wp:paragraph --></details>
<!-- /wp:details -->

<?php endforeach; ?></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></section>
<!-- /wp:group -->
