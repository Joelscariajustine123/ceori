<?php
/**
 * Title: Home – About CEORI
 * Slug: ceori/home-about
 * Categories: ceori-home, ceori-pages
 * Description: "Where expertise meets personal care" – large image beside expandable highlights.
 * Viewport Width: 1440
 *
 * @package ceori
 */

?>
<!-- wp:group {"tagName":"section","align":"full","className":"ceori-section ceori-about","backgroundColor":"mist","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull ceori-section ceori-about has-mist-background-color has-background"><!-- wp:group {"align":"wide","className":"ceori-section__head","layout":{"type":"default"}} -->
<div class="wp-block-group alignwide ceori-section__head"><!-- wp:paragraph {"className":"ceori-eyebrow"} -->
<p class="ceori-eyebrow">About us</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Where <em>Expertise</em> Meets Personal <em>Care</em></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"ceori-section__lead"} -->
<p class="ceori-section__lead">At CEORI Skin Clinic, we bring together experienced specialists, modern treatments and personalised care to support your skin, hair and aesthetic goals.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:columns {"align":"wide","className":"ceori-about__cols"} -->
<div class="wp-block-columns alignwide ceori-about__cols"><!-- wp:column {"className":"ceori-about__media reveal"} -->
<div class="wp-block-column ceori-about__media reveal"><?php echo ceori_image_block( 'about-main', 'ceori-cover-img' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>

<!-- wp:paragraph {"className":"ceori-play-wrap"} -->
<p class="ceori-play-wrap"><a class="ceori-play" href="https://www.youtube.com/watch?v=Lp7sEgC2mt8" data-video="Lp7sEgC2mt8">Watch a CEORI specialist explain PRP treatment</a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"className":"ceori-about__panel reveal"} -->
<div class="wp-block-column ceori-about__panel reveal"><!-- wp:details {"showContent":true,"className":"is-style-faq ceori-acc"} -->
<details class="wp-block-details is-style-faq ceori-acc" open><summary>Experienced Specialists</summary><!-- wp:paragraph -->
<p>Consultant dermatologists, a plastic surgeon, a cosmetic gynaecologist and an oral medicine specialist work together under one roof.</p>
<!-- /wp:paragraph --></details>
<!-- /wp:details -->

<!-- wp:details {"className":"is-style-faq ceori-acc"} -->
<details class="wp-block-details is-style-faq ceori-acc"><summary>Personalised Treatment Plans</summary><!-- wp:paragraph -->
<p>Every plan starts with listening to you and examining your skin, so treatment fits your concerns, lifestyle and goals.</p>
<!-- /wp:paragraph --></details>
<!-- /wp:details -->

<!-- wp:details {"className":"is-style-faq ceori-acc"} -->
<details class="wp-block-details is-style-faq ceori-acc"><summary>Modern Treatment Technology</summary><!-- wp:paragraph -->
<p>From lasers and HydraFacial to dermatosurgery, we use established, modern procedures chosen for safety and suitability.</p>
<!-- /wp:paragraph --></details>
<!-- /wp:details -->

<!-- wp:details {"className":"is-style-faq ceori-acc"} -->
<details class="wp-block-details is-style-faq ceori-acc"><summary>Care Focused on Your Needs</summary><!-- wp:paragraph -->
<p>Clear explanations, honest advice about what to expect and follow-up care that continues after your visit.</p>
<!-- /wp:paragraph --></details>
<!-- /wp:details -->

<!-- wp:buttons {"className":"ceori-about__actions"} -->
<div class="wp-block-buttons ceori-about__actions"><!-- wp:button {"className":"has-arrow"} -->
<div class="wp-block-button has-arrow"><a class="wp-block-button__link wp-element-button" href="<?php echo ceori_link( '/about-us/' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>">About CEORI</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></section>
<!-- /wp:group -->
