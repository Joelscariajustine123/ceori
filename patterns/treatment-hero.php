<?php
/**
 * Title: Treatment hero
 * Slug: ceori/treatment-hero
 * Categories: ceori-treatment
 * Inserter: no
 * Description: Treatment and category page hero – breadcrumbs, category, H1 title, intro, buttons and featured image.
 *
 * @package ceori
 */

?>
<!-- wp:group {"tagName":"section","align":"full","className":"ceori-thero","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull ceori-thero"><!-- wp:group {"align":"wide","className":"ceori-thero__frame","backgroundColor":"cream","layout":{"type":"default"}} -->
<div class="wp-block-group alignwide ceori-thero__frame has-cream-background-color has-background"><!-- wp:group {"className":"ceori-thero__body","layout":{"type":"default"}} -->
<div class="wp-block-group ceori-thero__body"><!-- wp:ceori/breadcrumbs /-->

<!-- wp:post-terms {"term":"treatment_category","className":"ceori-eyebrow ceori-thero__cat"} /-->

<!-- wp:post-title {"level":1,"className":"ceori-thero__title"} /-->

<!-- wp:post-excerpt {"className":"ceori-thero__lead"} /-->

<!-- wp:buttons {"className":"ceori-thero__actions"} -->
<div class="wp-block-buttons ceori-thero__actions"><!-- wp:button {"className":"has-arrow"} -->
<div class="wp-block-button has-arrow"><a class="wp-block-button__link wp-element-button" href="<?php echo ceori_link( '/contact-us/' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>#appointment">Book Consultation</a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"is-style-outline-ceori has-phone"} -->
<div class="wp-block-button is-style-outline-ceori has-phone"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_attr( ceori_tel_href( ceori_option( 'phone_primary' ) ) ); ?>">Call Us</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group -->

<!-- wp:post-featured-image {"aspectRatio":"5/4","sizeSlug":"large","className":"ceori-thero__media is-lcp"} /--></div>
<!-- /wp:group --></section>
<!-- /wp:group -->
