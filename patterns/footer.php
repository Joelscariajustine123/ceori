<?php
/**
 * Title: Footer
 * Slug: ceori/footer
 * Categories: footer
 * Block Types: core/template-part/footer
 * Inserter: no
 * Description: Cream band with logo, introduction, social links, quick links, treatments and contact details, followed by a photographic band with copyright and developer credit.
 *
 * @package ceori
 */

$ceori_quick = array(
	'/'            => 'Home',
	'/about-us/'   => 'About Us',
	'/treatments/' => 'Treatments',
	'/blog/'       => 'Blog',
	'/contact-us/' => 'Contact Us',
);
$ceori_cats  = array(
	'/dermatology-clinic-in-calicut/' => 'Clinical Dermatology',
	'/cosmetic-dermatology/'          => 'Cosmetic Dermatology',
	'/aesthetic-gynaecology/'         => 'Cosmetic Gynaecology',
	'/cosmetic-dentistry/'            => 'Cosmetic Dentistry',
	'/cosmetic-surgery-in-kozhikode/' => 'Cosmetic Surgery',
);

/**
 * Serialized core/list of links.
 *
 * @param array $links Path => label.
 * @return string
 */
$ceori_link_list = function ( $links ) {
	$out = '<!-- wp:list {"className":"ceori-footer__links"} -->' . "\n" . '<ul class="wp-block-list ceori-footer__links">';
	foreach ( $links as $path => $label ) {
		$out .= '<!-- wp:list-item -->' . "\n" . '<li><a href="' . ceori_link( $path ) . '">' . esc_html( $label ) . '</a></li>' . "\n" . '<!-- /wp:list-item -->';
	}
	return $out . '</ul>' . "\n" . '<!-- /wp:list -->';
};
?>
<!-- wp:group {"tagName":"div","className":"site-footer","layout":{"type":"default"}} -->
<div class="wp-block-group site-footer"><!-- wp:group {"align":"full","className":"site-footer__main","backgroundColor":"cream","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull site-footer__main has-cream-background-color has-background"><!-- wp:group {"align":"wide","className":"site-footer__grid","layout":{"type":"default"}} -->
<div class="wp-block-group alignwide site-footer__grid"><!-- wp:group {"className":"site-footer__brand","layout":{"type":"default"}} -->
<div class="wp-block-group site-footer__brand"><!-- wp:site-logo {"width":150,"shouldSyncIcon":false} /-->

<!-- wp:ceori/clinic-info {"field":"about"} /-->

<!-- wp:ceori/clinic-info {"field":"social"} /--></div>
<!-- /wp:group -->

<!-- wp:group {"className":"site-footer__col","layout":{"type":"default"}} -->
<div class="wp-block-group site-footer__col"><!-- wp:heading {"level":2,"className":"site-footer__title"} -->
<h2 class="wp-block-heading site-footer__title">Quick Links</h2>
<!-- /wp:heading -->

<?php echo $ceori_link_list( $ceori_quick ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
<!-- /wp:group -->

<!-- wp:group {"className":"site-footer__col","layout":{"type":"default"}} -->
<div class="wp-block-group site-footer__col"><!-- wp:heading {"level":2,"className":"site-footer__title"} -->
<h2 class="wp-block-heading site-footer__title">Treatments</h2>
<!-- /wp:heading -->

<?php echo $ceori_link_list( $ceori_cats ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
<!-- /wp:group -->

<!-- wp:group {"className":"site-footer__col site-footer__col--contact","layout":{"type":"default"}} -->
<div class="wp-block-group site-footer__col site-footer__col--contact"><!-- wp:heading {"level":2,"className":"site-footer__title"} -->
<h2 class="wp-block-heading site-footer__title">Contact</h2>
<!-- /wp:heading -->

<!-- wp:ceori/clinic-info {"field":"contact"} /-->

<!-- wp:ceori/clinic-info {"field":"hours"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:group <?php echo wp_json_encode( array( 'align' => 'full', 'className' => 'site-footer__band', 'style' => ceori_bg_style( 'footer-band' ), 'layout' => array( 'type' => 'constrained' ) ), JSON_UNESCAPED_SLASHES ); ?> -->
<div class="wp-block-group alignfull site-footer__band"><!-- wp:group {"align":"wide","className":"site-footer__up-row","layout":{"type":"default"}} -->
<div class="wp-block-group alignwide site-footer__up-row"><!-- wp:paragraph {"className":"site-footer__up"} -->
<p class="site-footer__up"><a href="#top" aria-label="Back to top"><?php echo ceori_icon( 'arrow-up' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"align":"wide","className":"site-footer__bottom","layout":{"type":"default"}} -->
<div class="wp-block-group alignwide site-footer__bottom"><!-- wp:ceori/clinic-info {"field":"copyright"} /-->

<!-- wp:paragraph {"className":"site-footer__credit"} -->
<p class="site-footer__credit">Developed by <a href="https://pensare.in/" target="_blank" rel="noopener">Pensare</a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
