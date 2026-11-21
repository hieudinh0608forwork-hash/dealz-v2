<?php
/**
 * Main plugin application.
 *
 * @package DealZ
 */

namespace DealZ\Core;

use DealZ\REST\HealthController;
use DealZ\REST\FinanceController;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Boots the plugin modules.
 */
final class Plugin {

	/**
	 * Singleton instance.
	 *
	 * @var Plugin|null
	 */
	private static $instance = null;

	/**
	 * Get singleton.
	 *
	 * @return Plugin
	 */
	public static function instance(): Plugin {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function init(): void {
		add_action( 'rest_api_init', array( new HealthController(), 'register_routes' ) );
		add_action( 'rest_api_init', array( new \DealZ\REST\TripController(), 'register_routes' ) );
		add_action( 'rest_api_init', array( new \DealZ\REST\ItineraryController(), 'register_routes' ) );
		add_action( 'rest_api_init', array( new FinanceController(), 'register_routes' ) );
		add_action( 'init', array( \DealZ\Database\Schema::class, 'maybe_upgrade' ), 1 );
		add_action( 'admin_notices', array( \DealZ\Database\Schema::class, 'render_admin_notice' ) );
	}
}
