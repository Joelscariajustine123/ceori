<?php
/**
 * Title: Page not found
 * Slug: ceori/not-found
 * Categories: ceori-pages
 * Inserter: no
 * Description: Branded 404 page with a way home, search and links to the main treatment categories.
 *
 * @package ceori
 */

?>
<!-- wp:group {"tagName":"section","align":"full","className":"ceori-section ceori-404","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull ceori-section ceori-404"><!-- wp:group {"align":"wide","className":"ceori-404__frame","backgroundColor":"cream","layout":{"type":"constrained","contentSize":"720px"}} -->
<div class="wp-block-group alignwide ceori-404__frame has-cream-background-color has-background"><!-- wp:paragraph {"className":"ceori-404__code"} -->
<p class="ceori-404__code">404</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1,"textAlign":"center"} -->
<h1 class="wp-block-heading has-text-align-center">This page has <em>moved</em></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","className":"ceori-section__lead"} -->
<p class="has-text-align-center ceori-section__lead">We couldn’t find the page you were looking for. It may have been renamed during our website update. Try searching, or choose one of our treatment areas below.</p>
<!-- /wp:paragraph -->

<!-- wp:search {"label":"Search the website","showLabel":false,"placeholder":"Search treatments and articles…","buttonText":"Search","buttonUseIcon":true,"className":"ceori-search ceori-search--center"} /-->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button {"className":"has-arrow"} -->
<div class="wp-block-button has-arrow"><a class="wp-block-button__link wp-element-button" href="<?php echo ceori_link( '/' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>">Back to Home</a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"is-style-outline-ceori"} -->
<div class="wp-block-button is-style-outline-ceori"><a class="wp-block-button__link wp-element-button" href="<?php echo ceori_link( '/contact-us/' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>">Contact Us</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group -->

<!-- wp:ceori/treatment-categories {"layout":"cards","align":"wide"} /--></section>
<!-- /wp:group -->
