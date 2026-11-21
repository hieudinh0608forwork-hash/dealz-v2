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
			<p class="eyebrow"><span class="eyebrow__dot" aria-hidden="true"></span><?php esc_html_e( 'Đang thí điểm tại Đà Nẵng – Hội An', 'dealz-theme' ); ?></p>
			<h1><?php esc_html_e( 'Lên kế hoạch cho chuyến đi của bạn.', 'dealz-theme' ); ?></h1>
			<p class="hero-section__description"><?php esc_html_e( 'Tập hợp thông tin chuyến đi và từng bước xây dựng hành trình phù hợp với lịch trình của bạn.', 'dealz-theme' ); ?></p>
			<a class="button button--primary hero-section__cta" href="#create-trip"><?php esc_html_e( 'Tạo chuyến đi', 'dealz-theme' ); ?><span aria-hidden="true">↗</span></a>
			<p class="hero-section__note"><?php esc_html_e( 'Bạn chủ động lựa chọn và chỉnh sửa thông tin chuyến đi.', 'dealz-theme' ); ?></p>
		</div>
		<div class="hero-art" aria-hidden="true">
			<svg class="hero-art__map" viewBox="0 0 400 500" role="presentation" focusable="false">
				<g fill="none" stroke="var(--dealz-lime-surface)" stroke-width="1.2">
					<path d="M-30 92C48 24 114 28 178 72s128 52 252-18"/>
					<path d="M-32 118C42 52 116 54 182 98s128 50 248-14"/>
					<path d="M-36 148C48 78 122 82 188 124s132 50 246-10"/>
					<path d="M-40 178C52 108 130 110 194 154s134 50 242-8"/>
					<path d="M-44 210C56 140 136 142 202 184s136 50 236-6"/>
					<path d="M-48 244C62 174 144 174 210 216s136 48 230-4"/>
					<path d="M-40 430C44 362 118 352 186 384s132 46 252-20"/>
					<path d="M-38 458C50 390 124 382 192 414s132 46 250-18"/>
				</g>
				<circle cx="302" cy="104" r="48" fill="var(--dealz-lime)"/>
				<path d="M82 356C132 306 166 332 206 274s52-92 104-82 36 92 4 134-80 58-122 48-64-4-110-18" fill="none" stroke="var(--dealz-lime)" stroke-width="3" stroke-linecap="round" stroke-dasharray="2 12"/>
				<g fill="var(--dealz-lime)" stroke="var(--dealz-green)" stroke-width="4">
					<circle cx="82" cy="356" r="10"/><circle cx="206" cy="274" r="10"/><circle cx="314" cy="192" r="10"/>
				</g>
			</svg>
			<div class="hero-art__label">Đà Nẵng <span>·</span> Hội An</div>
		</div>
	</section>

	<section class="feature-bento page-container" aria-label="<?php esc_attr_e( 'Các tính năng của DealZ', 'dealz-theme' ); ?>">
		<article class="feature-card feature-card--timeline">
			<p class="eyebrow"><?php esc_html_e( 'Lịch trình theo ngày', 'dealz-theme' ); ?></p>
			<h2><?php esc_html_e( 'Sắp xếp chuyến đi theo nhịp của bạn.', 'dealz-theme' ); ?></h2>
			<p><?php esc_html_e( 'Tạo chuyến đi, thêm ngày và sắp xếp điểm dừng hoặc bữa ăn trong từng ngày.', 'dealz-theme' ); ?></p>
			<div class="mini-timeline" aria-hidden="true"><span>Điểm dừng</span><i></i><span>Bữa ăn</span><i></i><span>Điểm dừng</span></div>
			<small><?php esc_html_e( 'Sơ đồ minh họa cấu trúc lịch trình.', 'dealz-theme' ); ?></small>
		</article>
		<article class="feature-card feature-card--budget">
			<p class="eyebrow"><?php esc_html_e( 'Ngân sách', 'dealz-theme' ); ?></p>
			<h2><?php esc_html_e( 'Theo dõi dự kiến và đã chi.', 'dealz-theme' ); ?></h2>
			<div class="budget-illustration" aria-hidden="true"><span>Dự kiến</span><i></i><span>Đã chi</span><i></i></div>
			<small><?php esc_html_e( 'Minh họa, không phải số liệu chuyến đi.', 'dealz-theme' ); ?></small>
		</article>
		<article class="feature-card feature-card--maps">
			<p class="eyebrow"><?php esc_html_e( 'Chỉ đường', 'dealz-theme' ); ?></p>
			<h2><?php esc_html_e( 'Mở Google Maps từ điểm dừng có địa chỉ.', 'dealz-theme' ); ?></h2>
			<p><?php esc_html_e( 'Liên kết chỉ đường xuất hiện khi điểm dừng có địa chỉ phù hợp.', 'dealz-theme' ); ?></p>
			<span class="feature-card__arrow" aria-hidden="true">↗</span>
		</article>
		<article class="feature-card feature-card--rental">
			<p class="eyebrow"><?php esc_html_e( 'Theo điểm dừng', 'dealz-theme' ); ?></p>
			<h2><?php esc_html_e( 'Thuê đồ theo hành trình.', 'dealz-theme' ); ?></h2>
			<span class="dz-chip"><?php esc_html_e( 'Sắp có', 'dealz-theme' ); ?></span>
			<p><?php esc_html_e( 'Danh mục thuê đồ chưa sẵn sàng. DealZ chưa hiển thị sản phẩm, giá hoặc cửa hàng.', 'dealz-theme' ); ?></p>
		</article>
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
					<h1 id="itinerary-workspace-title"><?php esc_html_e( 'Đang tải chuyến đi…', 'dealz-theme' ); ?></h1>
					<div id="itinerary-workspace-meta" class="workspace-meta" hidden></div>
				</div>
				<div id="pilot-warning" class="notice pilot-warning" role="status" hidden><?php esc_html_e( 'Điểm đến này nằm ngoài khu thí điểm Đà Nẵng – Hội An. Thông tin dịch vụ và tuyến đường có thể chưa được xác minh.', 'dealz-theme' ); ?></div>
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

				<div class="itinerary-layout">
					<aside class="itinerary-rail" aria-label="Tóm tắt hành trình">
						<section class="itinerary-rail__days" aria-labelledby="days-title">
							<h3 id="days-title">Ngày trong chuyến đi</h3>
							<div id="itinerary-days" class="day-list" aria-live="polite"></div>
						</section>
						<div id="budget-summary" class="budget-summary" aria-live="polite"></div>
						<section class="itinerary-rail__note dz-card dz-card--note"><p class="eyebrow">Gợi ý chung</p><p>Gợi ý tại đây chưa dựa trên dữ liệu điểm dừng cụ thể.</p></section>
					</aside>
					<div class="itinerary-main">
				<section id="itinerary-editor" class="workspace-panel" hidden aria-labelledby="days-title">
					<div class="workspace-panel__heading">
						<div>
							<p class="eyebrow"><?php esc_html_e( 'Sắp xếp theo ngày', 'dealz-theme' ); ?></p>
							<h3 id="add-day-title"><?php esc_html_e( 'Thêm ngày vào lịch trình', 'dealz-theme' ); ?></h3>
						</div>
					</div>
					<form id="create-day-form" class="inline-form">
						<div class="form-field">
							<label for="itinerary-day-date"><?php esc_html_e( 'Ngày muốn thêm', 'dealz-theme' ); ?></label>
							<input id="itinerary-day-date" name="local_date" type="date" required>
						</div>
						<button class="button button--primary button--compact" type="submit"><?php esc_html_e( 'Thêm ngày', 'dealz-theme' ); ?></button>
					</form>
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
					<div id="stop-anchor-list" class="stop-anchor-list" aria-label="Điểm dừng trong ngày"></div>
					<div id="day-items-list" class="itinerary-item-list" aria-live="polite"></div>
				</section>

				<section id="create-item-panel" class="workspace-panel" hidden aria-labelledby="create-item-title">
					<form id="create-item-form" class="item-form">
						<h3 id="create-item-title"><?php esc_html_e( 'Thêm điểm dừng hoặc bữa ăn', 'dealz-theme' ); ?></h3>
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
							<label for="item-end"><?php esc_html_e( 'Giờ kết thúc (nếu đã biết)', 'dealz-theme' ); ?></label>
							<input id="item-end" name="end_local" type="time">
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
					<div class="finance-grid">
						<section class="finance-card" aria-labelledby="budget-lines-title">
							<div class="finance-card__heading">
								<h4 id="budget-lines-title"><?php esc_html_e( 'Khoản dự kiến', 'dealz-theme' ); ?></h4>
							</div>
							<div id="budget-lines-list" class="finance-list" aria-live="polite"></div>
							<details class="finance-form-details"><summary>Thêm khoản dự kiến</summary><form id="create-budget-form" class="finance-form">
								
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
									<button class="button button--secondary" type="submit" id="create-budget-submit"><?php esc_html_e( 'Thêm khoản dự kiến', 'dealz-theme' ); ?></button>
								</div>
							</form></details>
						</section>

						<section class="finance-card" aria-labelledby="expenses-title">
							<div class="finance-card__heading">
								<h4 id="expenses-title"><?php esc_html_e( 'Khoản đã chi', 'dealz-theme' ); ?></h4>
							</div>
							<div id="expenses-list" class="finance-list" aria-live="polite"></div>
							<details class="finance-form-details"><summary>Ghi nhận khoản chi</summary><form id="create-expense-form" class="finance-form">
								
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
									<button class="button button--secondary" type="submit" id="create-expense-submit"><?php esc_html_e( 'Lưu khoản chi', 'dealz-theme' ); ?></button>
								</div>
							</form></details>
						</section>
					</div>
				</section>
				</div>
				</div>
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
	<section id="rental-workspace" class="rental-workspace page-container" hidden aria-labelledby="rental-workspace-title">
		<div class="workspace-toolbar">
			<a id="rental-back-link" class="text-link" href="#trips"><?php esc_html_e( '← Quay lại điểm dừng', 'dealz-theme' ); ?></a>
			<span class="workspace-label"><?php esc_html_e( 'Khám phá đồ thuê', 'dealz-theme' ); ?></span>
		</div>
		<div class="workspace-heading rental-heading">
			<span id="rental-context-chip" class="dz-chip" hidden></span>
			<p class="eyebrow"><?php esc_html_e( 'Thuê đồ theo điểm dừng', 'dealz-theme' ); ?></p>
			<h1 id="rental-workspace-title"><?php esc_html_e( 'Thuê đồ cho điểm dừng', 'dealz-theme' ); ?></h1>
			<p id="rental-stop-context"></p>
		</div>
		<div class="rental-layout">
			<div class="rental-empty-state" role="status" aria-live="polite">
				<div class="rental-empty-state__symbol" aria-hidden="true"><svg viewBox="0 0 64 64" focusable="false"><path d="M15 14a17 17 0 0 0 34 0M32 31l-18 22h36L32 31Z" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
				<h2><?php esc_html_e( 'Danh mục thuê đồ chưa sẵn sàng', 'dealz-theme' ); ?></h2>
				<p><?php esc_html_e( 'DealZ chưa có danh sách sản phẩm, phong cách, giá, tình trạng còn hàng hoặc cửa hàng được xác minh để lọc và giới thiệu. Vì vậy, mục này chưa hiển thị sản phẩm hay nhận yêu cầu thuê.', 'dealz-theme' ); ?></p>
				<p><?php esc_html_e( 'Thông tin điểm dừng được giữ trong liên kết để bạn quay lại đúng ngữ cảnh. Chưa có thao tác đặt thuê hoặc thanh toán.', 'dealz-theme' ); ?></p>
				<a class="button button--primary rental-empty-state__back" href="#trips"><?php esc_html_e( 'Quay lại điểm dừng', 'dealz-theme' ); ?></a>
			</div>
			<aside class="rental-note dz-card dz-card--note">
				<p class="eyebrow"><?php esc_html_e( 'Gợi ý chung', 'dealz-theme' ); ?></p>
				<h2><?php esc_html_e( 'Gợi ý chung khi chụp ảnh', 'dealz-theme' ); ?></h2>
				<ul><li><?php esc_html_e( 'Ưu tiên ánh sáng tự nhiên và tránh ngược sáng gắt.', 'dealz-theme' ); ?></li><li><?php esc_html_e( 'Chọn màu trang phục có độ tương phản vừa đủ với nền.', 'dealz-theme' ); ?></li><li><?php esc_html_e( 'Ưu tiên trang phục thoải mái khi di chuyển bằng xe máy.', 'dealz-theme' ); ?></li></ul>
				<p class="styleguide-help"><?php esc_html_e( 'Gợi ý chung, chưa dựa trên dữ liệu điểm dừng.', 'dealz-theme' ); ?></p>
				<?php $zalo_url = dealz_sanitize_zalo_url( get_option( 'dealz_zalo_url', '' ) ); ?>
				<?php if ( $zalo_url ) : ?><a class="button button--tertiary" href="<?php echo esc_url( $zalo_url ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Nhắn Zalo để được báo khi có shop', 'dealz-theme' ); ?> →</a><?php endif; ?>
			</aside>
		</div>
		<section class="next-step-section rental-next-step"><div class="next-step-card"><div><p class="eyebrow"><?php esc_html_e( 'Tiếp theo', 'dealz-theme' ); ?></p><h2><?php esc_html_e( 'Xem toàn bộ hành trình.', 'dealz-theme' ); ?></h2></div><a class="button button--tertiary" href="#trips"><?php esc_html_e( 'Xem toàn bộ hành trình', 'dealz-theme' ); ?> →</a></div></section>
	</section>
</main>
<?php
get_footer();
