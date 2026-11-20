<?php
/**
 * Comments list and form (standard WordPress markup, so comment and review plugins can
 * restyle or replace it).
 *
 * @package ceori
 */

if ( post_password_required() ) {
	return;
}
?>
<div id="comments" class="comments-area ceori-comments">
	<?php if ( have_comments() ) : ?>
		<h2 class="comments-title">
			<?php
			$ceori_count = get_comments_number();
			/* translators: %s: number of comments. */
			echo esc_html( sprintf( _n( '%s comment', '%s comments', $ceori_count, 'ceori' ), number_format_i18n( $ceori_count ) ) );
			?>
		</h2>
		<ol class="comment-list">
			<?php
			wp_list_comments(
				array(
					'style'       => 'ol',
					'short_ping'  => true,
					'avatar_size' => 48,
				)
			);
			?>
		</ol>
		<?php the_comments_navigation(); ?>
	<?php endif; ?>

	<?php
	if ( ! comments_open() && get_comments_number() ) {
		echo '<p class="no-comments">' . esc_html__( 'Comments are closed.', 'ceori' ) . '</p>';
	}
	comment_form();
	?>
</div>
