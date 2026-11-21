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
