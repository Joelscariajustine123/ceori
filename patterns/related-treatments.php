<?php
/**
 * Title: Related treatments section
 * Slug: ceori/related-treatments
 * Categories: ceori-treatment
 * Inserter: no
 * Description: Three related treatments for the current treatment page.
 *
 * @package ceori
 */

?>
<!-- wp:group {"tagName":"section","align":"full","className":"ceori-section ceori-related","backgroundColor":"mist","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull ceori-section ceori-related has-mist-background-color has-background"><!-- wp:group {"align":"wide","className":"ceori-section__head ceori-section__head--split","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"bottom"}} -->
<div class="wp-block-group alignwide ceori-section__head ceori-section__head--split"><!-- wp:group {"layout":{"type":"default"}} -->
<div class="wp-block-group"><!-- wp:paragraph {"className":"ceori-eyebrow"} -->
<p class="ceori-eyebrow">You may also like</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Related <em>Treatments</em></h2>
<!-- /wp:heading --></div>
<!-- /wp:group -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-outline-ceori"} -->
<div class="wp-block-button is-style-outline-ceori"><a class="wp-block-button__link wp-element-button" href="<?php echo ceori_link( '/treatments/' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>">View All Treatments</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group -->

<!-- wp:ceori/related-treatments {"align":"wide"} /--></section>
<!-- /wp:group -->
