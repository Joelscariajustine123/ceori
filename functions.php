<?php
/**
 * CEORI Skintiva theme bootstrap.
 *
 * @package ceori
 */

defined( 'ABSPATH' ) || exit;

define( 'CEORI_VERSION', '1.0.0' );
define( 'CEORI_DIR', get_template_directory() );
define( 'CEORI_URI', get_template_directory_uri() );

require CEORI_DIR . '/inc/helpers.php';
require CEORI_DIR . '/inc/compat.php';
require CEORI_DIR . '/inc/setup.php';
require CEORI_DIR . '/inc/settings.php';
require CEORI_DIR . '/inc/content-types.php';
require CEORI_DIR . '/inc/treatments-admin.php';
require CEORI_DIR . '/inc/breadcrumbs.php';
require CEORI_DIR . '/inc/treatment-markup.php';
require CEORI_DIR . '/inc/blocks.php';
require CEORI_DIR . '/inc/forms.php';
require CEORI_DIR . '/inc/seo.php';
require CEORI_DIR . '/inc/search.php';
require CEORI_DIR . '/inc/elementor.php';
require CEORI_DIR . '/inc/home-only.php';
require CEORI_DIR . '/inc/security.php';
require CEORI_DIR . '/inc/performance.php';
