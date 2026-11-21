<?php
/**
 * Site footer.
 *
 * @package DealZTheme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$zalo_url = dealz_sanitize_zalo_url( get_option( 'dealz_zalo_url', '' ) );
?>
<footer class="site-footer">
	<div class="site-footer__inner page-container">
		<div class="site-footer__brand">
			<a class="site-footer__wordmark" href="<?php echo esc_url( home_url( '/' ) ); ?>">DealZ</a>
			<p><?php esc_html_e( 'Đang thí điểm tại Đà Nẵng – Hội An', 'dealz-theme' ); ?></p>
		</div>
		<nav class="site-footer__legal" aria-label="<?php esc_attr_e( 'Tài liệu pháp lý', 'dealz-theme' ); ?>">
			<h2><?php esc_html_e( 'Thông tin', 'dealz-theme' ); ?></h2>
			<a href="<?php echo esc_url( home_url( '/chinh-sach-coc-va-huy/' ) ); ?>"><?php esc_html_e( 'Chính sách cọc và hủy', 'dealz-theme' ); ?></a>
			<a href="<?php echo esc_url( home_url( '/dieu-khoan/' ) ); ?>"><?php esc_html_e( 'Điều khoản', 'dealz-theme' ); ?></a>
			<a href="<?php echo esc_url( home_url( '/bao-mat/' ) ); ?>"><?php esc_html_e( 'Bảo mật', 'dealz-theme' ); ?></a>
		</nav>
		<div class="site-footer__support">
			<h2><?php esc_html_e( 'Hỗ trợ', 'dealz-theme' ); ?></h2>
			<?php if ( $zalo_url ) : ?>
				<a href="<?php echo esc_url( $zalo_url ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Liên hệ qua Zalo', 'dealz-theme' ); ?></a>
			<?php else : ?>
				<p><?php esc_html_e( 'Kênh Zalo sẽ hiển thị khi được cấu hình.', 'dealz-theme' ); ?></p>
			<?php endif; ?>
		</div>
	</div>
	<p class="site-footer__disclaimer"><?php esc_html_e( 'Sản phẩm đang trong quá trình phát triển.', 'dealz-theme' ); ?></p>
</footer>
<?php if ( $zalo_url ) : ?>
	<a class="zalo-float" href="<?php echo esc_url( $zalo_url ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'Liên hệ DealZ qua Zalo', 'dealz-theme' ); ?>"><?php esc_html_e( 'Zalo', 'dealz-theme' ); ?></a>
<?php endif; ?>
<?php wp_footer(); ?>
</body>
</html>
