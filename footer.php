<?php
/**
 * Site footer.
 *
 * The footer is built in Elementor (Appearance → Header Footer & Blocks → "CEORI Footer").
 * If no Elementor footer is published, the theme's built-in footer is shown instead.
 *
 * @package ceori
 */

?>
</main>
<div class="site-footer-wrap">
	<?php
	if ( ceori_hfe_active( 'footer' ) ) {
		hfe_render_footer();
	} else {
		echo do_blocks( '<!-- wp:pattern {"slug":"ceori/footer"} /-->' ); // phpcs:ignore WordPress.Security.EscapeOutput
	}
	?>
</div>
<?php ceori_body_capture_end(); ?>
<?php wp_footer(); ?>
</body>
</html>
