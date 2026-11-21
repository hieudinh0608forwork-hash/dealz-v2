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
		'dealz-theme',
		get_stylesheet_uri(),
		array(),
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
