<?php
/**
 * Title: Specialists
 * Slug: ceori/home-specialists
 * Categories: ceori-home, ceori-pages
 * Description: Specialist cards with names, designations and consultation links.
 * Viewport Width: 1440
 *
 * @package ceori
 */

?>
<!-- wp:group {"tagName":"section","align":"full","anchor":"specialists","className":"ceori-section ceori-specialists","backgroundColor":"cream","layout":{"type":"constrained"}} -->
<section id="specialists" class="wp-block-group alignfull ceori-section ceori-specialists has-cream-background-color has-background"><!-- wp:group {"align":"wide","className":"ceori-section__head ceori-section__head--split","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"bottom"}} -->
<div class="wp-block-group alignwide ceori-section__head ceori-section__head--split"><!-- wp:group {"layout":{"type":"default"}} -->
<div class="wp-block-group"><!-- wp:paragraph {"className":"ceori-eyebrow"} -->
<p class="ceori-eyebrow">Our specialists</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Meet the <em>Experts</em> Behind Your Care</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"ceori-section__lead"} -->
<p class="ceori-section__lead">Dermatologists, a plastic surgeon, a cosmetic gynaecologist and an oral medicine specialist – each focused on care that suits you.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"has-arrow"} -->
<div class="wp-block-button has-arrow"><a class="wp-block-button__link wp-element-button" href="<?php echo ceori_link( '/contact-us/' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>#appointment">Book a Consultation</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group -->

<!-- wp:ceori/doctors {"align":"wide"} /--></section>
<!-- /wp:group -->
