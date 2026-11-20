<?php
/**
 * Title: Home – Why choose CEORI
 * Slug: ceori/home-why
 * Categories: ceori-home, ceori-pages
 * Description: Four reasons arranged around a central portrait.
 * Viewport Width: 1440
 *
 * @package ceori
 */

$ceori_features = array(
	array( 'expert', 'Expert Guidance', 'Consult qualified professionals who understand your concerns.', '/about-us/', 'Meet our specialists' ),
	array( 'personal', 'Personalised Care', 'Discover treatment options based on your individual needs.', '/contact-us/', 'Book a consultation' ),
	array( 'technology', 'Modern Technology', 'Explore advanced dermatology and aesthetic procedures.', '/cosmetic-dermatology/', 'Learn more' ),
	array( 'comprehensive', 'Comprehensive Services', 'Find skin, hair, dental and cosmetic care in one place.', '/treatments/', 'View all treatments' ),
);

/**
 * One feature column.
 *
 * @param array $f Feature data.
 */
$ceori_feature = function ( $f ) {
	ob_start();
	?>
<!-- wp:group {"className":"ceori-feature reveal","layout":{"type":"default"}} -->
<div class="wp-block-group ceori-feature reveal"><!-- wp:group {"className":"ceori-feature__head","layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group ceori-feature__head"><!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"ceori-feature__icon"} -->
<figure class="wp-block-image size-full ceori-feature__icon"><img src="<?php echo esc_url( CEORI_URI . '/assets/icons/' . $f[0] . '.svg' ); ?>" alt=""/></figure>
<!-- /wp:image -->

<!-- wp:heading {"level":3,"className":"ceori-feature__title"} -->
<h3 class="wp-block-heading ceori-feature__title"><?php echo esc_html( $f[1] ); ?></h3>
<!-- /wp:heading --></div>
<!-- /wp:group -->

<!-- wp:paragraph {"className":"ceori-feature__text"} -->
<p class="ceori-feature__text"><?php echo esc_html( $f[2] ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"ceori-feature__link"} -->
<p class="ceori-feature__link"><a href="<?php echo ceori_link( $f[3] ); // phpcs:ignore WordPress.Security.EscapeOutput ?>"><?php echo esc_html( $f[4] ); ?></a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->
	<?php
	return ob_get_clean();
};
?>
<!-- wp:group {"tagName":"section","align":"full","className":"ceori-section ceori-why","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull ceori-section ceori-why"><!-- wp:group {"align":"wide","className":"ceori-section__head is-centered","layout":{"type":"default"}} -->
<div class="wp-block-group alignwide ceori-section__head is-centered"><!-- wp:paragraph {"className":"ceori-eyebrow"} -->
<p class="ceori-eyebrow">Why choose us</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Care Designed <em>Around</em> You</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"ceori-section__lead"} -->
<p class="ceori-section__lead">Specialist advice, honest guidance and treatment plans built around your skin – from your first consultation to follow-up.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:columns {"verticalAlignment":"center","align":"wide","className":"ceori-why__cols"} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center ceori-why__cols"><!-- wp:column {"verticalAlignment":"center","className":"ceori-why__side ceori-why__side--left"} -->
<div class="wp-block-column is-vertically-aligned-center ceori-why__side ceori-why__side--left"><?php echo $ceori_feature( $ceori_features[0] ) . "\n\n" . $ceori_feature( $ceori_features[1] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","className":"ceori-why__center"} -->
<div class="wp-block-column is-vertically-aligned-center ceori-why__center"><?php echo ceori_image_block( 'why-center', 'ceori-why__img' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","className":"ceori-why__side ceori-why__side--right"} -->
<div class="wp-block-column is-vertically-aligned-center ceori-why__side ceori-why__side--right"><?php echo $ceori_feature( $ceori_features[2] ) . "\n\n" . $ceori_feature( $ceori_features[3] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></section>
<!-- /wp:group -->
