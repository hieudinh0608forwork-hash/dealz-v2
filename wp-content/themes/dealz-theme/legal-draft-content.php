<?php
/**
 * Minimal legal draft route. It intentionally contains no operative policy terms.
 *
 * @package DealZTheme
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$slug = get_query_var( 'dealz_legal_draft' );
$pages = array(
	'deposit-cancellation' => array(
		'title'    => __( 'Chính sách cọc và hủy', 'dealz-theme' ),
		'sections' => array( __( 'Phạm vi áp dụng', 'dealz-theme' ), __( 'Quy trình đặt cọc', 'dealz-theme' ), __( 'Điều kiện thay đổi hoặc hủy', 'dealz-theme' ), __( 'Đối soát và hoàn trả', 'dealz-theme' ) ),
	),
	'terms' => array(
		'title'    => __( 'Điều khoản sử dụng', 'dealz-theme' ),
		'sections' => array( __( 'Phạm vi sử dụng', 'dealz-theme' ), __( 'Quyền và trách nhiệm của người dùng', 'dealz-theme' ), __( 'Phạm vi tính năng đang hoạt động', 'dealz-theme' ), __( 'Tiếp nhận yêu cầu', 'dealz-theme' ) ),
	),
	'privacy' => array(
		'title'    => __( 'Chính sách bảo mật', 'dealz-theme' ),
		'sections' => array( __( 'Dữ liệu có thể được xử lý', 'dealz-theme' ), __( 'Mục đích sử dụng dữ liệu', 'dealz-theme' ), __( 'Thời gian lưu trữ', 'dealz-theme' ), __( 'Quyền của người dùng', 'dealz-theme' ), __( 'Tiếp nhận yêu cầu về dữ liệu', 'dealz-theme' ) ),
	),
);
if ( ! isset( $pages[ $slug ] ) ) {
	status_header( 404 );
	return;
}
$page = $pages[ $slug ];
?>
<main id="primary" class="legal-draft-page page-container">
	<div class="workspace-toolbar"><a class="text-link" href="<?php echo esc_url( home_url( '/' ) ); ?>">← <?php esc_html_e( 'Quay lại DealZ', 'dealz-theme' ); ?></a><span class="workspace-label"><?php esc_html_e( 'Tài liệu dự thảo', 'dealz-theme' ); ?></span></div>
	<header class="legal-draft-page__intro">
		<p class="eyebrow"><?php esc_html_e( 'Bản nháp · Chưa áp dụng', 'dealz-theme' ); ?></p>
		<h1><?php echo esc_html( $page['title'] ); ?></h1>
		<div class="notice notice--warning" role="status"><?php esc_html_e( 'Bản nháp, chưa có hiệu lực. Nội dung này chưa áp dụng và không tạo ra cam kết pháp lý hoặc thương mại.', 'dealz-theme' ); ?></div>
	</header>
	<section class="legal-draft-page__sections" aria-label="<?php esc_attr_e( 'Các mục dự kiến', 'dealz-theme' ); ?>">
		<h2><?php esc_html_e( 'Các mục dự kiến', 'dealz-theme' ); ?></h2>
		<ul>
			<?php foreach ( $page['sections'] as $section ) : ?>
				<li><?php echo esc_html( $section ); ?></li>
			<?php endforeach; ?>
		</ul>
	</section>
</main>
