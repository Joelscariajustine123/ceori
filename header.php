<?php
/**
 * Site header.
 *
 * The header is built in Elementor (Appearance → Header Footer & Blocks → "CEORI Header").
 * If no Elementor header is published, the theme's built-in header is shown instead.
 *
 * @package ceori
 */

?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php ceori_body_capture_start(); ?>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'ceori' ); ?></a>
<div class="site-header-wrap">
	<?php
	if ( ceori_hfe_active( 'header' ) ) {
		hfe_render_header();
	} else {
		echo do_blocks( '<!-- wp:pattern {"slug":"ceori/header"} /-->' ); // phpcs:ignore WordPress.Security.EscapeOutput
	}
	?>
</div>
<main id="content" class="site-main">