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


			<section id="itinerary-workspace" class="itinerary-workspace" hidden aria-labelledby="itinerary-workspace-title">
				<div class="workspace-toolbar">
					<a class="text-link" href="#trips" id="back-to-trips">← <?php esc_html_e( 'Quay lại hành trình', 'dealz-theme' ); ?></a>
					<span class="workspace-label"><?php esc_html_e( 'Không gian lập lịch trình', 'dealz-theme' ); ?></span>
				</div>
				<div class="workspace-heading">
					<p class="eyebrow"><?php esc_html_e( 'Chuyến đi đang chọn', 'dealz-theme' ); ?></p>
					<h2 id="itinerary-workspace-title"><?php esc_html_e( 'Đang tải chuyến đi…', 'dealz-theme' ); ?></h2>
				</div>
				<div id="workspace-feedback" class="notice" role="status" aria-live="polite" hidden></div>

				<section class="workspace-panel" aria-labelledby="itinerary-version-title">
					<div class="workspace-panel__heading">
						<div>
							<p class="eyebrow"><?php esc_html_e( 'Bản lịch trình', 'dealz-theme' ); ?></p>
							<h3 id="itinerary-version-title"><?php esc_html_e( 'Lịch trình thủ công', 'dealz-theme' ); ?></h3>
						</div>
						<button class="button button--primary button--compact" type="button" id="create-itinerary-btn"><?php esc_html_e( 'Tạo lịch trình', 'dealz-theme' ); ?></button>
					</div>
					<div id="itinerary-versions" class="version-list" aria-live="polite"></div>
				</section>

				<section id="itinerary-editor" class="workspace-panel" hidden aria-labelledby="days-title">
					<div class="workspace-panel__heading">
						<div>
							<p class="eyebrow"><?php esc_html_e( 'Sắp xếp theo ngày', 'dealz-theme' ); ?></p>
							<h3 id="days-title"><?php esc_html_e( 'Các ngày trong chuyến đi', 'dealz-theme' ); ?></h3>
						</div>
					</div>
					<form id="create-day-form" class="inline-form">
						<div class="form-field">
							<label for="itinerary-day-date"><?php esc_html_e( 'Ngày muốn thêm', 'dealz-theme' ); ?></label>
							<input id="itinerary-day-date" name="local_date" type="date" required>
						</div>
						<button class="button button--primary button--compact" type="submit"><?php esc_html_e( 'Thêm ngày', 'dealz-theme' ); ?></button>
					</form>
					<div id="itinerary-days" class="day-list" aria-live="polite"></div>
				</section>

				<section id="day-items-panel" class="workspace-panel" hidden aria-labelledby="day-items-title">
					<div class="workspace-panel__heading">
						<div>
							<p class="eyebrow"><?php esc_html_e( 'Nội dung trong ngày', 'dealz-theme' ); ?></p>
							<h3 id="day-items-title"><?php esc_html_e( 'Điểm dừng', 'dealz-theme' ); ?></h3>
						</div>
					</div>
					<div class="validation-tools">
						<button class="button button--secondary" type="button" id="validate-day-btn"><?php esc_html_e( 'Kiểm tra khung giờ', 'dealz-theme' ); ?></button>
						<div id="validation-results" class="validation-results" role="status" aria-live="polite" hidden></div>
					</div>
					<div id="day-items-list" class="itinerary-item-list" aria-live="polite"></div>
					<form id="create-item-form" class="item-form">
						<h4><?php esc_html_e( 'Thêm điểm dừng hoặc bữa ăn', 'dealz-theme' ); ?></h4>
						<div class="form-field">
							<label for="item-title"><?php esc_html_e( 'Tên địa điểm / hoạt động', 'dealz-theme' ); ?></label>
							<input id="item-title" name="title_snapshot" type="text" maxlength="190" required placeholder="<?php esc_attr_e( 'Nhập tên điểm dừng', 'dealz-theme' ); ?>">
						</div>
						<div class="form-field">
							<label for="item-type"><?php esc_html_e( 'Loại thẻ', 'dealz-theme' ); ?></label>
							<select id="item-type" name="item_type">
								<option value="stop"><?php esc_html_e( 'Điểm dừng chính', 'dealz-theme' ); ?></option>
								<option value="lunch"><?php esc_html_e( 'Bữa trưa', 'dealz-theme' ); ?></option>
								<option value="dinner"><?php esc_html_e( 'Bữa tối', 'dealz-theme' ); ?></option>
							</select>
						</div>
						<div class="form-field">
							<label for="item-address"><?php esc_html_e( 'Địa chỉ (nếu có)', 'dealz-theme' ); ?></label>
							<input id="item-address" name="address_snapshot" type="text" maxlength="255" placeholder="<?php esc_attr_e( 'Chưa có địa chỉ thì có thể bỏ trống', 'dealz-theme' ); ?>">
						</div>
						<div class="form-field">
							<label for="item-start"><?php esc_html_e( 'Giờ đến', 'dealz-theme' ); ?></label>
							<input id="item-start" name="start_local" type="time">
						</div>
						<div class="form-field">
							<label for="item-duration"><?php esc_html_e( 'Thời lượng gợi ý (phút)', 'dealz-theme' ); ?></label>
							<input id="item-duration" name="duration_minutes" type="number" min="1" max="1440" step="1" placeholder="<?php esc_attr_e( 'Có thể bổ sung sau', 'dealz-theme' ); ?>">
						</div>
						<div class="form-field form-field--wide">
							<button class="button button--primary" type="submit" id="create-item-submit"><?php esc_html_e( 'Thêm vào ngày này', 'dealz-theme' ); ?></button>
						</div>
					</form>
				</section>

				<section id="finance-panel" class="workspace-panel" aria-labelledby="finance-title">
					<div class="workspace-panel__heading">
						<div>
							<p class="eyebrow"><?php esc_html_e( 'Theo dõi chi phí', 'dealz-theme' ); ?></p>
							<h3 id="finance-title"><?php esc_html_e( 'Ngân sách và khoản chi', 'dealz-theme' ); ?></h3>
						</div>
						<span class="workspace-label"><?php esc_html_e( 'Đơn vị tiền tệ theo từng khoản', 'dealz-theme' ); ?></span>
					</div>
					<div id="finance-feedback" class="notice" role="status" aria-live="polite" hidden></div>
					<div id="budget-summary" class="budget-summary" aria-live="polite"></div>
					<div class="finance-grid">
						<section class="finance-card" aria-labelledby="budget-lines-title">
							<div class="finance-card__heading">
								<h4 id="budget-lines-title"><?php esc_html_e( 'Khoản dự kiến', 'dealz-theme' ); ?></h4>
							</div>
							<div id="budget-lines-list" class="finance-list" aria-live="polite"></div>
							<form id="create-budget-form" class="finance-form">
								<h5><?php esc_html_e( 'Thêm khoản ngân sách', 'dealz-theme' ); ?></h5>
								<div class="form-field">
									<label for="budget-category"><?php esc_html_e( 'Nhóm chi phí', 'dealz-theme' ); ?></label>
									<select id="budget-category" name="category" required>
										<option value="transport"><?php esc_html_e( 'Di chuyển', 'dealz-theme' ); ?></option>
										<option value="accommodation"><?php esc_html_e( 'Lưu trú', 'dealz-theme' ); ?></option>
										<option value="food"><?php esc_html_e( 'Ăn uống', 'dealz-theme' ); ?></option>
										<option value="rental"><?php esc_html_e( 'Thuê đồ', 'dealz-theme' ); ?></option>
										<option value="activities"><?php esc_html_e( 'Hoạt động', 'dealz-theme' ); ?></option>
										<option value="other"><?php esc_html_e( 'Khác', 'dealz-theme' ); ?></option>
									</select>
								</div>
								<div class="form-field">
									<label for="budget-description"><?php esc_html_e( 'Mô tả', 'dealz-theme' ); ?></label>
									<input id="budget-description" name="description" type="text" maxlength="255" placeholder="<?php esc_attr_e( 'Ví dụ: Thuê xe máy', 'dealz-theme' ); ?>">
								</div>
								<div class="form-field">
									<label for="budget-quantity"><?php esc_html_e( 'Số lượng', 'dealz-theme' ); ?></label>
									<input id="budget-quantity" name="quantity" type="number" min="0.01" max="99999999" step="0.01" value="1.00" required>
								</div>
								<div class="form-field">
									<label for="budget-unit-amount"><?php esc_html_e( 'Đơn giá (đồng)', 'dealz-theme' ); ?></label>
									<input id="budget-unit-amount" name="unit_amount_minor" type="number" min="0" step="1" placeholder="<?php esc_attr_e( 'Có thể để trống', 'dealz-theme' ); ?>">
								</div>
								<div class="form-field">
									<label for="budget-cost-type"><?php esc_html_e( 'Trạng thái chi phí', 'dealz-theme' ); ?></label>
									<select id="budget-cost-type" name="cost_type">
										<option value="estimate"><?php esc_html_e( 'Ước tính', 'dealz-theme' ); ?></option>
										<option value="quoted"><?php esc_html_e( 'Đã báo giá', 'dealz-theme' ); ?></option>
										<option value="confirmed"><?php esc_html_e( 'Đã xác nhận', 'dealz-theme' ); ?></option>
										<option value="actual"><?php esc_html_e( 'Thực tế', 'dealz-theme' ); ?></option>
									</select>
								</div>
								<div class="form-field form-field--wide">
									<button class="button button--primary" type="submit" id="create-budget-submit"><?php esc_html_e( 'Thêm khoản dự kiến', 'dealz-theme' ); ?></button>
								</div>
							</form>
						</section>

						<section class="finance-card" aria-labelledby="expenses-title">
							<div class="finance-card__heading">
								<h4 id="expenses-title"><?php esc_html_e( 'Khoản đã chi', 'dealz-theme' ); ?></h4>
							</div>
							<div id="expenses-list" class="finance-list" aria-live="polite"></div>
							<form id="create-expense-form" class="finance-form">
								<h5><?php esc_html_e( 'Ghi nhận khoản chi', 'dealz-theme' ); ?></h5>
								<div class="form-field">
									<label for="expense-category"><?php esc_html_e( 'Nhóm chi phí', 'dealz-theme' ); ?></label>
									<select id="expense-category" name="category" required>
										<option value="transport"><?php esc_html_e( 'Di chuyển', 'dealz-theme' ); ?></option>
										<option value="accommodation"><?php esc_html_e( 'Lưu trú', 'dealz-theme' ); ?></option>
										<option value="food"><?php esc_html_e( 'Ăn uống', 'dealz-theme' ); ?></option>
										<option value="rental"><?php esc_html_e( 'Thuê đồ', 'dealz-theme' ); ?></option>
										<option value="activities"><?php esc_html_e( 'Hoạt động', 'dealz-theme' ); ?></option>
										<option value="other"><?php esc_html_e( 'Khác', 'dealz-theme' ); ?></option>
									</select>
								</div>
								<div class="form-field">
									<label for="expense-amount"><?php esc_html_e( 'Số tiền (đồng)', 'dealz-theme' ); ?></label>
									<input id="expense-amount" name="amount_minor" type="number" min="1" step="1" required>
								</div>
								<div class="form-field">
									<label for="expense-occurred-at"><?php esc_html_e( 'Thời điểm chi', 'dealz-theme' ); ?></label>
									<input id="expense-occurred-at" name="occurred_at" type="datetime-local" required>
								</div>
								<div class="form-field">
									<label for="expense-note"><?php esc_html_e( 'Ghi chú', 'dealz-theme' ); ?></label>
									<input id="expense-note" name="note" type="text" maxlength="1000">
								</div>
								<div class="form-field form-field--wide">
									<button class="button button--primary" type="submit" id="create-expense-submit"><?php esc_html_e( 'Lưu khoản chi', 'dealz-theme' ); ?></button>
								</div>
							</form>
						</section>
					</div>
				</section>
			</section>

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
				<p><?php esc_html_e( 'Bạn có thể thêm ngày, điểm dừng, khoản dự kiến và khoản chi thực tế. Bản đồ, tuyến đường và dịch vụ đặt chỗ sẽ được bổ sung khi có nguồn dữ liệu được xác minh.', 'dealz-theme' ); ?></p>
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
