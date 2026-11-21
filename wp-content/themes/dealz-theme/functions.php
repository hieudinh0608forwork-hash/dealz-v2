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
 * Register theme support and local assets.
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
 * Enqueue theme stylesheet.
 *
 * @return void
 */
function dealz_theme_enqueue_assets(): void {
	wp_enqueue_style(
		'dealz-theme',
		get_stylesheet_uri(),
		array(),
		wp_get_theme()->get( 'Version' )
	);
}
add_action( 'wp_enqueue_scripts', 'dealz_theme_enqueue_assets' );
