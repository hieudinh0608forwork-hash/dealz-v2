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
 *
 * @return void
 */
function dealz_theme_setup(): void {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
}
add_action( 'after_setup_theme', 'dealz_theme_setup' );

/**
 * Enqueue local theme assets and expose the WordPress REST nonce.
 *
 * @return void
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
 *
 * @return void
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
 * Register the private, noindex design-system reference page.
 */
function dealz_register_styleguide_route(): void {
	add_rewrite_rule( '^styleguide/?$', 'index.php?dealz_styleguide=1', 'top' );
	if ( ! get_option( 'dealz_styleguide_rewrite_ready' ) ) {
		flush_rewrite_rules( false );
		update_option( 'dealz_styleguide_rewrite_ready', 1, false );
	}
}
add_action( 'init', 'dealz_register_styleguide_route' );

function dealz_styleguide_query_vars( array $vars ): array {
	$vars[] = 'dealz_styleguide';
	return $vars;
}
add_filter( 'query_vars', 'dealz_styleguide_query_vars' );

function dealz_render_styleguide_route(): void {
	if ( ! get_query_var( 'dealz_styleguide' ) ) {
		return;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		status_header( 404 );
		nocache_headers();
		include get_404_template();
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
