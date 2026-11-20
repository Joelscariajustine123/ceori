<?php
/**
 * Title: Header
 * Slug: ceori/header
 * Categories: header
 * Block Types: core/template-part/header
 * Inserter: no
 * Description: Sticky header with logo, main menu and appointment button.
 *
 * @package ceori
 */

$ceori_nav = array(
	'overlayMenu' => 'mobile',
	'hasIcon'     => true,
	'icon'        => 'menu',
	'className'   => 'site-header__nav',
	'layout'      => array(
		'type'           => 'flex',
		'justifyContent' => 'right',
		'flexWrap'       => 'nowrap',
	),
);
// Use the menu created by the CEORI content import when it exists.
$ceori_nav_id = (int) get_option( 'ceori_main_navigation' );
if ( $ceori_nav_id && 'wp_navigation' === get_post_type( $ceori_nav_id ) ) {
	$ceori_nav = array( 'ref' => $ceori_nav_id ) + $ceori_nav;
}
?>
<!-- wp:group {"tagName":"div","className":"site-header","layout":{"type":"constrained"}} -->
<div class="wp-block-group site-header"><!-- wp:group {"align":"wide","className":"site-header__inner","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between"}} -->
<div class="wp-block-group alignwide site-header__inner"><!-- wp:site-logo {"width":132,"shouldSyncIcon":false,"className":"site-header__logo"} /-->

<!-- wp:navigation <?php echo wp_json_encode( $ceori_nav ); ?> /-->

<!-- wp:ceori/offcanvas /--></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
