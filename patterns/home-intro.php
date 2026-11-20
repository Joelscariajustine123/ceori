<?php
/**
 * Title: Home – Introduction
 * Slug: ceori/home-intro
 * Categories: ceori-home
 * Description: Centred clinic introduction with two floating images.
 * Viewport Width: 1440
 *
 * @package ceori
 */

?>
<!-- wp:group {"tagName":"section","align":"full","className":"ceori-section ceori-intro","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull ceori-section ceori-intro"><?php echo ceori_image_block( 'intro-left', 'ceori-intro__img ceori-intro__img--left reveal', true ); // phpcs:ignore WordPress.Security.EscapeOutput ?>

<!-- wp:group {"className":"ceori-intro__body reveal","layout":{"type":"constrained","contentSize":"760px"}} -->
<div class="wp-block-group ceori-intro__body reveal"><!-- wp:paragraph {"className":"ceori-intro__quote"} -->
<p class="ceori-intro__quote">At CEORI, dermatologist-led care meets thoughtful cosmetic expertise. We listen first, explain every option clearly and plan treatment around your skin, your concerns and your goals – <strong>so you can feel good in your skin.</strong></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"ceori-intro__byline"} -->
<p class="ceori-intro__byline"><strong>CEORI Skin Clinic</strong><span class="ceori-intro__rule" aria-hidden="true"></span>Eranhipalam, Calicut</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<?php echo ceori_image_block( 'intro-right', 'ceori-intro__img ceori-intro__img--right reveal', true ); // phpcs:ignore WordPress.Security.EscapeOutput ?></section>
<!-- /wp:group -->
