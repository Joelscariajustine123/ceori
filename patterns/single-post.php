<?php
/**
 * Title: Single article layout
 * Slug: ceori/single-post
 * Categories: ceori-pages
 * Inserter: no
 * Description: Blog article with breadcrumbs, meta, featured image, content, sharing and related articles.
 *
 * @package ceori
 */

?>
<!-- wp:group {"tagName":"article","align":"full","className":"ceori-article","layout":{"type":"constrained","contentSize":"760px"}} -->
<article class="wp-block-group alignfull ceori-article"><!-- wp:group {"align":"wide","className":"ceori-article__head","layout":{"type":"constrained","contentSize":"880px"}} -->
<div class="wp-block-group alignwide ceori-article__head"><!-- wp:ceori/breadcrumbs /-->

<!-- wp:post-terms {"term":"category","className":"ceori-eyebrow"} /-->

<!-- wp:post-title {"level":1,"className":"ceori-article__title"} /-->

<!-- wp:group {"className":"ceori-article__meta","layout":{"type":"flex","flexWrap":"wrap"}} -->
<div class="wp-block-group ceori-article__meta"><!-- wp:post-author-name {"className":"ceori-article__author"} /-->

<!-- wp:post-date /--></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:post-featured-image {"align":"wide","aspectRatio":"16/9","sizeSlug":"large","className":"ceori-article__media is-lcp"} /-->

<!-- wp:post-content {"className":"ceori-article__content","layout":{"type":"constrained","contentSize":"760px"}} /-->

<!-- wp:group {"className":"ceori-article__foot","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} -->
<div class="wp-block-group ceori-article__foot"><!-- wp:ceori/share /-->

<!-- wp:paragraph {"className":"ceori-article__note"} -->
<p class="ceori-article__note">This article is for general information. Please consult a specialist for advice about your own skin.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></article>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","align":"full","className":"ceori-section ceori-related","backgroundColor":"mist","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull ceori-section ceori-related has-mist-background-color has-background"><!-- wp:group {"align":"wide","className":"ceori-section__head","layout":{"type":"default"}} -->
<div class="wp-block-group alignwide ceori-section__head"><!-- wp:paragraph {"className":"ceori-eyebrow"} -->
<p class="ceori-eyebrow">Keep reading</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Related <em>Articles</em></h2>
<!-- /wp:heading --></div>
<!-- /wp:group -->

<!-- wp:ceori/related-posts {"align":"wide"} /--></section>
<!-- /wp:group -->

<!-- wp:pattern {"slug":"ceori/cta-appointment"} /-->
