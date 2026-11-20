<?php
/**
 * Title: Blog index layout
 * Slug: ceori/blog-index
 * Categories: ceori-pages
 * Inserter: no
 * Description: Blog/archive banner with search and categories, a grid of articles and pagination.
 *
 * @package ceori
 */

?>
<!-- wp:group {"tagName":"section","align":"full","className":"ceori-banner ceori-banner--blog","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull ceori-banner ceori-banner--blog"><!-- wp:group {"align":"wide","className":"ceori-banner__frame","backgroundColor":"cream","layout":{"type":"default"}} -->
<div class="wp-block-group alignwide ceori-banner__frame has-cream-background-color has-background"><!-- wp:group {"className":"ceori-banner__body","layout":{"type":"default"}} -->
<div class="wp-block-group ceori-banner__body"><!-- wp:ceori/breadcrumbs /-->

<!-- wp:query-title {"type":"archive","level":1,"showPrefix":false,"className":"ceori-banner__title ceori-archive-title"} /-->

<!-- wp:heading {"level":1,"className":"ceori-banner__title ceori-blog-title"} -->
<h1 class="wp-block-heading ceori-banner__title ceori-blog-title">Skin Care Tips &amp; <em>Expert Insights</em></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"ceori-banner__lead"} -->
<p class="ceori-banner__lead">Guides, treatment explainers and everyday advice from the specialists at CEORI Skin Clinic, Calicut.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"ceori-banner__tools","layout":{"type":"default"}} -->
<div class="wp-block-group ceori-banner__tools"><!-- wp:search {"label":"Search articles","showLabel":false,"placeholder":"Search articles…","buttonText":"Search","buttonUseIcon":true,"className":"ceori-search"} /-->

<!-- wp:categories {"showPostCounts":false,"className":"ceori-cat-pills"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","align":"full","className":"ceori-section ceori-section--tight","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull ceori-section ceori-section--tight"><!-- wp:query {"queryId":21,"query":{"perPage":9,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":true},"align":"wide","className":"ceori-posts"} -->
<div class="wp-block-query alignwide ceori-posts"><!-- wp:post-template {"className":"ceori-posts__grid","layout":{"type":"grid","columnCount":3}} -->
<!-- wp:post-featured-image {"isLink":true,"aspectRatio":"4/3","sizeSlug":"ceori-card","className":"ceori-post-card__media"} /-->

<!-- wp:post-date {"className":"ceori-post-card__date"} /-->

<!-- wp:post-title {"level":2,"isLink":true,"className":"ceori-post-card__title"} /-->

<!-- wp:post-excerpt {"moreText":"Read more","excerptLength":18,"className":"ceori-post-card__excerpt"} /-->
<!-- /wp:post-template -->

<!-- wp:query-pagination {"paginationArrow":"arrow","className":"ceori-pagination","layout":{"type":"flex","justifyContent":"center"}} -->
<!-- wp:query-pagination-previous /-->

<!-- wp:query-pagination-numbers /-->

<!-- wp:query-pagination-next /-->
<!-- /wp:query-pagination -->

<!-- wp:query-no-results -->
<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">No articles found. Try another search or browse our treatments.</p>
<!-- /wp:paragraph -->
<!-- /wp:query-no-results --></div>
<!-- /wp:query --></section>
<!-- /wp:group -->
