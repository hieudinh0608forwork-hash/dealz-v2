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
<?php
$explore_href = is_front_page() ? '#explore' : home_url( '/#explore' );
$trips_href   = is_front_page() ? '#trips' : home_url( '/#trips' );
$account_href = is_user_logged_in() ? admin_url( 'profile.php' ) : wp_login_url( home_url( '/' ) );
?>
<nav class="mobile-nav" aria-label="<?php esc_attr_e( 'Điều hướng nhanh', 'dealz-theme' ); ?>">
	<a href="<?php echo esc_url( $explore_href ); ?>"<?php echo is_front_page() ? ' aria-current="page"' : ''; ?>>
		<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1z" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linejoin="round"/></svg>
		<span><?php esc_html_e( 'Khám phá', 'dealz-theme' ); ?></span>
	</a>
	<a href="<?php echo esc_url( $trips_href ); ?>">
		<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="6" cy="18" r="2" fill="none" stroke="currentColor" stroke-width="1.75"/><circle cx="18" cy="6" r="2" fill="none" stroke="currentColor" stroke-width="1.75"/><path d="M8 18h3a3 3 0 0 0 3-3V9a3 3 0 0 1 3-3" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/></svg>
		<span><?php esc_html_e( 'Hành trình', 'dealz-theme' ); ?></span>
		<span id="mobile-trip-badge" class="mobile-nav__badge" hidden aria-label="<?php esc_attr_e( '0 điểm dừng chưa lưu trữ', 'dealz-theme' ); ?>">0</span>
	</a>
	<a href="<?php echo esc_url( $account_href ); ?>">
		<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="12" cy="8" r="3.5" fill="none" stroke="currentColor" stroke-width="1.75"/><path d="M4.5 21a7.5 7.5 0 0 1 15 0" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/></svg>
		<span><?php esc_html_e( 'Tài khoản', 'dealz-theme' ); ?></span>
	</a>
</nav>

<?php wp_footer(); ?>
</body>
</html>
