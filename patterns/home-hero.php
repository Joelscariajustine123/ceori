<?php
/**
 * Title: Home – Hero
 * Slug: ceori/home-hero
 * Categories: ceori-home
 * Description: Large hero with portrait, headline, specialist avatars and a featured treatment card.
 * Viewport Width: 1440
 *
 * @package ceori
 */

?>
<!-- wp:group {"tagName":"section","align":"full","className":"ceori-hero","layout":{"type":"default"}} -->
<section class="wp-block-group alignfull ceori-hero"><!-- wp:group {"className":"ceori-hero__frame","backgroundColor":"cream","layout":{"type":"default"}} -->
<div class="wp-block-group ceori-hero__frame has-cream-background-color has-background"><?php echo ceori_image_block( 'hero-portrait', 'ceori-hero__portrait is-lcp' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>

<!-- wp:group {"className":"ceori-hero__content","layout":{"type":"default"}} -->
<div class="wp-block-group ceori-hero__content"><!-- wp:heading {"level":1,"className":"ceori-hero__title"} -->
<h1 class="wp-block-heading ceori-hero__title">Feel Good in Your <em>Skin.</em></h1>
<!-- /wp:heading -->

<!-- wp:ceori/doctors {"layout":"avatars","limit":3,"caption":"Specialists Who Care"} /--></div>
<!-- /wp:group -->

<!-- wp:group {"className":"ceori-hero__card","layout":{"type":"default"}} -->
<div class="wp-block-group ceori-hero__card"><!-- wp:group {"className":"ceori-hero__card-frame","layout":{"type":"default"}} -->
<div class="wp-block-group ceori-hero__card-frame"><?php echo ceori_image_block( 'hero-card', 'ceori-hero__card-media' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
<!-- /wp:group -->

<!-- wp:group {"className":"ceori-hero__card-body","layout":{"type":"default"}} -->
<div class="wp-block-group ceori-hero__card-body"><!-- wp:heading {"className":"ceori-hero__card-title"} -->
<h2 class="wp-block-heading ceori-hero__card-title">Hydra Facial</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"ceori-hero__card-text"} -->
<p class="ceori-hero__card-text">Expert skin, hair and cosmetic care in Calicut. A gentle facial that cleanses, exfoliates and deeply hydrates.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"width":100,"className":"has-arrow"} -->
<div class="wp-block-button has-custom-width wp-block-button__width-100 has-arrow"><a class="wp-block-button__link wp-element-button" href="<?php echo ceori_link( '/contact-us/' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>?treatment=Hydra%20Facial#appointment">Book an Appointment</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->
