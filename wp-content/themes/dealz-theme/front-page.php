<?php
/**
 * DealZ traveler home and trip dashboard.
 *
 * @package DealZTheme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main id="primary" class="site-main">
	<section class="hero-section" id="explore">
		<div class="hero-section__content">
			<p class="eyebrow"><?php esc_html_e( 'Bắt đầu từ Đà Nẵng – Hội An', 'dealz-theme' ); ?></p>
			<h1><?php esc_html_e( 'Lên kế hoạch cho chuyến đi của bạn.', 'dealz-theme' ); ?></h1>
			<p class="hero-section__description"><?php esc_html_e( 'Tập hợp thông tin chuyến đi và từng bước xây dựng hành trình phù hợp với lịch trình của bạn.', 'dealz-theme' ); ?></p>
			<?php if ( is_user_logged_in() ) : ?>
			<a class="button button--primary hero-section__cta" href="#create-trip"><?php esc_html_e( 'Tạo chuyến đi', 'dealz-theme' ); ?><span aria-hidden="true">↗</span></a>
		<?php else : ?>
			<a class="button button--primary hero-section__cta" href="<?php echo esc_url( wp_login_url( home_url( '/' ) ) ); ?>"><?php esc_html_e( 'Đăng nhập để bắt đầu', 'dealz-theme' ); ?><span aria-hidden="true">↗</span></a>
		<?php endif; ?>
			<p class="hero-section__note"><?php esc_html_e( 'Bạn chủ động lựa chọn và chỉnh sửa thông tin chuyến đi.', 'dealz-theme' ); ?></p>
		</div>
		<div class="hero-art" aria-hidden="true">
			<div class="hero-art__sun"></div>
			<div class="hero-art__hill hero-art__hill--back"></div>
			<div class="hero-art__hill hero-art__hill--front"></div>
			<div class="hero-art__label">Đà Nẵng <span>·</span> Hội An</div>
			<div class="hero-art__pin">D</div>
		</div>
	</section>

	<section class="trips-section page-container" id="trips">
		<div class="section-heading">
			<div>
				<p class="eyebrow"><?php esc_html_e( 'Không gian của bạn', 'dealz-theme' ); ?></p>
				<h2><?php esc_html_e( 'Hành trình của bạn', 'dealz-theme' ); ?></h2>
				<p><?php esc_html_e( 'Lưu lại thông tin cơ bản để bắt đầu chuẩn bị cho chuyến đi.', 'dealz-theme' ); ?></p>
			</div>
		</div>

		<?php if ( is_user_logged_in() ) : ?>
			<div id="trip-feedback" class="notice" role="status" aria-live="polite" hidden></div>
			<div id="trip-list" class="trip-grid" aria-live="polite">
				<div class="loading-card">
					<span class="loading-indicator" aria-hidden="true"></span>
					<p><?php esc_html_e( 'Đang tải hành trình…', 'dealz-theme' ); ?></p>
				</div>
			</div>

			<div class="create-trip-panel" id="create-trip">
				<div class="create-trip-panel__intro">
					<p class="eyebrow"><?php esc_html_e( 'Bắt đầu hành trình mới', 'dealz-theme' ); ?></p>
					<h2><?php esc_html_e( 'Tạo chuyến đi', 'dealz-theme' ); ?></h2>
					<p><?php esc_html_e( 'Nhập một vài thông tin cơ bản. Bạn có thể cập nhật lại sau.', 'dealz-theme' ); ?></p>
				</div>
				<form id="create-trip-form" class="trip-form">
					<div class="form-field form-field--wide">
						<label for="trip-title"><?php esc_html_e( 'Tên chuyến đi', 'dealz-theme' ); ?> <span aria-hidden="true">*</span></label>
						<input id="trip-title" name="title" type="text" maxlength="190" placeholder="<?php esc_attr_e( 'Ví dụ: Cuối tuần ở Đà Nẵng', 'dealz-theme' ); ?>" required>
					</div>
					<div class="form-field">
						<label for="trip-origin"><?php esc_html_e( 'Nơi xuất phát', 'dealz-theme' ); ?></label>
						<input id="trip-origin" name="origin_text" type="text" maxlength="255" placeholder="<?php esc_attr_e( 'Bạn khởi hành từ đâu?', 'dealz-theme' ); ?>">
					</div>
					<div class="form-field">
						<label for="trip-destination"><?php esc_html_e( 'Điểm đến dự kiến', 'dealz-theme' ); ?></label>
						<input id="trip-destination" name="destination_name" type="text" maxlength="190" placeholder="<?php esc_attr_e( 'Bạn muốn đi đâu?', 'dealz-theme' ); ?>">
					</div>
					<div class="form-field">
						<label for="trip-start"><?php esc_html_e( 'Ngày bắt đầu', 'dealz-theme' ); ?></label>
						<input id="trip-start" name="start_date" type="date">
					</div>
					<div class="form-field">
						<label for="trip-end"><?php esc_html_e( 'Ngày kết thúc', 'dealz-theme' ); ?></label>
						<input id="trip-end" name="end_date" type="date">
					</div>
					<div class="form-field">
						<label for="trip-travelers"><?php esc_html_e( 'Số người tham gia', 'dealz-theme' ); ?></label>
						<input id="trip-travelers" name="traveler_count" type="number" min="1" max="50" step="1" value="1">
					</div>
					<div class="form-field">
						<label for="trip-budget"><?php esc_html_e( 'Ngân sách dự kiến (đồng)', 'dealz-theme' ); ?></label>
						<input id="trip-budget" name="budget_amount_minor" type="number" min="0" step="1" placeholder="<?php esc_attr_e( 'Chưa cần nhập', 'dealz-theme' ); ?>">
					</div>
					<div class="form-field form-field--wide form-actions">
						<button class="button button--primary" type="submit" id="create-trip-submit"><?php esc_html_e( 'Lưu chuyến đi', 'dealz-theme' ); ?></button>
						<p class="form-footnote"><?php esc_html_e( 'Chuyến đi mới được lưu ở chế độ riêng tư.', 'dealz-theme' ); ?></p>
					</div>
				</form>
			</div>
		<?php else : ?>
			<div class="empty-state">
				<div class="empty-state__symbol" aria-hidden="true">✦</div>
				<h3><?php esc_html_e( 'Đăng nhập để lưu hành trình', 'dealz-theme' ); ?></h3>
				<p><?php esc_html_e( 'Các chuyến đi của bạn sẽ được lưu trong tài khoản để có thể xem và cập nhật.', 'dealz-theme' ); ?></p>
				<a class="button button--primary" href="<?php echo esc_url( wp_login_url( home_url( '/' ) ) ); ?>"><?php esc_html_e( 'Đăng nhập', 'dealz-theme' ); ?></a>
			</div>
		<?php endif; ?>
	</section>

	<section class="next-step-section page-container">
		<div class="next-step-card">
			<div>
				<p class="eyebrow"><?php esc_html_e( 'Tiếp theo', 'dealz-theme' ); ?></p>
				<h2><?php esc_html_e( 'Bắt đầu bằng những thông tin cơ bản.', 'dealz-theme' ); ?></h2>
				<p><?php esc_html_e( 'Phần chỉnh sửa lịch trình và quản lý điểm dừng sẽ được mở rộng ở các phiên bản tiếp theo.', 'dealz-theme' ); ?></p>
			</div>
			<?php if ( is_user_logged_in() ) : ?>
				<a class="text-link" href="#create-trip"><?php esc_html_e( 'Tạo chuyến đi', 'dealz-theme' ); ?> <span aria-hidden="true">→</span></a>
			<?php else : ?>
				<a class="text-link" href="<?php echo esc_url( wp_login_url( home_url( '/' ) ) ); ?>"><?php esc_html_e( 'Đăng nhập để bắt đầu', 'dealz-theme' ); ?> <span aria-hidden="true">→</span></a>
			<?php endif; ?>
		</div>
	</section>
</main>
<nav class="mobile-nav" aria-label="<?php esc_attr_e( 'Điều hướng nhanh', 'dealz-theme' ); ?>">
	<a href="#explore"><span aria-hidden="true">⌂</span><span><?php esc_html_e( 'Khám phá', 'dealz-theme' ); ?></span></a>
	<a href="#trips"><span aria-hidden="true">⌁</span><span><?php esc_html_e( 'Hành trình', 'dealz-theme' ); ?></span></a>
	<a href="<?php echo esc_url( is_user_logged_in() ? admin_url( 'profile.php' ) : wp_login_url( home_url( '/' ) ) ); ?>"><span aria-hidden="true">○</span><span><?php esc_html_e( 'Tài khoản', 'dealz-theme' ); ?></span></a>
</nav>
<?php
get_footer();
