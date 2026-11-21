<?php
/**
 * Site header.
 *
 * @package DealZTheme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#primary"><?php esc_html_e( 'Bỏ qua đến nội dung', 'dealz-theme' ); ?></a>
<header class="site-header">
	<div class="site-header__inner">
		<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php esc_attr_e( 'DealZ – Trang chủ', 'dealz-theme' ); ?>">
			<span class="brand__mark" aria-hidden="true">D</span>
			<span>DealZ</span>
		</a>
		<nav class="desktop-nav" aria-label="<?php esc_attr_e( 'Điều hướng chính', 'dealz-theme' ); ?>">
			<a href="#explore"><?php esc_html_e( 'Khám phá', 'dealz-theme' ); ?></a>
			<a href="#trips"><?php esc_html_e( 'Hành trình', 'dealz-theme' ); ?></a>
		</nav>
		<div class="site-header__account">
			<?php if ( is_user_logged_in() ) : ?>
				<a class="account-link" href="<?php echo esc_url( admin_url( 'profile.php' ) ); ?>"><?php esc_html_e( 'Tài khoản', 'dealz-theme' ); ?></a>
			<?php else : ?>
				<a class="account-link" href="<?php echo esc_url( wp_login_url( home_url( '/' ) ) ); ?>"><?php esc_html_e( 'Đăng nhập', 'dealz-theme' ); ?></a>
			<?php endif; ?>
		</div>
	</div>
</header>
