<?php
/**
 * Title: Appointment call to action
 * Slug: ceori/cta-appointment
 * Categories: ceori-home, ceori-pages, ceori-treatment
 * Description: "Ready to take the next step?" banner with booking and call buttons.
 * Viewport Width: 1440
 *
 * @package ceori
 */

?>
<!-- wp:group {"tagName":"section","align":"full","className":"ceori-section ceori-cta-wrap","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull ceori-section ceori-cta-wrap"><!-- wp:group {"align":"wide","className":"ceori-cta reveal","backgroundColor":"blush","layout":{"type":"default"}} -->
<div class="wp-block-group alignwide ceori-cta reveal has-blush-background-color has-background"><!-- wp:group {"className":"ceori-cta__body","layout":{"type":"default"}} -->
<div class="wp-block-group ceori-cta__body"><!-- wp:heading -->
<h2 class="wp-block-heading">Ready to Take the <em>Next Step?</em></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"ceori-section__lead"} -->
<p class="ceori-section__lead">Speak with our team and find the right care for your concerns.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:buttons {"className":"ceori-cta__actions"} -->
<div class="wp-block-buttons ceori-cta__actions"><!-- wp:button {"className":"is-style-outline-ceori has-phone ceori-cta__call"} -->
<div class="wp-block-button is-style-outline-ceori has-phone ceori-cta__call"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_attr( ceori_tel_href( ceori_option( 'phone_primary' ) ) ); ?>">Call Us</a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"has-arrow"} -->
<div class="wp-block-button has-arrow"><a class="wp-block-button__link wp-element-button" href="<?php echo ceori_link( '/contact-us/' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>#appointment">Book an Appointment</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->
