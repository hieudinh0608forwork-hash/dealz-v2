<?php
/**
 * DealZ V2 theme bootstrap.
 *
 * @package DealZTheme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register theme support.
 */
function dealz_theme_setup(): void {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
}
add_action( 'after_setup_theme', 'dealz_theme_setup' );

/**
 * Enqueue local theme assets and expose the WordPress REST nonce.
 */
function dealz_theme_enqueue_assets(): void {
	$theme_version = wp_get_theme()->get( 'Version' );

	wp_enqueue_style(
		'dealz-design-tokens',
		get_theme_file_uri( '/assets/css/tokens.css' ),
		array(),
		$theme_version
	);
	wp_enqueue_style(
		'dealz-local-fonts',
		get_theme_file_uri( '/assets/fonts/fonts.css' ),
		array( 'dealz-design-tokens' ),
		$theme_version
	);
	wp_enqueue_style(
		'dealz-theme',
		get_stylesheet_uri(),
		array( 'dealz-design-tokens', 'dealz-local-fonts' ),
		$theme_version
	);
	wp_enqueue_script(
		'dealz-shell',
		get_theme_file_uri( '/assets/js/shell.js' ),
		array(),
		$theme_version,
		true
	);

	if ( is_front_page() ) {
		wp_enqueue_script(
			'dealz-app',
			get_theme_file_uri( '/assets/js/app.js' ),
			array(),
			$theme_version,
			true
		);
		wp_enqueue_script(
			'dealz-itinerary',
			get_theme_file_uri( '/assets/js/itinerary.js' ),
			array( 'dealz-app' ),
			$theme_version,
			true
		);
		wp_localize_script(
			'dealz-app',
			'dealzApp',
			array(
				'restUrl'  => esc_url_raw( rest_url( 'dealz/v1/' ) ),
				'nonce'    => wp_create_nonce( 'wp_rest' ),
				'loggedIn' => is_user_logged_in(),
				'loginUrl' => wp_login_url( home_url( '/' ) ),
			)
		);
	}
}
add_action( 'wp_enqueue_scripts', 'dealz_theme_enqueue_assets' );

/**
 * Preload the two most important local Vietnamese font subsets.
 */
function dealz_theme_preload_fonts(): void {
	$font_files = array(
		'/assets/fonts/bricolage-grotesque-800-vietnamese.woff2',
		'/assets/fonts/be-vietnam-pro-400-vietnamese.woff2',
	);
	foreach ( $font_files as $font_file ) {
		echo '<link rel="preload" href="' . esc_url( get_theme_file_uri( $font_file ) ) . '" as="font" type="font/woff2" crossorigin>' . "\n";
	}
}
add_action( 'wp_head', 'dealz_theme_preload_fonts', 1 );

/**
 * Avoid remote avatar requests on the site and in the WordPress toolbar.
 */
add_filter( 'pre_option_show_avatars', '__return_zero' );
function dealz_disable_remote_avatar_markup( $avatar ): string {
	return '';
}
add_filter( 'get_avatar', 'dealz_disable_remote_avatar_markup', 10, 1 );

/**
 * Register private styleguide and public legal-draft routes.
 */
function dealz_register_theme_routes(): void {
	add_rewrite_rule( '^styleguide/?$', 'index.php?dealz_styleguide=1', 'top' );
	add_rewrite_rule( '^chinh-sach-coc-va-huy/?$', 'index.php?dealz_legal_draft=deposit-cancellation', 'top' );
	add_rewrite_rule( '^dieu-khoan/?$', 'index.php?dealz_legal_draft=terms', 'top' );
	add_rewrite_rule( '^bao-mat/?$', 'index.php?dealz_legal_draft=privacy', 'top' );
	if ( '2' !== get_option( 'dealz_theme_rewrite_version' ) ) {
		flush_rewrite_rules( false );
		update_option( 'dealz_theme_rewrite_version', '2', false );
	}
}
add_action( 'init', 'dealz_register_theme_routes' );

function dealz_theme_query_vars( array $vars ): array {
	$vars[] = 'dealz_styleguide';
	$vars[] = 'dealz_legal_draft';
	return $vars;
}
add_filter( 'query_vars', 'dealz_theme_query_vars' );

/**
 * Resolve theme routes when rewrite rules have not yet been flushed.
 */
function dealz_detect_theme_routes(): void {
	$request_uri  = isset( $_SERVER['REQUEST_URI'] ) ? (string) wp_unslash( $_SERVER['REQUEST_URI'] ) : '/';
	$request_path = (string) wp_parse_url( $request_uri, PHP_URL_PATH );
	$slug         = basename( trim( $request_path, '/' ) );
	if ( 'styleguide' === $slug ) {
		set_query_var( 'dealz_styleguide', '1' );
	}
	$legal_routes = array(
		'chinh-sach-coc-va-huy' => 'deposit-cancellation',
		'dieu-khoan'            => 'terms',
		'bao-mat'               => 'privacy',
	);
	if ( isset( $legal_routes[ $slug ] ) ) {
		set_query_var( 'dealz_legal_draft', $legal_routes[ $slug ] );
	}
}
add_action( 'template_redirect', 'dealz_detect_theme_routes', -1 );

/**
 * Render the admin-only, noindex styleguide.
 */
function dealz_render_styleguide_route(): void {
	if ( ! get_query_var( 'dealz_styleguide' ) ) {
		return;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		status_header( 404 );
		nocache_headers();
		get_header();
		echo '<main id="primary" class="page-container"><h1>' . esc_html__( 'Không tìm thấy trang', 'dealz-theme' ) . '</h1></main>';
		get_footer();
		exit;
	}
	nocache_headers();
	header( 'X-Robots-Tag: noindex, nofollow', true );
	add_action( 'wp_head', static function (): void {
		echo '<meta name="robots" content="noindex, nofollow">' . "\n";
	}, 1 );
	get_header();
	include get_theme_file_path( '/styleguide-content.php' );
	get_footer();
	exit;
}
add_action( 'template_redirect', 'dealz_render_styleguide_route', 0 );

/**
 * Sanitize the optional Zalo contact URL; only HTTPS URLs are accepted.
 */
function dealz_sanitize_zalo_url( $value ): string {
	$url   = esc_url_raw( is_string( $value ) ? trim( $value ) : '' );
	$parts = wp_parse_url( $url );
	return is_array( $parts ) && isset( $parts['scheme'] ) && 'https' === strtolower( $parts['scheme'] ) ? $url : '';
}

function dealz_theme_register_settings(): void {
	register_setting(
		'dealz_theme_settings',
		'dealz_zalo_url',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'dealz_sanitize_zalo_url',
			'default'           => '',
		)
	);
}
add_action( 'admin_init', 'dealz_theme_register_settings' );

function dealz_theme_add_settings_page(): void {
	add_theme_page( 'DealZ Settings', 'DealZ Settings', 'manage_options', 'dealz-settings', 'dealz_theme_render_settings_page' );
}
add_action( 'admin_menu', 'dealz_theme_add_settings_page' );

function dealz_theme_render_settings_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'DealZ Settings', 'dealz-theme' ); ?></h1>
		<form method="post" action="options.php">
			<?php settings_fields( 'dealz_theme_settings' ); ?>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="dealz_zalo_url"><?php esc_html_e( 'Zalo URL (tùy chọn)', 'dealz-theme' ); ?></label></th>
					<td>
						<input class="regular-text" type="url" id="dealz_zalo_url" name="dealz_zalo_url" value="<?php echo esc_attr( (string) get_option( 'dealz_zalo_url', '' ) ); ?>" placeholder="https://zalo.me/…">
						<p class="description"><?php esc_html_e( 'Để trống thì ẩn toàn bộ liên kết Zalo. Chỉ lưu URL HTTPS đã được chủ dự án cung cấp.', 'dealz-theme' ); ?></p>
					</td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

/**
 * Render public legal drafts. They are deliberately noindex and non-operative.
 */
function dealz_render_legal_draft_route(): void {
	$slug    = get_query_var( 'dealz_legal_draft' );
	$allowed = array( 'deposit-cancellation', 'terms', 'privacy' );
	if ( ! is_string( $slug ) || ! in_array( $slug, $allowed, true ) ) {
		return;
	}
	nocache_headers();
	header( 'X-Robots-Tag: noindex, nofollow', true );
	add_action( 'wp_head', static function (): void {
		echo '<meta name="robots" content="noindex, nofollow">' . "\n";
	}, 1 );
	get_header();
	include get_theme_file_path( '/legal-draft-content.php' );
	get_footer();
	exit;
}
add_action( 'template_redirect', 'dealz_render_legal_draft_route', 0 );
