<?php
/**
 * Fallback template.
 *
 * @package DealZTheme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main id="primary" class="site-main">
	<div class="dealz-container">
		<h1><?php esc_html_e( 'DealZ', 'dealz-theme' ); ?></h1>
		<p><?php esc_html_e( 'Nền tảng du lịch đang được xây dựng.', 'dealz-theme' ); ?></p>
	</div>
</main>
<?php
get_footer();
