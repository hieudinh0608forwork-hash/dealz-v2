<?php
/**
 * Bootstrap the DealZ Core integration test suite.
 *
 * @package DealZ
 */

$_tests_dir = getenv( 'WP_TESTS_DIR' );

if ( ! $_tests_dir ) {
	$_tests_dir = '/tmp/wordpress-tests-lib';
}

require_once $_tests_dir . '/includes/functions.php';

/**
 * Load DealZ Core before the WordPress test suite initializes plugins.
 *
 * @return void
 */
function dealz_manually_load_plugin(): void {
	require dirname( __DIR__ ) . '/dealz-core.php';
}
tests_add_filter( 'muplugins_loaded', 'dealz_manually_load_plugin' );

require $_tests_dir . '/includes/bootstrap.php';
