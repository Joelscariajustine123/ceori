<?php
/**
 * Title: About – Mission & approach
 * Slug: ceori/about-approach
 * Categories: ceori-pages
 * Description: Mission statement with three principles.
 * Viewport Width: 1440
 *
 * @package ceori
 */

$ceori_principles = array(
	array( 'Listen first', 'Every visit begins with your concerns and questions. We take time to understand your skin, your history and what matters to you.' ),
	array( 'Honest, clear advice', 'We explain what a treatment can and cannot do, how many sessions it may take and what recovery involves – before you decide.' ),
	array( 'Care that continues', 'Good results depend on follow-up. We review your progress and adjust your plan, so you always know what comes next.' ),
);
?>
<!-- wp:group {"tagName":"section","align":"full","className":"ceori-section ceori-approach","backgroundColor":"blush","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull ceori-section ceori-approach has-blush-background-color has-background"><!-- wp:columns {"align":"wide","className":"ceori-approach__cols"} -->
<div class="wp-block-columns alignwide ceori-approach__cols"><!-- wp:column {"width":"40%"} -->
<div class="wp-block-column" style="flex-basis:40%"><!-- wp:paragraph {"className":"ceori-eyebrow"} -->
<p class="ceori-eyebrow">Our mission</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Helping You Feel <em>Confident</em> in Your Skin</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"ceori-section__lead"} -->
<p class="ceori-section__lead">Our aim is simple: safe, effective care that respects your natural features and your individual goals.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><?php foreach ( $ceori_principles as $ceori_p ) : ?><!-- wp:group {"className":"ceori-principle reveal","layout":{"type":"default"}} -->
<div class="wp-block-group ceori-principle reveal"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php echo esc_html( $ceori_p[0] ); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php echo esc_html( $ceori_p[1] ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<?php endforeach; ?></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></section>
<!-- /wp:group -->
