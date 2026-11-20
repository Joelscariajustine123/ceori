<?php
/**
 * Title: Consultation process
 * Slug: ceori/about-process
 * Categories: ceori-pages, ceori-treatment
 * Description: Three steps from booking to a personalised plan, plus opening hours.
 * Viewport Width: 1440
 *
 * @package ceori
 */

$ceori_steps = array(
	array( '01', 'Book your visit', 'Request an appointment online or call us. We arrange a slot based on the doctor’s schedule and availability, and confirm it with you by phone.' ),
	array( '02', 'Meet your specialist', 'Meet your doctor in person at the clinic. They listen to your concerns, examine your skin, hair or smile and answer your questions.' ),
	array( '03', 'Your personalised plan', 'We explain the diagnosis and the treatment options that suit you – including what to expect, aftercare and follow-up.' ),
);
?>
<!-- wp:group {"tagName":"section","align":"full","className":"ceori-section ceori-process","backgroundColor":"mist","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull ceori-section ceori-process has-mist-background-color has-background"><!-- wp:group {"align":"wide","className":"ceori-section__head is-centered","layout":{"type":"default"}} -->
<div class="wp-block-group alignwide ceori-section__head is-centered"><!-- wp:paragraph {"className":"ceori-eyebrow"} -->
<p class="ceori-eyebrow">How it works</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Your <em>Consultation</em>, Step by Step</h2>
<!-- /wp:heading --></div>
<!-- /wp:group -->

<!-- wp:columns {"align":"wide","className":"ceori-steps"} -->
<div class="wp-block-columns alignwide ceori-steps"><?php foreach ( $ceori_steps as $ceori_step ) : ?><!-- wp:column {"className":"ceori-step reveal"} -->
<div class="wp-block-column ceori-step reveal"><!-- wp:paragraph {"className":"ceori-step__num"} -->
<p class="ceori-step__num"><?php echo esc_html( $ceori_step[0] ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php echo esc_html( $ceori_step[1] ); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php echo esc_html( $ceori_step[2] ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<?php endforeach; ?></div>
<!-- /wp:columns -->

<!-- wp:group {"align":"wide","className":"ceori-hours-card reveal","backgroundColor":"base","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"center"}} -->
<div class="wp-block-group alignwide ceori-hours-card reveal has-base-background-color has-background"><!-- wp:group {"layout":{"type":"default"}} -->
<div class="wp-block-group"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Opening hours</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"ceori-muted"} -->
<p class="ceori-muted">Our clinic is here for you during the following hours.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:ceori/clinic-info {"field":"hours"} /--></div>
<!-- /wp:group --></section>
<!-- /wp:group -->
