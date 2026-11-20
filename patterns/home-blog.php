<?php
/**
 * Title: Latest articles
 * Slug: ceori/home-blog
 * Categories: ceori-home, ceori-pages
 * Description: The three latest blog posts, updated automatically.
 * Viewport Width: 1440
 *
 * @package ceori
 */

?>
<!-- wp:group {"tagName":"section","align":"full","className":"ceori-section ceori-blog-section","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull ceori-section ceori-blog-section"><!-- wp:group {"align":"wide","className":"ceori-section__head ceori-section__head--split","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"bottom"}} -->
<div class="wp-block-group alignwide ceori-section__head ceori-section__head--split"><!-- wp:group {"layout":{"type":"default"}} -->
<div class="wp-block-group"><!-- wp:paragraph {"className":"ceori-eyebrow"} -->
<p class="ceori-eyebrow">Blog &amp; insights</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading"><em>Skin Care</em> Tips &amp; Expert Insights</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"ceori-section__lead"} -->
<p class="ceori-section__lead">Practical advice, treatment guides and answers to common questions from our specialists.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"has-arrow"} -->
<div class="wp-block-button has-arrow"><a class="wp-block-button__link wp-element-button" href="<?php echo ceori_link( '/blog/' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>">View All Articles</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group -->

<!-- wp:query {"queryId":11,"query":{"perPage":3,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"exclude","inherit":false},"align":"wide","className":"ceori-posts"} -->
<div class="wp-block-query alignwide ceori-posts"><!-- wp:post-template {"className":"ceori-posts__grid","layout":{"type":"grid","columnCount":3}} -->
<!-- wp:post-featured-image {"isLink":true,"aspectRatio":"4/3","sizeSlug":"ceori-card","className":"ceori-post-card__media"} /-->

<!-- wp:post-date {"className":"ceori-post-card__date"} /-->

<!-- wp:post-title {"level":3,"isLink":true,"className":"ceori-post-card__title"} /-->

<!-- wp:post-excerpt {"moreText":"Read more","excerptLength":18,"className":"ceori-post-card__excerpt"} /-->
<!-- /wp:post-template --></div>
<!-- /wp:query --></section>
<!-- /wp:group -->
