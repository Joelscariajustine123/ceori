<?php
/**
 * Title: Search results layout
 * Slug: ceori/search-results
 * Categories: ceori-pages
 * Inserter: no
 * Description: Search results for treatments, pages and articles.
 *
 * @package ceori
 */

?>
<!-- wp:group {"tagName":"section","align":"full","className":"ceori-banner","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull ceori-banner"><!-- wp:group {"align":"wide","className":"ceori-banner__frame","backgroundColor":"cream","layout":{"type":"default"}} -->
<div class="wp-block-group alignwide ceori-banner__frame has-cream-background-color has-background"><!-- wp:group {"className":"ceori-banner__body","layout":{"type":"default"}} -->
<div class="wp-block-group ceori-banner__body"><!-- wp:ceori/breadcrumbs /-->

<!-- wp:query-title {"type":"search","level":1,"className":"ceori-banner__title"} /--></div>
<!-- /wp:group -->

<!-- wp:group {"className":"ceori-banner__tools","layout":{"type":"default"}} -->
<div class="wp-block-group ceori-banner__tools"><!-- wp:search {"label":"Search the website","showLabel":false,"placeholder":"Search treatments and articles…","buttonText":"Search","buttonUseIcon":true,"className":"ceori-search"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","align":"full","className":"ceori-section ceori-section--tight","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull ceori-section ceori-section--tight"><!-- wp:query {"queryId":31,"query":{"perPage":12,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":true},"align":"wide","className":"ceori-posts"} -->
<div class="wp-block-query alignwide ceori-posts"><!-- wp:post-template {"className":"ceori-posts__grid","layout":{"type":"grid","columnCount":3}} -->
<!-- wp:post-featured-image {"isLink":true,"aspectRatio":"4/3","sizeSlug":"ceori-card","className":"ceori-post-card__media"} /-->

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
<p class="has-text-align-center">Nothing matched your search. Try a different word, or explore our treatments below.</p>
<!-- /wp:paragraph -->

<!-- wp:ceori/treatment-categories {"layout":"cards"} /-->
<!-- /wp:query-no-results --></div>
<!-- /wp:query --></section>
<!-- /wp:group -->
