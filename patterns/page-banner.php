<?php
/**
 * Title: Page banner
 * Slug: ceori/page-banner
 * Categories: ceori-pages
 * Inserter: no
 * Description: Page heading banner with breadcrumbs, title, intro (excerpt) and optional featured image.
 *
 * @package ceori
 */

?>
<!-- wp:group {"tagName":"section","align":"full","className":"ceori-banner","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull ceori-banner"><!-- wp:group {"align":"wide","className":"ceori-banner__frame","backgroundColor":"cream","layout":{"type":"default"}} -->
<div class="wp-block-group alignwide ceori-banner__frame has-cream-background-color has-background"><!-- wp:group {"className":"ceori-banner__body","layout":{"type":"default"}} -->
<div class="wp-block-group ceori-banner__body"><!-- wp:ceori/breadcrumbs /-->

<!-- wp:post-title {"level":1,"className":"ceori-banner__title"} /-->

<!-- wp:post-excerpt {"className":"ceori-banner__lead"} /--></div>
<!-- /wp:group -->

<!-- wp:post-featured-image {"aspectRatio":"4/3","sizeSlug":"large","className":"ceori-banner__media"} /--></div>
<!-- /wp:group --></section>
<!-- /wp:group -->
