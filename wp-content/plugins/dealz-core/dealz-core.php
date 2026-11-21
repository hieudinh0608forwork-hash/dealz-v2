<?php
/**
 * Plugin Name: DealZ Core
 * Description: Core application foundation for DealZ V2.
 * Version: 0.1.0
 * Requires at least: 6.5
 * Requires PHP: 8.1
 * Text Domain: dealz-core
 *
 * @package DealZ
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'DEALZ_CORE_VERSION', '0.1.0' );
define( 'DEALZ_CORE_FILE', __FILE__ );
define( 'DEALZ_CORE_PATH', plugin_dir_path( __FILE__ ) );
define( 'DEALZ_CORE_URL', plugin_dir_url( __FILE__ ) );

spl_autoload_register(
	static function ( $class ) {
		$prefix = 'DealZ\\';
		$base_dir = DEALZ_CORE_PATH . 'src/';

		if ( 0 !== strpos( $class, $prefix ) ) {
			return;
		}

		$relative_class = substr( $class, strlen( $prefix ) );
		$file = $base_dir . str_replace( '\\', DIRECTORY_SEPARATOR, $relative_class ) . '.php';

		if ( is_readable( $file ) ) {
			require $file;
		}
	}
);

register_activation_hook(
	__FILE__,
	static function () {
		\DealZ\Core\Capabilities::activate();
		update_option( 'dealz_core_version', DEALZ_CORE_VERSION, false );
	}
);

add_action(
	'plugins_loaded',
	static function () {
		\DealZ\Core\Plugin::instance()->init();
	}
);
