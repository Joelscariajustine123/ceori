<?php
/**
 * Title: About – Welcome to CEORI
 * Slug: ceori/about-intro
 * Categories: ceori-pages
 * Description: Clinic introduction with two images and key points.
 * Viewport Width: 1440
 *
 * @package ceori
 */

?>
<!-- wp:group {"tagName":"section","align":"full","className":"ceori-section ceori-welcome","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull ceori-section ceori-welcome"><!-- wp:columns {"verticalAlignment":"center","align":"wide","className":"ceori-welcome__cols"} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center ceori-welcome__cols"><!-- wp:column {"verticalAlignment":"center","className":"ceori-welcome__media reveal"} -->
<div class="wp-block-column is-vertically-aligned-center ceori-welcome__media reveal"><?php echo ceori_image_block( 'about-1', 'ceori-welcome__img ceori-welcome__img--main' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>

<?php echo ceori_image_block( 'about-2', 'ceori-welcome__img ceori-welcome__img--small' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","className":"ceori-welcome__body reveal"} -->
<div class="wp-block-column is-vertically-aligned-center ceori-welcome__body reveal"><!-- wp:paragraph {"className":"ceori-eyebrow"} -->
<p class="ceori-eyebrow">Welcome to CEORI</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Skin, Hair &amp; Aesthetic Care in <em>Calicut</em></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>CEORI Skin Clinic in Eranhipalam, Calicut, brings together clinical dermatology, cosmetic dermatology, cosmetic gynaecology, cosmetic dentistry and cosmetic surgery in one place.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>We are dedicated to personalised care with a steady focus on safety, quality and comfort – taking time to understand your concerns and explaining your options clearly, so you can make decisions with confidence.</p>
<!-- /wp:paragraph -->

<!-- wp:list {"className":"is-style-checks ceori-two-col-list"} -->
<ul class="wp-block-list is-style-checks ceori-two-col-list"><!-- wp:list-item -->
<li>Dermatologist-led treatments</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>A professional, multi-specialty team</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Clear advice on costs before treatment</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Customised skincare plans</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></section>
<!-- /wp:group -->
