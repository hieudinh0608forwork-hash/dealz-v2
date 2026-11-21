<?php
/**
 * Private design-system reference page. Included only for manage_options users.
 *
 * @package DealZTheme
 */
if ( ! defined( 'ABSPATH' ) || ! current_user_can( 'manage_options' ) ) {
	exit;
}
?>
<main id="primary" class="styleguide-page page-container">
	<header class="styleguide-intro">
		<p class="eyebrow">DealZ · Design system</p>
		<h1>Trang styleguide</h1>
		<p>Thư viện thành phần và trạng thái giao diện đang được dùng trong DealZ. Trang này chỉ dành cho quản trị viên, không nằm trong menu và không được lập chỉ mục.</p>
	</header>

	<section class="styleguide-section" aria-labelledby="styleguide-tokens">
		<h2 id="styleguide-tokens">Màu và token</h2>
		<div class="styleguide-grid">
			<div class="styleguide-swatch" style="background:var(--dealz-cream)">--dealz-cream</div>
			<div class="styleguide-swatch" style="background:var(--dealz-white)">--dealz-white</div>
			<div class="styleguide-swatch" style="background:var(--dealz-ink);color:var(--dealz-white)">--dealz-ink</div>
			<div class="styleguide-swatch" style="background:var(--dealz-green);color:var(--dealz-white)">--dealz-green</div>
			<div class="styleguide-swatch" style="background:var(--dealz-mint-50)">--dealz-mint-50</div>
			<div class="styleguide-swatch" style="background:var(--dealz-mint-100)">--dealz-mint-100</div>
			<div class="styleguide-swatch" style="background:var(--dealz-lime-50)">--dealz-lime-50</div>
			<div class="styleguide-swatch" style="background:var(--dealz-orange-50)">--dealz-orange-50</div>
			<div class="styleguide-swatch" style="background:var(--dealz-lime)">--dealz-lime</div>
			<div class="styleguide-swatch" style="background:var(--dealz-primary);color:var(--dealz-white)">--dealz-primary</div>
			<div class="styleguide-swatch" style="background:var(--dealz-border)">--dealz-border</div>
			<div class="styleguide-swatch" style="background:var(--dealz-input-border)">--dealz-input-border</div>
		</div>
		<p class="styleguide-help">Nguồn màu duy nhất: assets/css/tokens.css. Ma trận tương phản và ngưỡng CI: docs/design-contrast.md.</p>
	</section>

	<section class="styleguide-section" aria-labelledby="styleguide-type">
		<h2 id="styleguide-type">Thang chữ</h2>
		<p class="eyebrow">Bricolage Grotesque · 800</p>
		<h1>Tiêu đề hiển thị</h1>
		<h2>Tiêu đề cấp hai</h2>
		<h3>Tiêu đề thẻ và nhóm</h3>
		<p>Be Vietnam Pro 400 · 17px · Ướ ặ ẫ ỡ ọ</p>
		<p class="styleguide-help">Nhãn và trợ giúp · 14px · 600</p>
		<p class="styleguide-help" style="font-variant-numeric:tabular-nums">Số tabular · 1.200.000 ₫ · 09:30</p>
	</section>

	<section class="styleguide-section" aria-labelledby="styleguide-buttons">
		<h2 id="styleguide-buttons">Nút và trạng thái</h2>
		<div class="styleguide-demo-row">
			<span class="button button--primary" data-btn="primary">Primary</span>
			<span class="button button--secondary" data-btn="secondary">Secondary</span>
			<span class="button button--tonal" data-btn="tonal">Tonal</span>
			<span class="button button--tertiary" data-btn="tertiary">Tertiary →</span>
			<span class="button button--icon" data-btn="icon" aria-label="Thêm">+</span>
		</div>
		<h3>Hover · Focus · Active · Disabled · Loading</h3>
		<div class="styleguide-demo-row">
			<span class="button button--primary is-hovered">Hover</span>
			<span class="button button--primary is-focused">Focus-visible</span>
			<span class="button button--primary is-active">Active</span>
			<button class="button button--primary" disabled>Disabled</button>
			<span class="button button--primary" aria-busy="true"><span class="button__spinner" aria-hidden="true"></span>Đang lưu…</span>
		</div>
	</section>

	<section class="styleguide-section" aria-labelledby="styleguide-inputs">
		<h2 id="styleguide-inputs">Ô nhập và trạng thái</h2>
		<div class="styleguide-demo-stack">
			<div class="styleguide-field"><label for="styleguide-default">Tên điểm dừng</label><input class="styleguide-input" id="styleguide-default" value="Ướ ặ ẫ ỡ ọ" readonly><span class="styleguide-help">Nhãn luôn hiển thị phía trên.</span></div>
			<div class="styleguide-field"><label for="styleguide-focus">Đang focus</label><input class="styleguide-input is-focused" id="styleguide-focus" value="Đà Nẵng" readonly></div>
			<div class="styleguide-field"><label for="styleguide-error">Trạng thái lỗi</label><input class="styleguide-input" id="styleguide-error" aria-invalid="true" value="Ngày không hợp lệ" readonly><span class="styleguide-error">Ngày kết thúc phải sau ngày bắt đầu.</span></div>
			<div class="styleguide-field"><label for="styleguide-disabled">Vô hiệu</label><input class="styleguide-input" id="styleguide-disabled" value="Chưa thể chỉnh sửa" disabled></div>
		</div>
	</section>

	<section class="styleguide-section" aria-labelledby="styleguide-chips">
		<h2 id="styleguide-chips">Chip</h2>
		<div class="styleguide-demo-row">
			<span class="dz-chip">Điểm dừng chính</span>
			<span class="dz-chip dz-chip--selected">Đang chọn</span>
			<span class="dz-chip dz-chip--meal">Bữa ăn</span>
			<span class="dz-chip" aria-disabled="true">Không tương tác</span>
		</div>
	</section>

	<section class="styleguide-section" aria-labelledby="styleguide-cards">
		<h2 id="styleguide-cards">Thẻ</h2>
		<div class="styleguide-demo-stack">
			<article class="dz-card"><p class="eyebrow">Điểm dừng</p><h3>Thẻ điểm dừng</h3><div class="dz-image-slot" role="img" aria-label="Khung ảnh chưa có ảnh đã xác minh"><strong>Tên điểm dừng</strong><span>Chưa có ảnh đã xác minh</span></div><p class="styleguide-help">Ảnh thật cần media đã xác minh và giấy phép.</p></article>
			<article class="dz-card dz-card--budget"><p>Dự kiến</p><strong>—</strong><p>Chưa có khoản chi được nhập.</p></article>
			<article class="dz-card dz-card--note"><p class="eyebrow">Gợi ý chung</p><h3>Thẻ ghi chú</h3><p>Thông tin mang tính tham khảo, không phải dữ liệu địa điểm đã xác minh.</p></article>
			<article class="dz-card dz-card--alert"><p class="eyebrow">Cảnh báo</p><h3>Đã đủ 4 điểm dừng chính</h3><p>Bạn vẫn có thể thêm thẻ bữa ăn.</p><span class="button button--tertiary">Thử lại →</span></article>
		</div>
	</section>

	<section class="styleguide-section" aria-labelledby="styleguide-feedback">
		<h2 id="styleguide-feedback">Toast · Skeleton · Empty state</h2>
		<div class="styleguide-demo-stack">
			<div class="dz-toast" role="status" aria-live="polite"><strong>✓</strong><span>Đã lưu thay đổi.</span></div>
			<div class="dz-toast dz-toast--error" role="alert"><strong>!</strong><span>Không thể tải dữ liệu. Hãy thử lại.</span></div>
			<div class="dz-skeleton" aria-busy="true" aria-label="Đang tải nội dung"></div>
			<div class="dz-empty-state"><div class="dz-empty-state__icon" aria-hidden="true">＋</div><h3>Chưa có điểm dừng</h3><p>Thêm điểm dừng đầu tiên để bắt đầu lịch trình.</p><span class="button button--secondary">Thêm điểm dừng</span></div>
		</div>
	</section>

	<section class="styleguide-section" aria-labelledby="styleguide-route">
		<h2 id="styleguide-route">ImageSlot và đoạn nối</h2>
		<div class="dz-image-slot" role="img" aria-label="Khung ảnh trống, chưa có ảnh được xác minh"><strong>Điểm dừng mẫu</strong><span>Chưa có ảnh đã xác minh</span></div>
		<div class="route-connector"><span class="route-connector__icon" aria-hidden="true">↗</span><span>Chưa có đủ dữ liệu tuyến đường</span></div>
	</section>
</main>
