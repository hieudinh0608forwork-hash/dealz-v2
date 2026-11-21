<?php
/**
 * Public application health endpoint.
 *
 * @package DealZ
 */

namespace DealZ\REST;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Exposes a deliberately minimal, non-sensitive health response.
 */
final class HealthController {

	/**
	 * Register REST routes.
	 *
	 * @return void
	 */
	public function register_routes(): void {
		register_rest_route(
			'dealz/v1',
			'/health',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_health' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * Return scaffold status without exposing runtime or secret information.
	 *
	 * @return \WP_REST_Response
	 */
	public function get_health(): \WP_REST_Response {
		return new \WP_REST_Response(
			array(
				'service'           => 'dealz-core',
				'status'            => 'scaffolded',
				'application_ready' => false,
				'message'           => __( 'DealZ Core foundation is installed; application modules are not yet enabled.', 'dealz-core' ),
			),
			200
		);
	}
}
